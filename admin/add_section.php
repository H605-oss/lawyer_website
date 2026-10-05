<?php
session_start();

// ✅ Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include "../includes/db.php";

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $section_name = $_POST['section_name'];
    $heading = $_POST['heading'] ?? '';
    $content = $_POST['description'] ?? '';
    $button_text = $_POST['button_text'] ?? '';
    $button_link = $_POST['button_link'] ?? '';
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        $image_path = 'images/' . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $message = 'Error uploading image.';
            $message_type = 'error';
        }
    }

    // ✅ Check if section already exists (update instead of duplicate)
    $check = $conn->prepare("SELECT id FROM homepage_sections WHERE section_name = ? LIMIT 1");
    $check->bind_param("s", $section_name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // ✅ Update existing section
        if ($image_path != '') {
            $sql = "UPDATE homepage_sections 
                    SET heading=?, content=?, image=?, button_text=?, button_link=? 
                    WHERE section_name=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $heading, $content, $image_path, $button_text, $button_link, $section_name);
        } else {
            $sql = "UPDATE homepage_sections 
                    SET heading=?, content=?, button_text=?, button_link=? 
                    WHERE section_name=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $heading, $content, $button_text, $button_link, $section_name);
        }
    } else {
        // ✅ Insert new section
        $sql = "INSERT INTO homepage_sections 
                (section_name, heading, content, image, button_text, button_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $section_name, $heading, $content, $image_path, $button_text, $button_link);
    }

    if ($stmt->execute()) {
        $message = 'Section saved successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error: ' . $stmt->error;
        $message_type = 'error';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Section</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg-color: #f0f2f5;
            --card-bg-color: #ffffff;
            --text-color: #333333;
            --primary-color: #3498db;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--text-color);
        }
        .form-card {
            background-color: var(--card-bg-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 60px;
            width: 100%;
            max-width: 600px;
        }
        .form-card h2 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            height: 120px;
        }
        .form-group input[type="file"] {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .submit-btn {
            width: 100%;
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: #1a252f;
        }
        .message-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Add / Update Homepage Section</h2>
        <?php if ($message): ?>
            <div class="message-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="add_section.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="section_name">Section Name</label>
                <select id="section_name" name="section_name" required>
                    <option value="hero">Hero</option>
                    <option value="about">About Us</option>
                    <option value="testimonial_1">Testimonial 1</option>
                    <option value="testimonial_2">Testimonial 2</option>
                    <option value="testimonial_3">Testimonial 3</option>
                    <option value="section1">Section 1</option>
                    <option value="section2">Section 2</option>
                    <option value="section3">Section 3</option>
                    <option value="cta">Call to Action</option>
                </select>
            </div>
            <div class="form-group">
                <label for="heading">Heading</label>
                <input type="text" id="heading" name="heading" placeholder="Enter heading"
                       value="<?php echo isset($_POST['heading']) ? $_POST['heading'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="description">Content</label>
                <textarea id="description" name="description" placeholder="Enter description"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <label for="button_text">Button Text</label>
                <input type="text" id="button_text" name="button_text" placeholder="e.g., Learn More"
                       value="<?php echo isset($_POST['button_text']) ? $_POST['button_text'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="button_link">Button Link</label>
                <input type="text" id="button_link" name="button_link" placeholder="e.g., about.php"
                       value="<?php echo isset($_POST['button_link']) ? $_POST['button_link'] : ''; ?>">
            </div>
            <button type="submit" class="submit-btn">Save Section</button>
        </form>
        <div class="back-link">
            <a href="manage_homepage.php">⬅ Go back to Manage Home</a>
        </div>
    </div>
</body>
</html>
