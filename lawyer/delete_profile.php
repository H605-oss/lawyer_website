<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Check agar lawyer login hai
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'lawyer') {
    header("location: ../login.php");
    exit();
}

$lawyer_id = $_SESSION['lawyer_id'] ?? 0;

// Lawyer ka data fetch karo (image delete karne ke liye)
$stmt = $conn->prepare("SELECT profile_image FROM lawyers WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
$lawyer = $result->fetch_assoc();
$stmt->close();

if (!$lawyer) {
    echo "<p>No profile found to delete.</p>";
    exit();
}

// Delete image agar default nahi hai
if (!empty($lawyer['profile_image']) && $lawyer['profile_image'] !== 'default.png') {
    $image_path = "../uploads/" . $lawyer['profile_image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Lawyer record delete karo
$delete_stmt = $conn->prepare("DELETE FROM lawyers WHERE id = ?");
$delete_stmt->bind_param("i", $lawyer_id);

if ($delete_stmt->execute()) {
    // Session destroy aur logout
    session_unset();
    session_destroy();
    header("Location: ../login.php?msg=Profile+deleted+successfully");
    exit();
} else {
    echo "<p>Error deleting profile: " . $delete_stmt->error . "</p>";
}

$delete_stmt->close();
$conn->close();
?>
