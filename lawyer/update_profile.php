<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Sirf lawyer access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'lawyer') {
    header("location: ../login.php");
    exit();
}

$lawyer_id = $_SESSION['lawyer_id'] ?? 0;

// Fetch lawyer data
$stmt = $conn->prepare("SELECT * FROM lawyers WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
$lawyer = $result->fetch_assoc();
$stmt->close();

if (!$lawyer) {
    die("No profile found.");
}

$message = "";

// Update process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name']);
    $email     = trim($_POST['email']);
    $specialty = trim($_POST['specialty']);
    $location  = trim($_POST['location']);
    $profile_image = $lawyer['profile_image'];

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['profile_image']['tmp_name'];
        $file_name = $_FILES['profile_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = uniqid('lawyer_', true) . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_file_name;
            if (move_uploaded_file($file_tmp_path, $upload_path)) {
                $profile_image = $new_file_name;
            } else {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Invalid image type.";
        }
    }

    // Update query
    $update_stmt = $conn->prepare("UPDATE lawyers SET name=?, email=?, specialty=?, location=?, profile_image=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $name, $email, $specialty, $location, $profile_image, $lawyer_id);
    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
        $lawyer['name'] = $name;
        $lawyer['email'] = $email;
        $lawyer['specialty'] = $specialty;
        $lawyer['location'] = $location;
        $lawyer['profile_image'] = $profile_image;
    } else {
        $message = "Update failed.";
    }
    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; display: flex; min-height: 100vh;}
        .sidebar {width: 250px; background-color: #2D3748; color: #E2E8F0; padding: 20px 0; position: fixed; height: 100%; top: 0; left: 0; display: flex; flex-direction: column; align-items: center;}
        .profile-section {text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); width: 100%;}
        .profile-circle {width: 100px; height: 100px; border-radius: 50%; background-color: #4A5568; display: flex; justify-content: center; align-items: center; margin: 0 auto 10px; overflow: hidden; border: 2px solid #63B3ED;}
        .profile-circle img {width: 100%; height: 100%; object-fit: cover;}
        .profile-name {font-size: 1.2em; font-weight: bold; color: #E2E8F0; margin-bottom: 5px;}
        .profile-status {color: #48BB78; font-size: 0.9em;}
        .menu-category {width: 100%; padding: 0 20px; margin-bottom: 20px;}
        .menu-category-title {font-size: 0.8em; color: #A0AEC0; text-transform: uppercase; margin-bottom: 15px; padding-left: 5px;}
        .sidebar nav ul {list-style: none; padding: 0; width: 100%;}
        .sidebar nav ul li {margin-bottom: 5px;}
        .sidebar nav ul li a {color: #E2E8F0; text-decoration: none; display: flex; align-items: center; padding: 12px 15px; border-radius: 6px; transition: 0.3s; font-size: 1em;}
        .sidebar nav ul li a:hover {background-color: #4A5568; color: #FFFFFF;}
        .sidebar nav ul li.active a {background-color: #4299E1; color: #FFFFFF;}
        .sidebar nav ul li a i {margin-right: 15px; font-size: 1.1em;}
        .main-content {margin-left: 250px; flex-grow: 1; padding: 40px; display: flex; justify-content: center; align-items: center;}
        .container {width: 100%; max-width: 600px; padding: 40px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);}
        h1 {text-align: center; margin-bottom: 25px; color: #2c3e50; font-size: 28px; border-bottom: 2px solid #ddd; padding-bottom: 15px;}
        .form-group {margin-bottom: 20px;}
        label {display: block; margin-bottom: 8px; font-weight: bold; color: #333;}
        input[type="text"], input[type="email"], input[type="file"] {width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background-color: #fafafa; box-sizing: border-box; font-size: 16px;}
        button {width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #2c3e50; color: #fff; font-size: 18px; font-weight: bold; cursor: pointer;}
        button:hover {background-color: #1a252f;}
        .message {text-align: center; padding: 12px; border-radius: 8px; margin-bottom: 20px;}
        .info {background: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32;}
        .profile-preview {text-align: center; margin-bottom: 15px;}
        .profile-preview img {width: 120px; border-radius: 8px;}
        .back-link {display: inline-block; margin-top: 20px; color: #2c3e50; text-decoration: none; font-weight: bold;}
        .back-link:hover {color: #1a252f;}
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
        <div class="container">
            <h1>Update Profile</h1>

            <?php if ($message): ?>
                <div class="message info">
                    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($lawyer['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($lawyer['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Specialty:</label>
                    <input type="text" name="specialty" value="<?= htmlspecialchars($lawyer['specialty']) ?>">
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($lawyer['location']) ?>">
                </div>
                <div class="form-group">
                    <label>Profile Image:</label>
                    <div class="profile-preview">
                        <img src="../uploads/<?= htmlspecialchars($lawyer['profile_image'] ?: 'default.png') ?>" alt="Profile">
                    </div>
                    <input type="file" name="profile_image" accept="image/*">
                </div>
                <button type="submit">Update Profile</button>
            </form>

            <!-- Centered Back Link -->
            <div style="text-align:center; margin-top:20px;">
                <a href="manage_profile.php" class="back-link">← Back to Manage Profile</a>
            </div>
        </div>
    </div>
</body>
</html>
