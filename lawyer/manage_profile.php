<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// sirf lawyer access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'lawyer') {
    header("location: ../login.php");
    exit();
}

$lawyer_id = $_SESSION['lawyer_id'] ?? 0;

// fetch lawyer data
$stmt = $conn->prepare("SELECT * FROM lawyers WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
$lawyer = $result->fetch_assoc();
$stmt->close();

if (!$lawyer) {
    echo "<p>No profile found. Please contact admin.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {font-family: Arial, sans-serif;background: #f7f7f7;margin: 0;display: flex;min-height: 100vh;}
        .sidebar {width: 250px;background-color: #2D3748;color: #E2E8F0;padding: 20px 0;position: fixed;height: 100%;top: 0;left: 0;display: flex;flex-direction: column;align-items: center;}
        .profile-section {text-align: center;margin-bottom: 30px;padding-bottom: 20px;border-bottom: 1px solid rgba(255, 255, 255, 0.1);width: 100%;}
        .profile-circle {width: 100px;height: 100px;border-radius: 50%;background-color: #4A5568;display: flex;justify-content: center;align-items: center;margin: 0 auto 10px;overflow: hidden;border: 2px solid #63B3ED;}
        .profile-circle img {width: 100%;height: 100%;object-fit: cover;}
        .profile-name {font-size: 1.2em;font-weight: bold;color: #E2E8F0;margin-bottom: 5px;}
        .profile-status {color: #48BB78;font-size: 0.9em;}
        .menu-category {width: 100%;padding: 0 20px;margin-bottom: 20px;}
        .menu-category-title {font-size: 0.8em;color: #A0AEC0;text-transform: uppercase;margin-bottom: 15px;padding-left: 5px;letter-spacing: 0.05em;}
        .sidebar nav ul {list-style: none;padding: 0;width: 100%;}
        .sidebar nav ul li {margin-bottom: 5px;}
        .sidebar nav ul li a {color: #E2E8F0;text-decoration: none;display: flex;align-items: center;padding: 12px 15px;border-radius: 6px;transition: background-color 0.3s ease, color 0.3s ease;font-size: 1.0em;}
        .sidebar nav ul li a:hover {background-color: #4A5568;color: #FFFFFF;}
        .sidebar nav ul li.active a {background-color: #4299E1;color: #FFFFFF;}
        .sidebar nav ul li a i {margin-right: 15px;font-size: 1.1em;}
        .main-content {margin-left: 250px;flex-grow: 1;padding: 30px;background-color: #f7f7f7;}
        .dashboard-header {display: flex;justify-content: space-between;align-items: center;margin-bottom: 20px;}
        .dashboard-header h1 {color: #2c3e50;margin: 0;font-size: 1.8em;}
        .logout-btn {background-color: #e74c3c;color: white;padding: 10px 15px;border: none;border-radius: 6px;text-decoration: none;font-weight: bold;transition: background-color 0.3s ease;display: flex;align-items: center;gap: 8px;}
        .logout-btn:hover {background-color: #c0392b;}

        /* Table Style like get_appointments.php */
        table {width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow: 0 2px 8px rgba(0,0,0,0.05);}
        thead {background:#2c3e50;color:#fff;}
        th, td {padding:12px 15px;border-bottom:1px solid #ddd;text-align:left;}
        tbody tr:hover {background:#f9f9f9;}
        .actions a {padding:6px 12px;border-radius:5px;text-decoration:none;color:#fff;margin-right:5px;font-size:0.9em;display:inline-block;}
        .btn-edit {background:#3498db;}
        .btn-edit:hover {background:#2980b9;}
        .btn-delete {background:#e74c3c;}
        .btn-delete:hover {background:#c0392b;}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-circle">
                <img src="<?= !empty($lawyer['profile_image']) ? "../uploads/" . htmlspecialchars($lawyer['profile_image']) : "../assets/default.png" ?>" alt="Profile">
            </div>
            <div class="profile-name"><?= htmlspecialchars($lawyer['name'] ?? 'Lawyer') ?></div>
            <div class="profile-status">Online</div>
        </div>
        <div class="menu-category">
            <div class="menu-category-title">GENERAL</div>
            <nav>
                <ul>
                    <li><a href="lawyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="manage_profile.php"><i class="fas fa-user-edit"></i> Manage Profile</a></li>
                    <li><a href="get_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="lawyer_client_list.php"><i class="fas fa-users"></i> My Clients</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Manage Profile</h1>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialty</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($lawyer['id']) ?></td>
                    <td><?= htmlspecialchars($lawyer['name']) ?></td>
                    <td><?= htmlspecialchars($lawyer['email']) ?></td>
                    <td><?= htmlspecialchars($lawyer['specialty']) ?></td>
                    <td><?= htmlspecialchars($lawyer['location']) ?></td>
                    <td class="actions">
                        <a href="update_profile.php?id=<?= $lawyer['id'] ?>" class="btn-edit">Edit</a>
                        <a href="delete_profile.php?id=<?= $lawyer['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete your profile?');">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
