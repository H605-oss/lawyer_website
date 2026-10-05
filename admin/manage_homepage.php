<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
include "../includes/db.php";

// Fetch homepage sections from 'homepage_sections' table
$sql = "SELECT * FROM homepage_sections ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Homepage</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --bg-color: #f0f2f5;
      --card-bg-color: #ffffff;
      --text-color: #333333;
      --header-bg-color: #2c3e50;
      --sidebar-color: #1a202c; 
      --primary-color: #3498db;
      --accent-color: #20c997;
      --delete-color: #e74c3c;
      --edit-color: #8e44ad;
    }
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      background-color: var(--bg-color);
      display: flex;
      height: 100vh;
      color: var(--text-color);
    }
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
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2px solid var(--primary-color);
      object-fit: cover;
      margin-bottom: 10px;
    }
    .sidebar-header .user-name {
      font-size: 18px;
      font-weight: bold;
      color: #ecf0f1;
      margin: 5px 0 0;
    }
    .sidebar-header .user-status {
      font-size: 14px;
      color: var(--accent-color);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .sidebar-header .user-status::before {
      content: '';
      width: 8px;
      height: 8px;
      background-color: var(--accent-color);
      border-radius: 50%;
      margin-right: 5px;
    }
    .sidebar-menu-title {
      font-size: 14px;
      font-weight: bold;
      color: #bdc3c7;
      text-transform: uppercase;
      margin-bottom: 15px;
      padding-left: 15px;
    }
    .sidebar-menu {
      list-style-type: none;
      padding: 0;
      margin: 0;
      flex-grow: 1;
    }
    .sidebar-menu li {
      margin-bottom: 10px;
    }
    .sidebar-menu a {
      color: #ecf0f1;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      font-size: 16px;
    }
    .sidebar-menu a:hover {
      background-color: #34495e;
    }
    .sidebar-menu a.active {
      background-color: var(--primary-color);
    }
    .sidebar-menu a i {
      margin-right: 10px;
    }
    .main-content {
      flex-grow: 1;
      padding: 30px;
      overflow-y: auto;
    }
    .main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .main-header h1 {
      font-size: 24px;
      font-weight: 500;
      color: #333;
      margin: 0;
    }
    .main-header .add-btn {
      background-color: var(--accent-color);
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background-color 0.3s ease;
    }
    .main-header .add-btn:hover {
      background-color: #1a9c72;
    }
    .content-card {
      background-color: var(--card-bg-color);
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    .content-card h2 {
      font-size: 20px;
      font-weight: 500;
      margin-top: 0;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--primary-color);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table thead {
      background-color: #f8f9fa;
    }
    table th, table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
      vertical-align: middle;
    }
    table th {
      font-weight: 600;
      color: #333;
    }
    table tbody tr:hover {
      background-color: #f1f1f1;
    }
    table img {
      width: 80px;
      height: auto;
      border-radius: 6px;
      object-fit: cover;
    }
    .action-buttons {
      display: flex;
      gap: 8px;
    }
    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
      color: white;
      transition: background-color 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .edit-btn {
      background-color: #3498db;
    }
    .delete-btn {
      background-color: #e74c3c;
    }
    .edit-btn:hover {
      background-color: #2980b9;
    }
    .delete-btn:hover {
      background-color: #c0392b;
    }
    .text-center {
      text-align: center;
      color: #999;
    }
    .logout-btn-bottom {
      background-color: var(--delete-color);
      color: #fff !important;
      font-weight: bold;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: auto;
      justify-content: center;
    }
    .logout-btn-bottom:hover {
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
      <li><a href="admin_pannel.php" class="<?php echo ($currentPage == 'admin_pannel.php') ? 'active' : ''; ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
      <li><a href="manage_lawyers.php" class="<?php echo ($currentPage == 'manage_lawyers.php') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Manage Lawyers</a></li>
      <li><a href="manage_appointments.php" class="<?php echo ($currentPage == 'manage_appointments.php') ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> Manage Appointments</a></li>
      <li><a href="manage_aboutus.php" class="<?php echo ($currentPage == 'manage_aboutus.php') ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> Manage About Us</a></li>
      <li><a href="manage_homepage.php" class="<?php echo ($currentPage == 'manage_homepage.php') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Manage Home</a></li>
      <li><a href="manage_service.php" class="<?php echo ($currentPage == 'manage_service.php') ? 'active' : ''; ?>"><i class="fa-solid fa-cogs"></i> Manage Service</a></li>
    </ul>
    <a href="../logout.php" class="logout-btn-bottom"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <div class="main-content">
    <div class="main-header">
      <h1>Manage Homepage Sections</h1>
      <a href="add_section.php" class="add-btn"><i class="fa-solid fa-plus"></i> Add New Section</a>
    </div>
    
    <div class="content-card">
      <h2>Homepage Sections List</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Section</th>
            <th>Title</th>
            <th>Description</th>
            <th>Image</th>
            <th>Button Text</th>
            <th>Button Link</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
<?php
if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['section_name']); ?></td>
    <td><?php echo htmlspecialchars($row['heading']); ?></td>
    <td><?php echo htmlspecialchars(substr($row['content'], 0, 50)); ?>...</td>
    <td>
        <?php if (!empty($row['image'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Section Image">
        <?php else: ?>
            N/A
        <?php endif; ?>
    </td>
    <td><?php echo htmlspecialchars($row['button_text']); ?></td>
    <td><?php echo htmlspecialchars($row['button_link']); ?></td>
    <td>
      <div class="action-buttons">
        <a href="edit_section.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">
          <i class="fa-solid fa-pen-to-square"></i> Edit
        </a>
        <a href="delete_section.php?id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this section?');">
          <i class="fa-solid fa-trash-can"></i> Delete
        </a>
      </div>
    </td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
    <td colspan="8" class="text-center">No sections found</td>
</tr>
<?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
