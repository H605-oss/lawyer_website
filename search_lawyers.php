<?php
session_start();
include 'includes/db.php';

// ✅ Restrict page access to logged-in users only
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$specialty = $_GET['specialty'] ?? '';
$location = $_GET['location'] ?? '';

$sql = "SELECT * FROM lawyers WHERE 1=1";
$params = [];
$types = '';

if (!empty($specialty)) {
    $sql .= " AND specialty LIKE ?";
    $params[] = '%' . $specialty . '%';
    $types .= 's';
}

if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = '%' . $location . '%';
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$lawyers = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Lawyer</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<?php include 'includes/header.php'; ?>

<main class="w-full flex-grow">
    <div class="container max-w-5xl mx-auto py-8">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 md:p-10 border border-gray-200">
            <div class="mb-8 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Find Your Lawyer</h1>
                <p class="mt-2 text-gray-500 text-sm sm:text-base max-w-lg mx-auto">Search by specialization or location to find the right legal expert for your needs.</p>
                <form action="search_lawyers.php" method="get" class="mt-6 flex flex-col sm:flex-row gap-4 items-center justify-center">
                    <input type="text" name="specialty" value="<?php echo htmlspecialchars($specialty); ?>" placeholder="Specialization (e.g., Criminal Law)" class="w-full sm:w-1/2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 shadow-sm">
                    <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location (e.g., New York)" class="w-full sm:w-1/2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 shadow-sm">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500">
                        Search
                    </button>
                </form>
            </div>

            <hr class="mb-8 border-gray-300">

            <?php if (empty($lawyers)): ?>
                <div class="bg-blue-50 p-6 rounded-lg shadow-md text-center text-gray-600">
                    <p class="text-lg font-semibold">No lawyers found</p>
                    <p class="mt-2">We couldn't find any lawyers matching your criteria. Please try a different search or browse our directory.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($lawyers as $lawyer): ?>
                        <div class="lawyer-card bg-white p-6 rounded-xl shadow-lg border border-gray-200 flex flex-col items-center text-center transition-transform hover:scale-105 duration-300">
                            <?php
                                $image_src = !empty($lawyer['profile_image']) ? 'uploads/' . htmlspecialchars($lawyer['profile_image']) : 'images/default-user.png';
                            ?>
                            <img src="<?php echo $image_src; ?>" alt="Lawyer Profile Picture" class="w-28 h-28 rounded-full mb-4 object-cover border-4 border-blue-400">
                            <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($lawyer['name']); ?></h3>
                            <p class="text-sm text-blue-600 font-semibold mt-1 mb-2"><?php echo htmlspecialchars($lawyer['specialty']); ?></p>
                            <div class="text-gray-500 text-sm mb-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span><?php echo htmlspecialchars($lawyer['location']); ?></span>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed mb-4"><?php echo htmlspecialchars($lawyer['bio']); ?></p>
                            <a href="lawyer_profile.php?id=<?php echo $lawyer['id']; ?>" class="mt-auto w-full py-2 px-4 bg-gray-900 text-white font-bold rounded-lg shadow-md hover:bg-gray-700 transition duration-300">
                                View Profile
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
