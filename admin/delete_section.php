<?php
session_start();

// ✅ Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include "../includes/db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM sections WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: manage_homepage.php?msg=deleted");
    } else {
        echo "Error deleting section: " . $stmt->error;
    }
} else {
    echo "Invalid request";
}
?>
