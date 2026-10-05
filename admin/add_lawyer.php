<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../includes/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_plain = $_POST['password'];
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
    $specialty = trim($_POST['specialty']);
    $location = trim($_POST['location']);
    $bio = trim($_POST['bio']);
    $profile_image_path = null;

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['profile_image']['tmp_name'];
        $file_name = $_FILES['profile_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = uniqid('lawyer_', true) . '.' . $file_ext;
            // Corrected upload path to use 'uploads' directory
            $upload_path = '../uploads/' . $new_file_name;

            if (move_uploaded_file($file_tmp_path, $upload_path)) {
                $profile_image_path = $new_file_name;
            } else {
                $error = "Error uploading the image. Please try again.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Insert into DB (users + lawyers table)
    if (empty($error)) {
        $conn->begin_transaction();
        try {
            // 1) Insert into users table
            $sql_users = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'lawyer')";
            $stmt1 = $conn->prepare($sql_users);
            $stmt1->bind_param("sss", $name, $email, $password_hashed);
            $stmt1->execute();

            // 2) Insert into lawyers table
            $sql_lawyers = "INSERT INTO lawyers (name, profile_image, email, password, specialty, location, bio) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $conn->prepare($sql_lawyers);
            $stmt2->bind_param("sssssss", $name, $profile_image_path, $email, $password_hashed, $specialty, $location, $bio);
            $stmt2->execute();

            $conn->commit();

            header("location: manage_lawyers.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lawyer</title>
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
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
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
            font-size: 28px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 15px;
        }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-color);
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="file"], textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--form-bg-color);
            color: var(--text-color);
            box-sizing: border-box;
            font-size: 16px;
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
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background-color: var(--button-hover-color); }
        .message {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error { background-color: #fce4e4; color: #c62828; border: 1px solid #c62828; }
        .success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--button-color);
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover { color: var(--button-hover-color); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Lawyer</h1>
        
        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form action="add_lawyer.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="specialty">Specialty:</label>
                <input type="text" id="specialty" name="specialty" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio"></textarea>
            </div>
            <div class="form-group">
                <label for="profile_image">Profile Picture:</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            <button type="submit">Add Lawyer</button>
        </form>
        <a href="manage_lawyers.php" class="back-link">Go back to Manage Lawyers</a>
    </div>
</body>
</html>