<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../includes/db.php';

// fetch all records
$result = $conn->query("SELECT * FROM about_us");

// Current page set for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About Us</title>
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
        .main-content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: var(--header-bg-color); }
        .add-btn { background-color: var(--accent-color); color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; background-color: #1e7e51; }
        .add-btn:hover {background-color: #155d3d; }
        .add-btn i { margin-right: 8px; }

        .dashboard-card { background-color: var(--card-bg-color); padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        h3 { margin-top: 0; color: var(--header-bg-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; margin-bottom: 20px; }

        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; height: 70px; }
        table thead th { background-color: #f2f2f2; color: var(--text-color); font-weight: bold; text-transform: uppercase; }
        table tbody tr:hover { background-color: #fafafa; }
        table img { max-height: 60px; border-radius: 6px; }

        /* Action Buttons */
        .action-links { display: flex; gap: 10px; }
        .action-links a, .view-btn { 
            text-decoration: none; 
            padding: 12px 18px; 
            border-radius: 6px; 
            color: white; 
            font-weight: bold; 
            font-size: 14px;
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            height: 45px; 
            line-height: 20px; 
        }
        .action-links a i, .view-btn i { margin-right: 6px; font-size: 15px; }
        .action-links .edit-btn { background-color: var(--edit-color); }
        .action-links .edit-btn:hover { background-color: #2980b9; }
        .action-links .delete-btn { background-color: var(--delete-color); }
        .action-links .delete-btn:hover { background-color: #c0392b; }
        .view-btn { background-color: var(--primary-color); }
        .view-btn:hover { background-color: #2980b9; }

        /* Modal styles */
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); padding-top: 60px; backdrop-filter: blur(5px); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 30px; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; animation: fadeIn 0.5s; }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px; }
        .close-btn:hover, .close-btn:focus { color: #000; text-decoration: none; cursor: pointer; }
        .modal-body { line-height: 1.6; margin-top: 20px; }
        .modal-image-container { text-align: center; margin-top: 20px; }
        .modal-image-container img { max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #ddd; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
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
            <h1>Manage About Us</h1>
            <a href="add_aboutus.php" class="add-btn"><i class="fa-solid fa-plus"></i> Add New</a>
        </div>

        <div class="dashboard-card">
            <h3>About Us Sections</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Section</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Actions</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['section_name']); ?></td>
                                <td><?= htmlspecialchars($row['title']); ?></td>
                                <td><?= substr($row['description'], 0, 80) ?>...</td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="../images/<?= $row['image']; ?>" width="100" class="img-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-links">
                                    <a href="edit_aboutus.php?id=<?= $row['id']; ?>" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="delete_aboutus.php?id=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash-can"></i> Delete</a>
                                </td>
                                <td>
                                    <a href="#" class="view-btn" 
                                       data-id="<?= $row['id']; ?>" 
                                       data-title="<?= htmlspecialchars($row['title']); ?>" 
                                       data-section="<?= htmlspecialchars($row['section_name']); ?>" 
                                       data-description="<?= htmlspecialchars($row['description']); ?>" 
                                       data-image="../images/<?= htmlspecialchars($row['image']); ?>">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 id="modalTitle"></h2>
            <div class="modal-body">
                <p><strong>Section:</strong> <span id="modalSection"></span></p>
                <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                <div class="modal-image-container">
                    <img id="modalImage" src="" alt="About Us Image">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('viewModal');
            const closeBtn = document.querySelector('.close-btn');
            const viewBtns = document.querySelectorAll('.view-btn');

            viewBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const title = btn.getAttribute('data-title');
                    const section = btn.getAttribute('data-section');
                    const description = btn.getAttribute('data-description');
                    const image = btn.getAttribute('data-image');

                    document.getElementById('modalTitle').textContent = title;
                    document.getElementById('modalSection').textContent = section;
                    document.getElementById('modalDescription').textContent = description;
                    document.getElementById('modalImage').src = image;

                    modal.style.display = 'block';
                });
            });

            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
