<?php
include 'includes/db.php';

$message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type'];

    // Check if email already exists (prepared statement)
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "<p class='error-msg'>Email already exists. Please use a different email.</p>";
    } else {
        // Hash password (optional: remove if plain-text needed for testing)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = $password; // keeping same as your current structure

        $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $user_type);

        if ($insert_stmt->execute()) {
            header("location: login.php?registration_success=1");
            exit();
        } else {
            $message = "<p class='error-msg'>Something went wrong. Please try again later.</p>";
        }
        $insert_stmt->close();
    }

    $check_stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            animation: fadeInBody 1.2s ease-in-out;
        }
        @keyframes fadeInBody {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }

        .signup-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
            transform: translateY(40px);
            opacity: 0;
            animation: slideUpContainer 1s ease-out forwards;
        }
        @keyframes slideUpContainer {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            animation: fadeInTitle 1s ease-in;
        }
        @keyframes fadeInTitle {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group {
            margin-bottom: 15px;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: 0.3s ease;
        }
        .input-group input:focus,
        .input-group select:focus {
            border-color: #333;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .signup-btn {
            width: 100%;
            padding: 15px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .signup-btn:hover {
            background-color: #111;
            transform: scale(1.03);
        }

        .social-login-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #666;
        }
        .social-login-separator::before,
        .social-login-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }
        .social-login-separator:not(:empty)::before {
            margin-right: .25em;
        }
        .social-login-separator:not(:empty)::after {
            margin-left: .25em;
        }

        .social-buttons button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f7f7f7;
            font-size: 16px;
            cursor: pointer;
            color: #333;
            transition: all 0.3s ease;
        }
        .social-buttons button:hover {
            background-color: #eee;
            transform: scale(1.03);
        }
        .social-buttons button img {
            width: 20px;
            margin-right: 10px;
        }
        .social-buttons .google { background-color: #fff; border: 1px solid #ccc; }
        .social-buttons .facebook { background-color: #333; color: #fff; border: none; }
        .social-buttons .facebook img { filter: grayscale(100%) invert(100%); } 
        .social-buttons .facebook:hover {
            background-color: #111; 
        }

        .footer-link {
            margin-top: 20px;
            font-size: 14px;
        }
        .footer-link a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }

        .error-msg, .success-msg {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            animation: fadeError 0.5s ease-in-out;
        }
        .error-msg {
            color: #8b0000;
            background-color: #f5e0e0;
            border: 1px solid #d0a0a0;
        }
        .success-msg {
            color: #006400;
            background-color: #e0f5e0;
            border: 1px solid #a0d0a0;
        }
        @keyframes fadeError {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php 
            echo $message; 
            echo $success_message;
        ?>
        <form action="signup.php" method="POST">
            <div class="input-group">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <select id="user_type" name="user_type" required>
                    <option value="customer">Client</option>
                    <option value="lawyer">Lawyer</option>
                </select>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>

        <p class="social-login-separator">Or</p>

        <div class="social-buttons">
            <button class="facebook">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook logo">
                Login with Facebook
            </button>
            <button class="google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google logo">
                Login with Google
            </button>
        </div>

        <div class="footer-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
