<?php
$DB_HOST='localhost';
$DB_USER='root';
$DB_PASS='';
$DB_NAME='lawyer_booking';

$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

$id = $_GET['id'] ?? null;
if(!$id || !is_numeric($id)){
    die("⚠️ Invalid service ID.");
}
$id = intval($id);

$stmt = $conn->prepare("DELETE FROM services WHERE id=?");
$stmt->bind_param("i",$id);
if($stmt->execute()){
    header("Location: manage_service.php?msg=deleted");
    exit;
}else{
    die("❌ Error deleting service: ".$conn->error);
}
