<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

// Get the current page name to set the active menu item
$currentPage = basename($_SERVER['PHP_SELF']);

// Include your database connection file
include "../includes/db.php";

// Dashboard counts
$fetchedLawyers = 0;
$fetchedAppointments = 0;
$fetchedAboutus = 0;
$fetchedServices = 0;
$fetchedHomepageSections = 0;

// Set max values for the charts
$maxLawyers = 20; 
$maxAppointments = 50; 
$maxAboutus = 10; 
$maxServices = 15;
$maxHomepageSections = 5;

// Count Lawyers
$sqlLawyers = "SELECT COUNT(*) as total FROM lawyers";
$resultLawyers = $conn->query($sqlLawyers);
if ($resultLawyers && $row = $resultLawyers->fetch_assoc()) {
    $fetchedLawyers = $row['total'];
}

// Count Appointments
$sqlAppointments = "SELECT COUNT(*) as total FROM appointments";
$resultAppointments = $conn->query($sqlAppointments);
if ($resultAppointments && $row = $resultAppointments->fetch_assoc()) {
    $fetchedAppointments = $row['total'];
}

// Count About Us Sections
$sqlAboutus = "SELECT COUNT(*) as total FROM about_us";
$resultAboutus = $conn->query($sqlAboutus);
if ($resultAboutus && $row = $resultAboutus->fetch_assoc()) {
    $fetchedAboutus = $row['total'];
}

// Count Services (Assuming a 'services' table exists)
$sqlServices = "SELECT COUNT(*) as total FROM services";
$resultServices = $conn->query($sqlServices);
if ($resultServices && $row = $resultServices->fetch_assoc()) {
    $fetchedServices = $row['total'];
}

// Count Homepage Sections (Assuming a 'homepage_sections' table exists)
$sqlHomepage = "SELECT COUNT(*) as total FROM homepage_sections";
$resultHomepage = $conn->query($sqlHomepage);
if ($resultHomepage && $row = $resultHomepage->fetch_assoc()) {
    $fetchedHomepageSections = $row['total'];
}

