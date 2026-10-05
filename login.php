<?php
session_start();
include 'includes/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, password, user_type, username FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $password_ok = false;
            if (!empty($row['password']) && password_verify($password, $row['password'])) {
                $password_ok = true;
            } elseif ($password === $row['password']) {
                $password_ok = true;
            }

            if ($password_ok) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_type'] = $row['user_type'];
                $_SESSION['username']  = $row['username'];
                $_SESSION['email']     = $row['email'];

                if ($_SESSION['user_type'] == 'admin') {
                    header("location: admin/admin_pannel.php");
                    exit();
                } elseif ($_SESSION['user_type'] == 'lawyer') {
                    $lawyer_stmt = $conn->prepare("SELECT id FROM lawyers WHERE email = ? LIMIT 1");
                    $lawyer_stmt->bind_param("s", $email);
                    $lawyer_stmt->execute();
                    $lawyer_result = $lawyer_stmt->get_result();
                    if ($lawyer_row = $lawyer_result->fetch_assoc()) {
                        $_SESSION['lawyer_id'] = $lawyer_row['id'];
                    } else {
                        $_SESSION['lawyer_id'] = null;
                    }
                    $lawyer_stmt->close();
                    header("location: lawyer/lawyer_dashboard.php");
                    exit();
                } else {
                    header("location: index.php");
                    exit();
                }
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No user found with that email.";
        }
        $stmt->close();
    } else {
        $message = "Database query error. Please try again later.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        overflow: hidden;
        animation: fadeIn 1.2s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }

    .login-container {
        background-color: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 380px;
        text-align: center;
        transform: translateY(50px);
        opacity: 0;
        animation: slideUp 1s ease-out forwards;
    }
    @keyframes slideUp {
        to { transform: translateY(0); opacity: 1; }
    }

    h2 {
        margin-bottom: 25px;
        color: #333;
        animation: fadeSlide 1.3s ease;
    }
    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group {
        margin-bottom: 20px;
    }
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .form-group input:focus {
        border-color: #333;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    .login-btn {
        width: 100%;
        padding: 12px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .login-btn:hover {
        background-color: #555;
        transform: scale(1.02);
    }

    .social-login {
        margin-top: 20px;
    }
    .social-login .or-separator {
        margin: 20px 0;
        position: relative;
        text-align: center;
    }
    .social-login .or-separator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #ddd;
        z-index: 1;
    }
    .social-login .or-separator span {
        background-color: #fff;
        padding: 0 10px;
        position: relative;
        z-index: 2;
        color: #888;
        font-size: 14px;
    }

    .social-login-btn {
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        border: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .social-login-btn:hover {
        transform: scale(1.03);
        background-color: #fafafa;
    }
    .social-login-btn img {
        width: 20px;
        margin-right: 10px;
    }

    .login-message {
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        animation: fadeInError 0.5s ease-in-out;
        display: <?php echo empty($message) ? 'none' : 'block'; ?>;
    }
    @keyframes fadeInError {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .footer-link {
        margin-top: 20px;
        font-size: 14px;
    }
    .footer-link a {
        color: #333;
        text-decoration: none;
    }
    .footer-link a:hover {
        text-decoration: underline;
    }

    /* ✅ Profile dropdown look (same style as image) */
    .profile-dropdown {
        background-color: #1e1f26;
        color: #fff;
        border-radius: 8px;
        width: 180px;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        position: absolute;
        top: 60px;
        right: 20px;
        animation: fadeIn 0.4s ease-in-out;
    }
    .profile-dropdown h4 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }
    .profile-dropdown p {
        margin: 5px 0;
        font-size: 13px;
        color: #bbb;
    }
    .profile-dropdown a {
        color: #fff;
        text-decoration: none;
        display: block;
        margin-top: 8px;
        font-size: 14px;
    }
    .profile-dropdown a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <div class="login-message">
        <?php echo htmlspecialchars($message); ?>
    </div>

    <?php if (isset($_SESSION['username'])): ?>
        <div class="profile-dropdown">
            <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <a href="logout.php">Logout</a>
        </div>
    <?php else: ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="social-login">
            <div class="or-separator"><span>Or</span></div>
            <button class="social-login-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook logo"> Login with Facebook
            </button>
            <button class="social-login-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google logo"> Login with Google
            </button>
        </div>

        <div class="footer-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
