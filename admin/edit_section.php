<?php
session_start();
include "../includes/db.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: manage_homepage.php");
    exit();
}

$section_id = intval($_GET['id']);
$message = '';

// Fetch section data
$sql = "SELECT * FROM sections WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("location: manage_homepage.php");
    exit();
}
$section = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_name = $_POST['section_name'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $button_link = $_POST['button_link'];

    $image = $section['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = "../uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            if (!empty($image) && file_exists($uploadDir . $image)) {
                unlink($uploadDir . $image);
            }
            $image = $imageName;
        }
    }

    $updateSql = "UPDATE sections SET section_name=?, title=?, description=?, button_text=?, button_link=?, image=? WHERE id=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssssi", $section_name, $title, $description, $button_text, $button_link, $image, $section_id);

    if ($updateStmt->execute()) {
        $message = "Section updated successfully!";
        header("location: manage_homepage.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Section</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body { font-family: 'Arial', sans-serif; background: #f4f7f9; margin:0; padding:0; }
    .container { max-width: 700px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);}
    h2 { text-align:center; margin-bottom: 25px; color: #333; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display:block; margin-bottom: 6px; font-weight: bold; color: #555; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border-radius: 6px; border:1px solid #ccc; font-size: 14px; }
    .form-group input[type="file"] { padding: 6px; }
    button { background-color: #3498db; color: #fff; padding: 12px 20px; border:none; border-radius: 6px; font-size: 16px; cursor:pointer; display:flex; align-items:center; gap:8px; }
    button:hover { background-color: #2980b9; }
    .message { margin-bottom: 15px; text-align:center; color: green; font-weight: bold; }
    img.preview { max-width: 180px; margin-bottom: 15px; border-radius: 6px; display:block; }
</style>
</head>
<body>
<div class="container">
    <h2>Edit Homepage Section</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Section Name</label>
            <input type="text" name="section_name" value="<?php echo htmlspecialchars($section['section_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($section['title']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($section['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Button Text</label>
            <input type="text" name="button_text" value="<?php echo htmlspecialchars($section['button_text']); ?>">
        </div>

        <div class="form-group">
            <label>Button Link</label>
            <input type="text" name="button_link" value="<?php echo htmlspecialchars($section['button_link']); ?>">
        </div>

        <div class="form-group">
            <label>Image</label>
            <?php if(!empty($section['image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($section['image']); ?>" class="preview" alt="Section Image">
            <?php endif; ?>
            <input type="file" name="image">
        </div>

        <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Update Section</button>
    </form>
</div>
</body>
</html>
