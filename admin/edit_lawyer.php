<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../includes/db.php';

$error = '';
$success = '';

// Check if an ID is provided
if (!isset($_GET['id'])) {
    die("Error: Lawyer ID not provided.");
}

$lawyer_id = $_GET['id'];

// Handle update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $location = $_POST['location'];
    $bio = $_POST['bio'];
    $new_image = $_FILES['profile_image']['name'] ?? '';

    // Get current image
    $sql = "SELECT profile_image FROM lawyers WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lawyer_id);
    $stmt->execute();
    $stmt->bind_result($current_image);
    $stmt->fetch();
    $stmt->close();

    // Image handling
    if (!empty($new_image)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($new_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check valid image type
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed)) {
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file);

            // Delete old image if it exists
            if (!empty($current_image) && file_exists("../uploads/" . $current_image)) {
                unlink("../uploads/" . $current_image);
            }

            $update_sql = "UPDATE lawyers SET name=?, specialty=?, location=?, bio=?, profile_image=? WHERE id=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssssi", $name, $specialty, $location, $bio, $new_image, $lawyer_id);
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        // No new image
        $update_sql = "UPDATE lawyers SET name=?, specialty=?, location=?, bio=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $name, $specialty, $location, $bio, $lawyer_id);
    }

    // Execute update
    if (empty($error) && $stmt->execute()) {
        $success = "Lawyer updated successfully!";
        header("Refresh: 1; url=manage_lawyers.php");
    } elseif (empty($error)) {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch data to prefill form
$sql = "SELECT * FROM lawyers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
$lawyer = $result->fetch_assoc();

if (!$lawyer) {
    die("Lawyer not found.");
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Lawyer</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --bg-color: #f4f4f4;
        --card-bg-color: #ffffff;
        --text-color: #333333;
        --form-bg-color: #fafafa;
        --border-color: #dddddd;
        --button-color: #2c3e50;
        --button-hover-color: #1a252f;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--bg-color);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .container {
        width: 100%;
        max-width: 600px;
        padding: 40px;
        background-color: var(--card-bg-color);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        margin-bottom: 25px;
        color: var(--button-color);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: var(--text-color);
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: var(--form-bg-color);
        color: var(--text-color);
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input:focus, textarea:focus {
        border-color: var(--button-color);
        outline: none;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    button {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 8px;
        background-color: var(--button-color);
        color: white;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: var(--button-hover-color);
    }

    .message {
        text-align: center;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .error {
        background-color: #fce4e4;
        color: #c62828;
        border: 1px solid #c62828;
    }

    .success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #2e7d32;
    }

    .profile-preview {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-preview img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #ddd;
        object-fit: cover;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: var(--button-color);
        text-decoration: none;
        font-weight: bold;
    }

    .back-link:hover {
        color: var(--button-hover-color);
    }
</style>
</head>
<body>
<div class="container">
    <h1>Edit Lawyer Profile</h1>

    <?php if ($error): ?>
        <div class="message error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="profile-preview">
        <img src="../uploads/<?= htmlspecialchars($lawyer['profile_image'] ?: 'default-user.png') ?>" alt="Profile Image">
    </div>

    <form action="edit_lawyer.php?id=<?= htmlspecialchars($lawyer_id) ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile_image">Change Profile Image (optional):</label>
            <input type="file" name="profile_image" accept="image/*">
        </div>

        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($lawyer['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Specialty:</label>
            <input type="text" name="specialty" value="<?= htmlspecialchars($lawyer['specialty']) ?>" required>
        </div>

        <div class="form-group">
            <label>Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($lawyer['location']) ?>" required>
        </div>

        <div class="form-group">
            <label>Bio:</label>
            <textarea name="bio"><?= htmlspecialchars($lawyer['bio']) ?></textarea>
        </div>

        <button type="submit">Update Lawyer</button>
    </form>

    <a href="manage_lawyers.php" class="back-link">Go back to Manage Lawyers</a>
</div>
</body>
</html>
