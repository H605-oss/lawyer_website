<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

// Check if function 'e' doesn't exist before declaring it.
if (!function_exists('e')) {
    function e($v) {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// ✅ Check confirmed appointment for chat link
$showChat = false;
$appointment_id = 0;

// Login checks
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['lawyer_id']);
$is_lawyer = isset($_SESSION['lawyer_id']);

// ✅ Detect current file name
$current_file = basename($_SERVER['PHP_SELF']);

// ✅ Check if current page is lawyer panel (inside /lawyer/ folder)
$is_lawyer_panel = strpos($_SERVER['PHP_SELF'], '/lawyer/') !== false;

// ✅ Check confirmed appointment (for showing chat button on all pages)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE user_id = ? AND status = 'confirmed' LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $showChat = true;
        $appointment_id = $row['id'];
    }
    $stmt->close();
}

// ✅ Show dashboard if lawyer is in panel OR on chat.php
$showDashboard = $is_logged_in && ($is_lawyer_panel || ($is_lawyer && $current_file === 'chat.php'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Website</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .professional-navbar { 
            background-color: #1a202c; 
            border-bottom: none !important;
            box-shadow: none !important;
        }
        .professional-navbar-link {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s, transform 0.3s;
        }
        .professional-navbar-link:hover {
            color: #4299e1;
            transform: translateY(-2px);
        }
        .profile-dropdown { position: relative; }
        .profile-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #1a202c;
            min-width: 190px;
            border-radius: 0.5rem;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.3);
            margin-top: 0.5rem;
            z-index: 10;
            overflow: hidden;
        }
        .profile-dropdown:hover .profile-dropdown-content {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        .profile-dropdown-content span, .profile-dropdown-content small, .profile-dropdown-content a {
            color: #f1f5f9;
            display: block;
            text-align: center;
        }
        .profile-dropdown-content span { font-weight: 600; padding-top: 10px; }
        .profile-dropdown-content small { color: #cbd5e1; font-size: 13px; margin-bottom: 10px; }
        .profile-dropdown-content a {
            padding: 10px 16px; text-decoration: none; border-top: 1px solid #2d3748;
        }
        .profile-dropdown-content a:hover { background-color: #4299e1; color: white; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .site-logo img {
            height: 4rem; width: auto; object-fit: contain; transition: transform 0.3s ease;
        }
        .site-logo img:hover { transform: scale(1.05); }
        a { text-decoration: none; }
    </style>
</head>
<body class="bg-gray-100">

<header class="shadow-sm sticky top-0 z-50">
    <nav class="professional-navbar w-full px-6 py-3 flex justify-between items-center text-white">
        
        <a href="index.php" class="site-logo flex items-center">
            <img src="./images/logo.png" alt="Logo">
        </a>

        <div class="hidden md:flex items-center space-x-6">

            <!-- ✅ Dashboard visible for lawyer panel or chat.php -->
            <?php if ($showDashboard): ?>
                <a href="<?= $is_lawyer ? './lawyer/lawyer_dashboard.php' : 'user_dashboard.php' ?>" class="professional-navbar-link">Dashboard</a>
            <?php endif; ?>

            <a href="index.php" class="professional-navbar-link">Home</a>
            <a href="aboutus.php" class="professional-navbar-link">About Us</a>
            <a href="service.php" class="professional-navbar-link">Services</a>
            <a href="contactus.php" class="professional-navbar-link">Contact</a>

            <form action="search_lawyers.php" method="GET" class="flex items-center">
                <div class="relative w-72 rounded-full border border-gray-600 focus-within:border-blue-500 transition-colors duration-300">
                    <input type="text" name="specialty" placeholder="Search..." 
                           class="w-full bg-gray-700 text-gray-100 placeholder-gray-400 rounded-full py-2 pl-4 pr-10 focus:outline-none">
                    <button type="submit" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-blue-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- ✅ Chat Button -->
            <?php if ($showChat): ?>
                <a href="chat.php?appointment_id=<?= $appointment_id ?>" 
                   class="bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-500 transition-all duration-300">
                    <i class="fas fa-comments"></i> Chat with Lawyer
                </a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id']) || isset($_SESSION['lawyer_id'])): ?>
                <div class="profile-dropdown">
                    <button class="flex items-center text-xl text-gray-300 hover:text-white transition-colors duration-300 focus:outline-none">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div class="profile-dropdown-content">
                        <span><?php echo e($_SESSION['username']); ?></span>
                        <small><?php echo e($_SESSION['email']); ?></small>
                        <a href="logout.php" class="text-red-400 hover:text-white">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="bg-gray-700 text-white px-6 py-2 rounded-full hover:bg-gray-600 transition-colors duration-300 shadow-lg">Login</a>
            <?php endif; ?>
        </div>

        <button id="mobile-menu-button" class="md:hidden text-white text-2xl">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <div id="mobile-menu" class="md:hidden bg-gray-900 text-white transition-all duration-300 ease-in-out overflow-hidden max-h-0">
        
        <!-- ✅ Dashboard visible for lawyer panel or chat.php -->
        <?php if ($showDashboard): ?>
            <a href="<?= $is_lawyer ? 'lawyer/lawyer_dashboard.php' : 'user_dashboard.php' ?>" class="block px-6 py-3 text-center hover:bg-gray-700">Dashboard</a>
        <?php endif; ?>

        <a href="index.php" class="block px-6 py-3 text-center hover:bg-gray-700">Home</a>
        <a href="aboutus.php" class="block px-6 py-3 text-center hover:bg-gray-700">About Us</a>
        <a href="service.php" class="block px-6 py-3 text-center hover:bg-gray-700">Services</a>
        <a href="contactus.php" class="block px-6 py-3 text-center hover:bg-gray-700">Contact</a>

        <form action="search_lawyers.php" method="GET" class="p-4">
            <input type="text" name="specialty" placeholder="Search Lawyers" 
                   class="w-full bg-gray-700 text-white placeholder-gray-400 border border-gray-600 rounded-full py-2 px-4 focus:outline-none focus:border-blue-500">
        </form>

        <?php if ($showChat): ?>
            <a href="chat.php?appointment_id=<?= $appointment_id ?>" class="block px-6 py-3 text-center hover:bg-green-600">
                <i class="fas fa-comments"></i> Chat with Lawyer
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['lawyer_id'])): ?>
            <div class="text-center border-t border-gray-700 py-3">
                <span class="block font-semibold"><?php echo e($_SESSION['username']); ?></span>
                <small class="block text-gray-400 mb-2"><?php echo e($_SESSION['email']); ?></small>
                <a href="logout.php" class="inline-block bg-red-500 text-white px-6 py-2 rounded-full mt-1 hover:bg-red-600">Logout</a>
            </div>
        <?php else: ?>
            <a href="login.php" class="block px-6 py-3 text-center hover:bg-gray-700">Login</a>
        <?php endif; ?>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        var mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('max-h-0');
        mobileMenu.classList.toggle('max-h-screen');
    });
</script>
</body>
</html>
