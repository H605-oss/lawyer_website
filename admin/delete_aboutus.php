<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}
include '../includes/db.php';

$id = $_GET['id'];

// Delete image from folder
$result = $conn->query("SELECT image FROM about_us WHERE id=$id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['image']) && file_exists("../images/" . $row['image'])) {
        unlink("../images/" . $row['image']);
    }
}

$conn->query("DELETE FROM about_us WHERE id=$id");

header("Location: manage_aboutus.php?deleted=1");
exit();
