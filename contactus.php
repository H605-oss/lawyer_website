<?php
session_start();
require 'vendor/autoload.php';
include 'includes/db.php';
include 'includes/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Check if user is logged in before sending mail
    if (!isset($_SESSION['user_id'])) {
        $status = "not_logged_in"; // User not logged in
    } else {
        $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
        $last_name  = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
        $email      = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone      = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $query      = filter_var($_POST['query'], FILTER_SANITIZE_STRING);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'hajrabibi012003@gmail.com';
                $mail->Password   = ''; // App password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                $mail->setFrom('hajrabibi012003@gmail.com', 'Lawyer_Website');
                $mail->addAddress($email, $first_name . ' ' . $last_name);
                $mail->addBCC('YOUR_GMAIL_ADDRESS@gmail.com');

                $mail->isHTML(true);
                $mail->Subject = "Thank you for contacting Lawyer Website";
                $mail->Body = "
                    <h2>Hi " . htmlspecialchars($first_name) . " " . htmlspecialchars($last_name) . ",</h2>
                    <p>Thank you for reaching out to us. Here are the details you submitted:</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                    <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($query)) . "</p>
                    <br><p>We will get back to you shortly.</p>
                ";
                $mail->AltBody = "Name: $first_name $last_name\nEmail: $email\nPhone: $phone\nMessage: $query";

                $mail->send();
                $status = "success";
            } catch (Exception $e) {
                $status = "error";
            }
        } else {
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        .form-container {
            max-width: 800px;
            margin: 4rem auto;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 2.5rem;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .contact-header h2 {
            font-size: 2.2rem;
            font-weight: bold;
            color: #212529;
        }
        .contact-header p {
            color: #495057;
        }
        input, textarea {
            color: #212529 !important;
            background-color: #f9fafb;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="form-container">
        <div class="contact-header">
            <h2>Get In Touch</h2>
            <p>We're ready to help you with your legal needs.</p>
        </div>

        <?php if ($status === "success"): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md fade-in" role="alert">
                <p class="font-bold">Success!</p>
                <p>Your message has been sent successfully.</p>
            </div>

        <?php elseif ($status === "error"): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md fade-in" role="alert">
                <p class="font-bold">Error!</p>
                <p>There was a problem sending your message. Please try again later.</p>
            </div>

        <?php elseif ($status === "not_logged_in"): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md fade-in" role="alert">
                <p class="font-bold">Login Required!</p>
                <p>You must be logged in to submit this form. <a href="login.php" class="underline text-blue-700">Click here to login</a>.</p>
            </div>
        <?php endif; ?>

        <form action="contactus.php" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first_name" id="first_name" required 
                           class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required 
                           class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required 
                           class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" name="phone" id="phone" required 
                           class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label for="query" class="block text-sm font-medium text-gray-700">What do you have in mind?</label>
                <textarea name="query" id="query" rows="5" required 
                          class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" 
                    class="w-full md:w-auto px-8 py-3 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-full transition-all duration-300 hover:scale-105">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
