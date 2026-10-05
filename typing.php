<?php
include 'includes/db.php';
session_start();

$appointment_id = intval($_GET['appointment_id']);
$sender = $_GET['sender'] ?? '';
if (!$appointment_id || !in_array($sender, ['lawyer','client'])) exit;

// store typing info in a temporary table or file
// simple approach: store in DB or session (here we use DB table `typing_status`)
// Make sure you have a table:
// CREATE TABLE typing_status (appointment_id INT, sender_type ENUM('lawyer','client'), last_typing DATETIME, PRIMARY KEY(appointment_id,sender_type));

$now = date('Y-m-d H:i:s');

// check if row exists
$stmt = $conn->prepare("INSERT INTO typing_status (appointment_id, sender_type, last_typing) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE last_typing=?");
$stmt->bind_param("isss", $appointment_id, $sender, $now, $now);
$stmt->execute();
$stmt->close();
