<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Check if lawyer is logged in
if (!isset($_SESSION['lawyer_id'])) {
    die("Lawyer ID not found in session. Please login again.");
}

$lawyer_id = $_SESSION['lawyer_id'];

// Debugging line (check lawyer_id)
if (!$lawyer_id) {
    die("No valid lawyer_id found. Make sure your lawyer record exists in 'lawyers' table.");
}

// Fetch lawyer info
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

// Fetch appointments
$sql = "SELECT id, appointment_date, appointment_time, status,
               full_name, email_address, phone_number,
               legal_issue, legal_matter
        FROM appointments
        WHERE lawyer_id = ?
        ORDER BY appointment_date DESC, appointment_time DESC";
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
  <title>Appointments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
          <li><a href="lawyer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="manage_profile.php"><i class="fas fa-user-edit"></i> Manage Profile</a></li>
          <li class="active"><a href="get_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
          <li><a href="lawyer_client_list.php"><i class="fas fa-users"></i> My Clients</a></li>
        </ul>
      </nav>
    </div>
  </div>
  <div class="main-content">
    <div class="dashboard-header">
      <h1>Appointments</h1>
      <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="card shadow p-4">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th><th>Client Name</th><th>Email</th><th>Phone</th>
            <th>Date</th><th>Time</th><th>Issue</th><th>Matter</th>
            <th>Status</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['full_name']); ?></td>
                <td><?= htmlspecialchars($row['email_address']); ?></td>
                <td><?= htmlspecialchars($row['phone_number']); ?></td>
                <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                <td><?= htmlspecialchars($row['appointment_time']); ?></td>
                <td><?= htmlspecialchars($row['legal_issue']); ?></td>
                <td><?= htmlspecialchars($row['legal_matter']); ?></td>
                <td>
                  <span class="badge 
                    <?php 
                      if ($row['status'] == 'pending') echo 'bg-warning';
                      elseif ($row['status'] == 'confirmed') echo 'bg-success';
                      elseif ($row['status'] == 'rejected') echo 'bg-danger';
                      else echo 'bg-secondary';
                    ?>">
                    <?= htmlspecialchars($row['status']); ?>
                  </span>
                </td>
                <td>
                  <form method="post" action="update_appointment_status.php" class="d-flex flex-wrap gap-1">
                    <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="status" value="confirmed" class="btn btn-success btn-sm">Accept</button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                    <button type="submit" name="status" value="pending" class="btn btn-warning btn-sm">Pending</button>

                    <?php if ($row['status'] == 'confirmed'): ?>
                     <a href="../chat.php?appointment_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Chat</a>

                    <?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="10" class="text-center">No appointments found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
