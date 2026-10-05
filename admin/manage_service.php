<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

// DB config
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lawyer_booking';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Fetch all services
$res = $conn->query("SELECT * FROM services ORDER BY id DESC");
$services = [];
if ($res) {
    while ($r = $res->fetch_assoc()) $services[] = $r;
    $res->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Services</title>
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
        .sidebar-header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #34495e; margin-bottom: 20px; }
        .sidebar-header .profile-pic { width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--primary-color); object-fit: cover; margin-bottom: 10px; }
        .sidebar-header .user-name { font-size: 18px; font-weight: bold; color: #ecf0f1; margin: 5px 0 0; }
        .sidebar-header .user-status { font-size: 14px; color: var(--accent-color); display: flex; align-items: center; justify-content: center; }
        .sidebar-header .user-status::before { content: ''; width: 8px; height: 8px; background-color: var(--accent-color); border-radius: 50%; margin-right: 5px; }
        .sidebar-menu-title { font-size: 14px; font-weight: bold; color: #bdc3c7; text-transform: uppercase; margin-bottom: 15px; padding-left: 15px; }
        .sidebar-menu { list-style-type: none; padding: 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu a { color: #ecf0f1; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 8px; transition: background-color 0.3s ease; font-size: 16px; }
        .sidebar-menu a:hover { background-color: #34495e; }
        .sidebar-menu a.active { background-color: var(--primary-color); }
        .sidebar-menu a i { margin-right: 10px; }
        .sidebar .logout-btn { background-color: var(--delete-color); color: #fff; padding: 12px; text-align: center; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background-color 0.3s ease; margin-top: auto; }
        .sidebar .logout-btn:hover { background-color: #c0392b; }

        /* Main Content */
        .main-content { 
            flex-grow: 1; 
            padding: 40px; 
            overflow-y: auto; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px; 
        }
        .header h1 { 
            margin: 0; 
            color: var(--header-bg-color); 
        }
        .add-service-btn {
            background-color: var(--accent-color);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            background-color: #1e7e51;
            align-items: center;
        }
        .add-service-btn i {
            margin-right: 8px;
        }
        .add-service-btn:hover {
            background-color: #155d3d;
        }

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

        /* Table */
        .service-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .service-table th, .service-table td { 
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        .service-table thead th { 
            background-color: #f2f2f2; 
            color: var(--text-color); 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .service-table tbody tr:hover { 
            background-color: #fafafa; 
        }

        /* Action Buttons */
        .action-links {
            display: flex;
            gap: 10px;
        }
        .action-links a { 
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        .action-links a i {
            margin-right: 5px;
        }

        .action-links .edit-btn {
            background-color: var(--edit-color);
        }
        .action-links .edit-btn:hover {
            background-color: #2980b9;
        }
        .action-links .delete-btn {
            background-color: var(--delete-color);
        }
        .action-links .delete-btn:hover {
            background-color: #c0392b;
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
            <li><a href="manage_homepage.php" class="<?php echo ($currentPage == 'manage_homepage.php') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> <span>Manage Home</span></a></li>
            <li><a href="manage_service.php" class="<?php echo ($currentPage == 'manage_service.php') ? 'active' : ''; ?>"><i class="fa-solid fa-cogs"></i> <span>Manage Service</span></a></li>
        </ul>
        <a href="../logout.php" class="logout-btn"> <i class="fa-solid fa-right-from-bracket"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manage Services</h1>
            <a href="add_service.php" class="add-service-btn">
                <i class="fa-solid fa-plus"></i> Add New Service
            </a>
        </div>

        <div class="dashboard-card">
            <h3>Service List</h3>
            <?php if (empty($services)): ?>
                <p>No services found. Please add one.</p>
            <?php else: ?>
                <table class="service-table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Title</th>
                      <th>Subtitle</th>
                      <th>Description</th>
                      <th>Expertise Tags</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($services as $row): ?>
                      <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['subtitle']); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['description'])); ?></td>
                        <td><?= htmlspecialchars($row['expertise']); ?></td>
                        <td class="action-links">
                          <a href="edit_service.php?id=<?= $row['id']; ?>" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                          <a href="delete_service.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this service?')" class="delete-btn"><i class="fa-solid fa-trash-can"></i> Delete</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
