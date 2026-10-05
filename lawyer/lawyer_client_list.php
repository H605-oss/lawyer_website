<?php
session_start();
require_once("../includes/db.php");

if (!isset($_SESSION['lawyer_id']) || $_SESSION['user_type'] !== 'lawyer') {
    header("location: ../login.php");
    exit();
}

$lawyer_id = $_SESSION['lawyer_id'];

// Lawyer Info
$lawyer_name = "Lawyer";
$lawyer_image = "../assets/default.png";

$stmt = $conn->prepare("SELECT name, profile_image FROM lawyers WHERE id = ?");
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result_lawyer = $stmt->get_result();
if ($lawyer = $result_lawyer->fetch_assoc()) {
    $lawyer_name = $lawyer['name'] ?? "Lawyer";
    if (!empty($lawyer['profile_image'])) {
        $lawyer_image = "../uploads/" . $lawyer['profile_image'];
    }
}
$stmt->close();

// ✅ Fix query: use appointment_date + appointment_time
$sql = "SELECT DISTINCT u.username, u.email, 
               CONCAT(a.appointment_date, ' ', a.appointment_time) AS last_appointment
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.lawyer_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Clients</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {font-family: Arial, sans-serif;background: #f7f7f7;margin: 0;display: flex;min-height: 100vh;}
    .sidebar {width: 250px;background-color: #2D3748;color: #E2E8F0;padding: 20px 0;position: fixed;height: 100%;top: 0;left: 0;display: flex;flex-direction: column;align-items: center;}
    .profile-section {text-align: center;margin-bottom: 20px;padding-bottom: 20px;border-bottom: 1px solid rgba(255, 255, 255, 0.1);width: 100%;}
    .profile-circle {width: 100px;height: 100px;border-radius: 50%;background-color: #4A5568;display: flex;justify-content: center;align-items: center;margin: 0 auto 10px;overflow: hidden;border: 2px solid #63B3ED;}
    .profile-circle img {width: 100%;height: 100%;object-fit: cover;}
    .profile-name {font-size: 1.2em;font-weight: bold;color: #E2E8F0;margin-bottom: 5px;}
    .profile-status {color: #48BB78;font-size: 0.9em;}
    .menu-category {width: 100%;padding: 0 20px;margin-top: 10px;}
    .menu-category-title {font-size: 0.8em;color: #A0AEC0;text-transform: uppercase;margin-bottom: 10px;padding-left: 5px;letter-spacing: 0.05em;}
    .sidebar nav ul {list-style: none;padding: 0;margin: 0;width: 100%;}
    .sidebar nav ul li {margin-bottom: 5px;}
    .sidebar nav ul li a {color: #E2E8F0;text-decoration: none;display: flex;align-items: center;padding: 12px 15px;border-radius: 6px;transition: background-color 0.3s ease, color 0.3s ease;font-size: 1.0em;}
    .sidebar nav ul li a:hover {background-color: #4A5568;color: #FFFFFF;}
    .sidebar nav ul li.active a {background-color: #4299E1;color: #FFFFFF;}
    .sidebar nav ul li a i {margin-right: 12px;font-size: 1.1em;min-width: 20px;text-align: center;}
    .main-content {margin-left: 250px;flex-grow: 1;padding: 30px;background-color: #f7f7f7;}
    .dashboard-header {display: flex;justify-content: space-between;align-items: center;margin-bottom: 20px;}
    .dashboard-header h1 {color: #2c3e50;margin: 0;font-size: 1.8em;}
    .logout-btn {background-color: #e74c3c;color: white;padding: 10px 15px;border: none;border-radius: 6px;text-decoration: none;font-weight: bold;transition: background-color 0.3s ease;display: flex;align-items: center;gap: 8px;}
    .logout-btn:hover {background-color: #c0392b;}
  </style>
</head>
<body>
  <!-- Sidebar -->
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
          <li><a href="lawyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="manage_profile.php"><i class="fas fa-user-edit"></i> Manage Profile</a></li>
          <li><a href="get_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
          <li class="active"><a href="lawyer_client_list.php"><i class="fas fa-users"></i> My Clients</a></li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="dashboard-header">
      <h1>My Clients</h1>
      <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="card shadow p-4">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Client Name</th>
            <th>Email</th>
            <th>Last Appointment</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['last_appointment']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-center">No clients found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
