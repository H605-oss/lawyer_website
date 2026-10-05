<?php
include 'includes/db.php';
session_start();

$appointment_id = intval($_GET['appointment_id']);
$viewer = $_GET['viewer'] ?? '';
if (!$appointment_id || !in_array($viewer, ['lawyer','client'])) exit;

// The "other" user
$other = ($viewer === 'lawyer') ? 'client' : 'lawyer';

// fetch last typing time
$stmt = $conn->prepare("SELECT last_typing FROM typing_status WHERE appointment_id=? AND sender_type=?");
$stmt->bind_param("is", $appointment_id, $other);
$stmt->execute();
$stmt->bind_result($last_typing);
$stmt->fetch();
$stmt->close();

$status = '';
if ($last_typing) {
    $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($last_typing);
    if ($diff <= 3) { // typing within last 3 seconds
        $status = "Typing...";
    }
}

echo $status;
