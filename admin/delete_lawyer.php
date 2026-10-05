<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}
include '../includes/db.php';

if (isset($_GET['id'])) {
    $lawyer_id = $_GET['id'];

    // First delete appointments linked to this lawyer
    $deleteAppointments = $conn->prepare("DELETE FROM appointments WHERE lawyer_id = ?");
    $deleteAppointments->bind_param("i", $lawyer_id);
    $deleteAppointments->execute();
    $deleteAppointments->close();

    // Now delete the lawyer
    $sql = "DELETE FROM lawyers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lawyer_id);

    if ($stmt->execute()) {
        header("location: manage_lawyers.php?status=deleted");
        exit();
    } else {
        echo "Error deleting lawyer: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>
