<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section_name = $_POST['section_name'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Upload image
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $target = "../images/" . $image;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $error = "Error uploading image.";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO about_us (section_name, title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $section_name, $title, $description, $image);

        if ($stmt->execute()) {
            $success = "About Us section added successfully!";
        } else {
            $error = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add About Us</title>
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
            max-width: 650px;
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
        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--form-bg-color);
            color: var(--text-color);
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea { resize: vertical; min-height: 100px; }
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
        <h1>Add About Us Section</h1>

        <?php if ($error): ?>
            <div class="message error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="add_aboutus.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="section_name">Section Name</label>
                <select id="section_name" name="section_name" required>
                    <option value="hero">Hero</option>
                    <option value="mission">Our Mission</option>
                    <option value="who_we_are">Who We Are</option>
                    <option value="why_choose_us">Why Choose Us</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit">Save Section</button>
        </form>

        <a href="manage_aboutus.php" class="back-link">Go back to Manage About Us</a>
    </div>
</body>
</html>
