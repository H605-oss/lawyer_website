<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check if an ID is provided in the URL
if (!isset($_GET['id'])) {
    header("location: index.php");
    exit();
}

$lawyer_id = $_GET['id'];

// Fetch the specific lawyer's data
$sql = "SELECT id, name, specialty, location, bio, profile_image FROM lawyers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("location: index.php");
    exit();
}

$lawyer = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lawyer['name']); ?> Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 mt-8 flex flex-col md:flex-row items-center">
            <!-- Lawyer Profile Section -->
            <div class="md:w-1/3 text-center md:text-left mb-6 md:mb-0 md:mr-12">
                <?php
                    $image_src = !empty($lawyer['profile_image']) ? 'uploads/' . htmlspecialchars($lawyer['profile_image']) : 'images/default-user.png';
                ?>
                <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($lawyer['name']); ?> Profile Picture" class="rounded-full w-48 h-48 md:w-64 md:h-64 object-cover mx-auto md:mx-0 shadow-lg border-4 border-gray-200">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mt-6"><?php echo htmlspecialchars($lawyer['name']); ?></h1>
                <p class="text-lg text-gray-600 mt-2">Specialty: <?php echo htmlspecialchars($lawyer['specialty']); ?></p>
                <p class="text-lg text-gray-600 mt-1">Location: <?php echo htmlspecialchars($lawyer['location']); ?></p>
            </div>
            
            <!-- Bio and Appointment Button Section -->
            <div class="md:w-2/3">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">About Me</h2>
                <p class="text-gray-700 leading-relaxed mb-8">
                    <?php echo htmlspecialchars($lawyer['bio']); ?>
                </p>

                <div class="mt-8 bg-gray-50 p-8 rounded-lg shadow-inner text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Ready to schedule?</h2>
                    <p class="text-gray-600 mb-6">Click the button below to book an appointment with <?php echo htmlspecialchars($lawyer['name']); ?>.</p>
                    <a href="book_appointment.php?id=<?php echo htmlspecialchars($lawyer['id']); ?>" class="inline-block bg-black text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-800 transition-colors duration-300 mt-6 shadow-md">
                        Schedule Your Appointment
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-8 text-center">
            <a href="index.php" class="text-blue-600 hover:underline">← Back to Home  Page</a>
        </div>
    </div>
</body> 
</html>