// Calculate percentages, capped at 100%
$totalLawyers = min(100, round(($fetchedLawyers / $maxLawyers) * 100));
$totalAppointments = min(100, round(($fetchedAppointments / $maxAppointments) * 100));
$totalAboutus = min(100, round(($fetchedAboutus / $maxAboutus) * 100));
$totalServices = min(100, round(($fetchedServices / $maxServices) * 100));
$totalHomepage = min(100, round(($fetchedHomepageSections / $maxHomepageSections) * 100));

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f0f2f5;
            --card-bg-color: #ffffff;
            --text-color-dark: #2c3e50;
            --text-color-light: #555555;
            --sidebar-color: #1a202c; 
            --primary-color: #4a74c6;
            --accent-color: #2ecc71;
            --delete-color: #e74c3c;
            --edit-color: #f1c40f;

            /* Card Colors */
            --lawyers-color: #3498db;
            --appointments-color: #27ae60;
            --aboutus-color: #e67e22;
            --services-color: #8e44ad;
            --homepage-color: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--bg-color);
            display: flex;
            height: 100vh;
            color: var(--text-color-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sidebar {
            width: 250px;
            background-color: var(--sidebar-color);
            color: #ecf0f1;
            padding: 25px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: width 0.3s ease;
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 1px solid #4a627a;
            margin-bottom: 25px;
        }

        .sidebar-header .profile-pic {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            object-fit: cover;
            margin-bottom: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar-header .user-name {
            font-size: 20px;
            font-weight: 600;
            color: #f5f5f5;
            margin: 5px 0 0;
        }

        .sidebar-header .user-status {
            font-size: 14px;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .sidebar-header .user-status::before {
            content: '';
            width: 8px;
            height: 8px;
            background-color: var(--accent-color);
            border-radius: 50%;
            margin-right: 8px;
            box-shadow: 0 0 5px var(--accent-color);
        }

        .sidebar-menu-title {
            font-size: 13px;
            font-weight: 600;
            color: #bdc3c7;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-left: 15px;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 14px 15px;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 15px;
            font-weight: 500;
        }

        .sidebar-menu a:hover {
            background-color: #4a627a;
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar-menu a i {
            margin-right: 12px;
            font-size: 18px;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .top-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color-dark);
            position: relative;
        }

        .top-header h1::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 60px;
            height: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        /* Logout Button */
        .logout-btn {
            background-color: var(--delete-color);
            color: #fff !important;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logout-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        /* Responsive Grid for all content */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            flex-grow: 1;
        }

        .dashboard-item {
            background-color: var(--card-bg-color);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-content i {
            font-size: 45px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .card-content .text h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color-dark);
        }

        .card-content .text p {
            margin: 0;
            font-size: 16px;
            color: var(--text-color-light);
            font-weight: 400;
        }

        /* Chart-specific styles */
        .chart-container {
            height: 180px;
            width: 100%;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Specific item colors */
        .dashboard-item.lawyers .card-content i { color: var(--lawyers-color); }
        .dashboard-item.appointments .card-content i { color: var(--appointments-color); }
        .dashboard-item.aboutus .card-content i { color: var(--aboutus-color); }
        .dashboard-item.services .card-content i { color: var(--services-color); }
        .dashboard-item.homepage .card-content i { color: var(--homepage-color); }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .sidebar {
                width: 200px;
                padding: 15px;
            }
            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/admin.jpeg" alt="Profile Picture" class="profile-pic">
            <p class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p class="user-status">Online</p>
        </div>
        <p class="sidebar-menu-title">General</p>
        <ul class="sidebar-menu">
            <li><a href="admin_pannel.php" class="<?php echo ($currentPage == 'admin_pannel.php') ? 'active' : ''; ?>"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_lawyers.php" class="<?php echo ($currentPage == 'manage_lawyers.php') ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> <span>Manage Lawyers</span></a></li>
            <li><a href="manage_appointments.php" class="<?php echo ($currentPage == 'manage_appointments.php') ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> <span>Manage Appointments</span></a></li>
            <li><a href="manage_aboutus.php" class="<?php echo ($currentPage == 'manage_aboutus.php') ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> <span>Manage Aboutus</span></a></li>
            <li><a href="manage_homepage.php" class="<?php echo ($currentPage == 'manage_homepage.php') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> <span>Manage Home</span></a></li>
            <li><a href="manage_service.php" class="<?php echo ($currentPage == 'manage_service.php') ? 'active' : ''; ?>"><i class="fa-solid fa-cogs"></i> <span>Manage Service</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-header">
            <h1>Admin Dashboard</h1>
            <a href="../logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-item lawyers">
                <div class="card-content">
                    <i class="fa-solid fa-users"></i>
                    <div class="text">
                        <h3><?php echo htmlspecialchars($fetchedLawyers); ?></h3>
                        <p>Total Lawyers</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="lawyersChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item appointments">
                <div class="card-content">
                    <i class="fa-solid fa-calendar-check"></i>
                    <div class="text">
                        <h3><?php echo htmlspecialchars($fetchedAppointments); ?></h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="appointmentsChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item services">
                <div class="card-content">
                    <i class="fa-solid fa-handshake"></i>
                    <div class="text">
                        <h3><?php echo htmlspecialchars($fetchedServices); ?></h3>
                        <p>Total Services</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item homepage">
                <div class="card-content">
                    <i class="fa-solid fa-file-invoice"></i>
                    <div class="text">
                        <h3><?php echo htmlspecialchars($fetchedHomepageSections); ?></h3>
                        <p>Homepage Sections</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="homepageChart"></canvas>
                </div>
            </div>

            <div class="dashboard-item aboutus">
                <div class="card-content">
                    <i class="fa-solid fa-circle-info"></i>
                    <div class="text">
                        <h3><?php echo htmlspecialchars($fetchedAboutus); ?></h3>
                        <p>About Us Sections</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="aboutusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for charts
        const lawyersData = {
            labels: ['Lawyers'],
            datasets: [{
                label: 'Current Lawyers',
                data: [<?php echo (int) $fetchedLawyers; ?>],
                backgroundColor: ['#3498db'],
                borderColor: ['#2980b9'],
                borderWidth: 1,
                borderRadius: 5
            }, {
                label: 'Max Lawyers',
                data: [<?php echo (int) ($maxLawyers - $fetchedLawyers); ?>],
                backgroundColor: ['#e0e0e0'],
                borderColor: ['#d5d5d5'],
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        const appointmentsData = {
            labels: ['Current', 'Max'],
            datasets: [{
                label: 'Appointments',
                data: [<?php echo (int) $fetchedAppointments; ?>, <?php echo (int) $maxAppointments; ?>],
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39, 174, 96, 0.2)',
                tension: 0.4,
                fill: true
            }]
        };
        
        const servicesData = {
            labels: ['Used', 'Remaining'],
            datasets: [{
                label: 'Services',
                data: [<?php echo (int) $fetchedServices; ?>, <?php echo (int) ($maxServices - $fetchedServices); ?>],
                backgroundColor: ['#8e44ad', '#e0e0e0'],
                hoverOffset: 4
            }]
        };
        
        const homepageData = {
            labels: ['Used', 'Remaining'],
            datasets: [{
                label: 'Homepage Sections',
                data: [<?php echo (int) $fetchedHomepageSections; ?>, <?php echo (int) ($maxHomepageSections - $fetchedHomepageSections); ?>],
                backgroundColor: ['#2c3e50', '#e0e0e0'],
                hoverOffset: 4
            }]
        };

        const aboutusData = {
            labels: ['Current', 'Max'],
            datasets: [{
                label: 'About Us Sections',
                data: [<?php echo (int) $fetchedAboutus; ?>, <?php echo (int) $maxAboutus; ?>],
                backgroundColor: ['#e67e22', '#f39c12'],
                borderColor: 'rgba(230, 126, 34, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 50,
                    display: false
                },
                x: {
                    display: false
                }
            }
        };

        // Create the charts
        new Chart(document.getElementById('lawyersChart'), {
            type: 'bar',
            data: lawyersData,
            options: {
                ...commonOptions,
                indexAxis: 'y', // This is the key change for horizontal bars
                scales: {
                    ...commonOptions.scales,
                    y: {
                        beginAtZero: true,
                        display: true,
                        grid: { display: false }
                    },
                    x: {
                        beginAtZero: true,
                        max: <?php echo (int) $maxLawyers; ?>,
                        display: true
                    }
                },
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('appointmentsChart'), {
            type: 'line',
            data: appointmentsData,
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        max: <?php echo (int) $maxAppointments; ?>,
                        display: true
                    },
                    x: {
                        display: true
                    }
                }
            }
        });

        new Chart(document.getElementById('servicesChart'), {
            type: 'doughnut',
            data: servicesData,
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: { display: false },
                    x: { display: false }
                },
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
        
        new Chart(document.getElementById('homepageChart'), {
            type: 'pie',
            data: homepageData,
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: { display: false },
                    x: { display: false }
                },
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('aboutusChart'), {
            type: 'bar',
            data: aboutusData,
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: <?php echo (int) $maxAboutus; ?>,
                        display: true
                    },
                    x: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>