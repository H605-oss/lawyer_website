<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
include '../includes/db.php';

// Fetch appointments with user + lawyer info
$sql = "SELECT a.id, u.username AS user_name, l.name AS lawyer_name, 
               CONCAT(a.appointment_date, ' ', a.appointment_time) AS appointment_datetime, 
               a.created_at
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN lawyers l ON a.lawyer_id = l.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$resultAppointments = $conn->query($sql);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg-color: #f4f4f4;
            --card-bg-color: #ffffff;
            --text-color: #333333;
            --header-bg-color: #2c3e50;
            --sidebar-color: #1a202c; 
            --primary-color: #3498db;
            --accent-color: #20c997;
            --delete-color: #e74c3c;
            --edit-color: #3498db;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: var(--bg-color);
            display: flex;
            height: 100vh;
            color: var(--text-color);
        }

        /* Sidebar */
         .sidebar {
            width: 250px;
            background-color: var(--sidebar-color);
            color: #ecf0f1;
            padding: 25px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: width 0.3s ease;
        }
        .sidebar-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }
        .sidebar-header .profile-pic {
            width: 80px; height: 80px; border-radius: 50%;
            border: 2px solid var(--primary-color);
            object-fit: cover; margin-bottom: 10px;
        }
        .sidebar-header .user-name {
            font-size: 18px; font-weight: bold; color: #ecf0f1; margin: 5px 0 0;
        }
        .sidebar-header .user-status {
            font-size: 14px; color: var(--accent-color);
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-header .user-status::before {
            content: ''; width: 8px; height: 8px;
            background-color: var(--accent-color);
            border-radius: 50%; margin-right: 5px;
        }
        .sidebar-menu-title {
            font-size: 14px; font-weight: bold; color: #bdc3c7;
            text-transform: uppercase; margin-bottom: 15px; padding-left: 15px;
        }
        .sidebar-menu { list-style-type: none; padding: 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu a {
            color: #ecf0f1; text-decoration: none;
            display: flex; align-items: center;
            padding: 12px 15px; border-radius: 8px;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .sidebar-menu a:hover { background-color: #34495e; }
        .sidebar-menu a.active { background-color: var(--primary-color); }
        .sidebar-menu a i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        .sidebar .logout-btn {
            background-color: var(--delete-color);
            color: #fff; padding: 12px; text-align: center;
            border-radius: 8px; text-decoration: none; font-weight: bold;
            transition: background-color 0.3s ease; margin-top: auto;
        }
        .sidebar .logout-btn:hover { background-color: #c0392b; }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }
        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px;
        }
        .header h1 { margin: 0; color: var(--header-bg-color); }

        /* Card Style */
        .dashboard-card { 
            background-color: var(--card-bg-color); 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            margin-bottom: 25px; 
        }

        h3 { 
            margin-top: 0; 
            color: var(--header-bg-color); 
            border-bottom: 2px solid var(--primary-color); 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }

        /* Table Styles */
        .appointments-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .appointments-table th, .appointments-table td { 
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        .appointments-table thead th { 
            background-color: #f2f2f2; 
            color: var(--text-color); 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .appointments-table tbody tr:hover { 
            background-color: #fafafa; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/admin.jpeg" alt="Profile Picture" class="profile-pic">

            <p class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p class="user-status">Online</p>
        </div>
        <p class="sidebar-menu-title">General</p>
        <ul class="sidebar-menu">
              <li><a href="admin_pannel.php" class="<?php echo ($currentPage == 'admin_pannel.php') ? 'active' : ''; ?>"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_lawyers.php" class="<?php echo ($currentPage == 'manage_lawyers.php') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> <span>Manage Lawyers</span></a></li>
            <li><a href="manage_appointments.php" class="<?php echo ($currentPage == 'manage_appointments.php') ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> <span>Manage Appointments</span></a></li>
            <li><a href="manage_aboutus.php" class="<?php echo ($currentPage == 'manage_aboutus.php') ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> <span>Manage Aboutus</span></a></li>
            <li><a href="manage_homepage.php" class="<?php echo ($currentPage == 'manage_aboutus.php') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> <span>Manage Home</span></a></li>
            <li><a href="manage_service.php" class="<?php echo ($currentPage == 'manage_aboutus.php') ? 'active' : ''; ?>"><i class="fa-solid fa-cogs"></i> <span>Manage Service</span></a></li>
        </ul>
        <a href="../logout.php" class="logout-btn"> <i class="fa-solid fa-right-from-bracket"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Appointments</h1>
        </div>

        <div class="dashboard-card">
            <h3>Appointment List</h3>
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Lawyer</th>
                        <th>Appointment Date & Time</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultAppointments && $resultAppointments->num_rows > 0): ?>
                        <?php while($row = $resultAppointments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['lawyer_name']); ?></td>
                                <td><?php echo $row['appointment_datetime']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No appointments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
