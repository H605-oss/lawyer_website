<?php
session_start();
include __DIR__ . '/../includes/db.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['lawyer_id'])) {
    die("Unauthorized access");
}

$lawyer_id = $_SESSION['lawyer_id'];

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hajrabibi012003@gmail.com';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('hajrabibi012003@gmail.com', 'Lawyer_Website');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("sii", $status, $appointment_id, $lawyer_id);

    if ($stmt->execute()) {
        $q = $conn->prepare("SELECT full_name, email_address, appointment_date, appointment_time FROM appointments WHERE id = ?");
        $q->bind_param("i", $appointment_id);
        $q->execute();
        $r = $q->get_result()->fetch_assoc();
        $q->close();

        $subject = "Appointment Update ($status)";
        $body = "<p>Dear {$r['full_name']},</p>
                 <p>Your appointment scheduled on <b>{$r['appointment_date']}</b> at <b>{$r['appointment_time']}</b> has been <b>$status</b> by the lawyer.</p>";

        // ✅ If confirmed → auto create welcome message only (no link)
        if ($status === 'confirmed') {
            $msg = $conn->prepare("INSERT INTO messages (appointment_id, sender_type, sender_id, message) VALUES (?, 'lawyer', ?, ?)");
            $welcome = "Hello! Your appointment has been confirmed. You can chat with me here before our meeting.";
            $msg->bind_param("iis", $appointment_id, $lawyer_id, $welcome);
            $msg->execute();
            $msg->close();
        }

        sendMail($r['email_address'], $subject, $body);

        header("Location: get_appointments.php");
        exit();
    }
}
?>
