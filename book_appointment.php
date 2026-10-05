<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
require 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lawyer_name = '';
$lawyer_email = '';
$lawyer_id = null;
$error_message = '';
$success_message = '';

$user_fullname = '';
$user_email = '';
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ? LIMIT 1");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
if ($u = $res_user->fetch_assoc()) {
    $user_fullname = $u['username'];
    $user_email = $u['email'];
}
$stmt_user->close();

if (isset($_GET['id'])) {
    $lawyer_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT name, email FROM lawyers WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $lawyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $lawyer = $result->fetch_assoc();
        $lawyer_name = $lawyer['name'];
        $lawyer_email = $lawyer['email'];
    }
    $stmt->close();
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lawyer_id_post = intval($_POST['lawyer_id']);
    $fullName = trim($_POST['fullName']);
    $fullAddress = trim($_POST['fullAddress']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $emailAddress = trim($_POST['emailAddress']);
    $appointmentDate = trim($_POST['appointmentDate']);
    $appointmentTime = trim($_POST['appointmentTime']);
    $preferredAttorney = trim($_POST['preferredAttorney']);
    $legalIssue = trim($_POST['legalIssue']);
    $legalMatter = ($_POST['legalMatter'] === 'Other') ? trim($_POST['otherLegalMatter']) : trim($_POST['legalMatter']);

    // ✅ Backend restriction: Prevent past date/time submission
    $currentDateTime = new DateTime();
    $selectedDateTime = new DateTime("$appointmentDate $appointmentTime");
    if ($selectedDateTime < $currentDateTime) {
        $error_message = "You cannot select a past date or time.";
    } elseif (empty($lawyer_id_post) || empty($fullName) || empty($fullAddress) || empty($phoneNumber) ||
        empty($emailAddress) || empty($appointmentDate) || empty($appointmentTime) || empty($legalIssue) || empty($legalMatter)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO appointments 
                (user_id, lawyer_id, full_name, full_address, phone_number, email_address, appointment_date, appointment_time, preferred_attorney, legal_issue, legal_matter, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssssssss", $user_id, $lawyer_id_post, $fullName, $fullAddress, $phoneNumber, $emailAddress, $appointmentDate, $appointmentTime, $preferredAttorney, $legalIssue, $legalMatter);

        if ($stmt->execute()) {
            $success_message = "Appointment booked successfully! Status: Pending.";

            // Email to Lawyer
            if (!empty($lawyer_email)) {
                sendMail($lawyer_email, 
                    "New Appointment (Pending)",
                    "<p>Dear $lawyer_name,</p>
                    <p><b>$user_fullname</b> has requested an appointment on <b>$appointmentDate</b> at <b>$appointmentTime</b>.</p>
                    <p>Status: <b>Pending</b>. Please log in to confirm or reject the request.</p>"
                );
            }

            // Email to Client
            sendMail($user_email,
                "Appointment Request Submitted",
                "<p>Dear $user_fullname,</p>
                <p>Your appointment with <b>$lawyer_name</b> has been successfully submitted and is currently <b>Pending</b> confirmation.</p>
                <p>You will receive an email once the lawyer accepts or rejects your request.</p>"
            );
        } else {
            $error_message = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment</title>
 <script src="https://cdn.tailwindcss.com"></script>
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- ✅ Tailwind + Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
    font-family: 'Inter', sans-serif !important;
    background-color: #f5f5f5; 
}
</style>
</head>
<body>
<div class="container my-5">
    <div class="card p-4 shadow">
        <h1 class="h3 mb-3 border-bottom pb-2">Book Appointment</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="lawyer_id" value="<?= htmlspecialchars($lawyer_id) ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" class="form-control" name="fullName" required value="<?= htmlspecialchars($user_fullname) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Full Address</label>
                <input type="text" class="form-control" name="fullAddress" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="tel" class="form-control" name="phoneNumber" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" class="form-control" name="emailAddress" required value="<?= htmlspecialchars($user_email) ?>">
                </div>
            </div>

            <!-- ✅ Date and Time Restriction Added -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Time</label>
                    <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Preferred Attorney</label>
                <input type="text" class="form-control" name="preferredAttorney" value="<?= htmlspecialchars($lawyer_name) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Legal Issue</label>
                <textarea class="form-control" name="legalIssue" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Legal Matter</label>
                <div class="d-flex flex-wrap">
                    <div class="form-check me-3"><input class="form-check-input" type="radio" name="legalMatter" value="Family Law" required> Family Law</div>
                    <div class="form-check me-3"><input class="form-check-input" type="radio" name="legalMatter" value="Criminal Law"> Criminal Law</div>
                    <div class="form-check me-3"><input class="form-check-input" type="radio" name="legalMatter" value="Real Estate Law"> Real Estate Law</div>
                    <div class="form-check me-3"><input class="form-check-input" type="radio" name="legalMatter" value="Business Law"> Business Law</div>
                    <div class="form-check me-3 d-flex align-items-center">
                        <input class="form-check-input" type="radio" name="legalMatter" value="Other">
                        <label class="form-check-label me-2">Other:</label>
                        <input type="text" class="form-control form-control-sm" name="otherLegalMatter" style="width:auto;">
                    </div>
                </div>
            </div>
            <button class="btn btn-dark w-100">Submit Appointment</button>
        </form>
    </div>
</div>

<!-- ✅ JS Restriction for Date & Time -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.getElementById("appointmentDate");
    const timeInput = document.getElementById("appointmentTime");

    // Minimum date = today
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const minDate = `${yyyy}-${mm}-${dd}`;
    dateInput.setAttribute("min", minDate);

    // Adjust time restriction based on date
    dateInput.addEventListener("change", function() {
        const selectedDate = new Date(this.value);
        const now = new Date();

        if (selectedDate.toDateString() === now.toDateString()) {
            // Same day → restrict past time
            const hh = String(now.getHours()).padStart(2, '0');
            const min = String(now.getMinutes()).padStart(2, '0');
            timeInput.setAttribute("min", `${hh}:${min}`);
        } else {
            // Future date → all times allowed
            timeInput.removeAttribute("min");
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
