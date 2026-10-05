<?php
session_start();
require_once("../includes/db.php");

// Check if user is logged in and is a lawyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'lawyer') {
    header("location: ../login.php");
    exit();
}

// Fetch lawyer_id
$lawyer_id = null;
if (isset($_SESSION['lawyer_id'])) {
    $lawyer_id = $_SESSION['lawyer_id'];
} else if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $lawyer_stmt = $conn->prepare("SELECT id FROM lawyers WHERE email = ?");
    $lawyer_stmt->bind_param("s", $email);
    $lawyer_stmt->execute();
    $lawyer_result = $lawyer_stmt->get_result();
    if ($lawyer_row = $lawyer_result->fetch_assoc()) {
        $lawyer_id = $lawyer_row['id'];
        $_SESSION['lawyer_id'] = $lawyer_id;
    }
    $lawyer_stmt->close();
}

// Error message
$error_message = null;
if (!$lawyer_id) {
    $error_message = "Your lawyer account could not be found. Please contact support.";
}

// Lawyer Info
$lawyer_name = "Lawyer";
$lawyer_image = "../assets/default.png";
if ($lawyer_id) {
    $stmt = $conn->prepare("SELECT name, profile_image FROM lawyers WHERE id = ?");
    $stmt->bind_param("i", $lawyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($lawyer = $result->fetch_assoc()) {
        $lawyer_name = $lawyer['name'] ?? "Lawyer";
        if (!empty($lawyer['profile_image'])) {
            // Corrected file path
            $lawyer_image = "../uploads/" . $lawyer['profile_image'];
        }
    }
    $stmt->close();
}
// Dashboard Stats
$max_appointments_capacity = 50;
$max_cases_capacity = 100;

$pending_appointments = 0;
$rejected_appointments = 0;
$total_cases = 0;

if ($lawyer_id) {
    // Pending
    $stmt_pending = $conn->prepare("SELECT COUNT(*) AS pending_count 
        FROM appointments 
        WHERE lawyer_id = ? AND status = 'pending'");
    $stmt_pending->bind_param("i", $lawyer_id);
    $stmt_pending->execute();
    $data_pending = $stmt_pending->get_result()->fetch_assoc();
    $pending_appointments = $data_pending['pending_count'];
    $stmt_pending->close();

    // Rejected
    $stmt_rejected = $conn->prepare("SELECT COUNT(*) AS rejected_count 
        FROM appointments 
        WHERE lawyer_id = ? AND status = 'rejected'");
    $stmt_rejected->bind_param("i", $lawyer_id);
    $stmt_rejected->execute();
    $data_rejected = $stmt_rejected->get_result()->fetch_assoc();
    $rejected_appointments = $data_rejected['rejected_count'];
    $stmt_rejected->close();

    // Total Cases (confirmed)
    $stmt_total = $conn->prepare("SELECT COUNT(*) AS total_count 
        FROM appointments 
        WHERE lawyer_id = ? AND status = 'confirmed'");
    $stmt_total->bind_param("i", $lawyer_id);
    $stmt_total->execute();
    $data_total = $stmt_total->get_result()->fetch_assoc();
    $total_cases = $data_total['total_count'];
    $stmt_total->close();
}

$conn->close();

// Percentages
$pending_percent = $max_appointments_capacity > 0 ? round(($pending_appointments / $max_appointments_capacity) * 100) : 0;
$rejected_percent = $max_appointments_capacity > 0 ? round(($rejected_appointments / $max_appointments_capacity) * 100) : 0;
$cases_percent = $max_cases_capacity > 0 ? round(($total_cases / $max_cases_capacity) * 100) : 0;

// Function to generate chart style with custom color
function getChartStyle($percent, $color) {
    $bg_color = "#E2E8F0";
    return "background: conic-gradient({$color} {$percent}%, {$bg_color} {$percent}%);";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {font-family: Arial, sans-serif;background: #f7f7f7;margin: 0;display: flex;min-height: 100vh;}
        .sidebar {width: 250px;background-color: #2D3748;color: #E2E8F0;padding: 20px 0;position: fixed;height: 100%;top: 0;left: 0;display: flex;flex-direction: column;align-items: center;}
        .profile-section {text-align: center;margin-bottom: 20px;padding-bottom: 20px;border-bottom: 1px solid rgba(255, 255, 255, 0.1);width: 100%;}
        .profile-circle {width: 100px;height: 100px;border-radius: 50%;overflow: hidden;margin: 0 auto 10px;border: 2px solid #63B3ED;}
        .profile-circle img {width: 100%;height: 100%;object-fit: cover;}
        .profile-name {font-size: 1.2em;font-weight: bold;color: #E2E8F0;margin-bottom: 5px;}
        .profile-status {color: #48BB78;font-size: 0.9em;}
        .menu-category {width: 100%;padding: 0 20px;margin-top: 10px;}
        .menu-category-title {font-size: 0.8em;color: #A0AEC0;text-transform: uppercase;margin-bottom: 10px;padding-left: 5px;}
        .sidebar nav ul {list-style: none;padding: 0;margin: 0;width: 100%;}
        .sidebar nav ul li {margin-bottom: 5px;}
        .sidebar nav ul li a {color: #E2E8F0;text-decoration: none;display: flex;align-items: center;padding: 12px 15px;border-radius: 6px;font-size: 1.0em;}
        .sidebar nav ul li.active a {background-color: #4299E1;color: #FFFFFF;}
        .sidebar nav ul li a i {margin-right: 12px;font-size: 1.1em;min-width: 20px;text-align: center;}
        .main-content {margin-left: 250px;flex-grow: 1;padding: 30px;background-color: #f7f7f7;}
        .dashboard-header {display: flex;justify-content: space-between;align-items: center;margin-bottom: 20px;}
        .dashboard-header h1 {color: #2c3e50;margin: 0;font-size: 1.8em;}
        .logout-btn {background-color: #e74c3c;color: white;padding: 10px 15px;border-radius: 6px;text-decoration: none;font-weight: bold;}
        .logout-btn:hover {background-color: #c0392b;}
        .dashboard-cards {display: grid;grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));gap: 20px;margin-bottom: 30px;}
        .card {background-color: #fff;padding: 25px;border-radius: 12px;box-shadow: 0 4px 12px rgba(0,0,0,0.08);text-align: center;transition: 0.3s;}
        .card:hover {transform: translateY(-5px);}
        .card .icon {font-size: 2.5em;margin-bottom: 10px;}
        .card .value {font-size: 2em;font-weight: bold;color: #2c3e50;}
        .card .title {font-size: 1em;color: #718096;text-transform: uppercase;letter-spacing: 1px;}
        .charts-section {display: grid;grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 20px;}
        .chart-card {background-color: #fff;padding: 25px;border-radius: 12px;box-shadow: 0 4px 12px rgba(0,0,0,0.08);text-align: center;}
        .chart-title {font-size: 1.2em;color: #2c3e50;margin-bottom: 20px;}
        .chart-circle {width: 150px;height: 150px;border-radius: 50%;display: flex;align-items: center;justify-content: center;margin: 0 auto 15px;position: relative;}
        .chart-circle::after {content: '';width: 120px;height: 120px;background: white;border-radius: 50%;position: absolute;}
        .chart-percent {position: relative;z-index: 1;font-size: 1.5em;font-weight: bold;color: #2c3e50;}
        .chart-info {font-size: 0.9em;color: #718096;}
        .alert {padding: 15px;border-radius: 8px;margin-bottom: 20px;text-align: center;}
        .alert-danger {background-color: #f8d7da;color: #721c24;border: 1px solid #f5c6cb;}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-circle">
                <img src="<?= htmlspecialchars($lawyer_image) ?>" alt="Profile Picture">
            </div>
            <div class="profile-name"><?= htmlspecialchars($lawyer_name) ?></div>
            <div class="profile-status">Online</div>
        </div>
        <div class="menu-category">
            <div class="menu-category-title">GENERAL</div>
            <nav>
                <ul>
                    <li class="active"><a href="lawyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="manage_profile.php"><i class="fas fa-user-edit"></i> Manage Profile</a></li>
                    <li><a href="get_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="lawyer_client_list.php"><i class="fas fa-users"></i> My Clients</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1>Lawyer Dashboard</h1>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-cards">
            <div class="card">
                <div class="icon" style="color: #f1c40f;"><i class="fas fa-calendar-alt"></i></div>
                <div class="value"><?= $pending_appointments ?></div>
                <div class="title">Pending Appointments</div>
            </div>
            <div class="card">
                <div class="icon" style="color: #e74c3c;"><i class="fas fa-calendar-times"></i></div>
                <div class="value"><?= $rejected_appointments ?></div>
                <div class="title">Rejected Appointments</div>
            </div>
            <div class="card">
                <div class="icon" style="color: #2ecc71;"><i class="fas fa-gavel"></i></div>
                <div class="value"><?= $total_cases ?></div>
                <div class="title">Total Cases</div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-title">Pending Appointments Overview</div>
                <div class="chart-circle" style="<?= getChartStyle($pending_percent, '#f1c40f') ?>">
                    <span class="chart-percent"><?= $pending_appointments ?></span>
                </div>
                <div class="chart-info">
                    Pending appointments: <?= $pending_appointments ?> (out of <?= $max_appointments_capacity ?> max capacity)
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Rejected Appointments Overview</div>
                <div class="chart-circle" style="<?= getChartStyle($rejected_percent, '#e74c3c') ?>">
                    <span class="chart-percent"><?= $rejected_appointments ?></span>
                </div>
                <div class="chart-info">
                    Rejected appointments: <?= $rejected_appointments ?> (out of <?= $max_appointments_capacity ?> max capacity)
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Total Cases Overview</div>
                <div class="chart-circle" style="<?= getChartStyle($cases_percent, '#2ecc71') ?>">
                    <span class="chart-percent"><?= $total_cases ?></span>
                </div>
                <div class="chart-info">
                    Total cases: <?= $total_cases ?> (out of <?= $max_cases_capacity ?> max capacity)
                </div>
            </div>
        </div>
    </div>
</body>
</html>