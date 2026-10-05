<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Fetch sections from DB
$query = "SELECT * FROM about_us";
$result = mysqli_query($conn, $query);

$sections = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sections[$row['section_name']] = $row;
    }
}
// Default fallback data
$data = [
    'hero' => [
        'title' => "About Our Law Firm",
        'description' => "We provide reliable legal services that protect our clients' rights.
Our experienced attorneys deliver personalized strategies with professionalism and integrity.",
        'image' => "images/law.jpg"
    ],
    'our_mission' => [
        'title' => "Our Mission",
        'description' => " mission is to provide comprehensive and reliable legal services that protect the rights of our clients and ensure fair outcomes.",
        'image' => "images/our_mission.jpg"
    ],
    'who_we_are' => [
        'title' => "Who We Are",
        'description' => "We are a professional law firm with years of experience providing reliable legal services. Our team of expert lawyers specializes in criminal law, corporate cases, family law, and civil disputes.",
        'image' => "images/team.jpeg"
    ],
    'why_choose_us' => [
        'title' => "Why Choose Us",
        'description' => "We go beyond legal expertise by offering personalized attention, transparent guidance, and a results-driven approach.",
        'image' => "images/service.jpg"
    ]
];

// Overwrite defaults with DB values
foreach ($data as $key => $default) {
    if (isset($sections[$key])) {
        $data[$key]['title'] = $sections[$key]['title'] ?? $default['title'];
        $data[$key]['description'] = $sections[$key]['description'] ?? $default['description'];
        if (!empty($sections[$key]['image'])) {
            $data[$key]['image'] = "images/" . $sections[$key]['image'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Inter', sans-serif;
}

/* Hero Section */
.about-hero {
    height: 80vh;
    background-image: url('<?php echo $data['hero']['image']; ?>');
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #fff;
}
.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(26, 32, 44, 0.75); /* dark overlay */
    z-index: 1;
}
.about-hero .content {
    position: relative;
    z-index: 2;
}
.about-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 4rem;
    font-weight: 700;
}
.about-hero p {
    font-size: 1.5rem;
    margin-top: 1rem;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}
.about-hero a {
    margin-top: 2rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Feature Cards */
.feature-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}
.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
.feature-card i {
    font-size: 3rem;
    color: #b87333;
    margin-bottom: 1rem;
    transition: color 0.3s;
}
.feature-card:hover i {
    color: #1a202c;
}
</style>
</head>
<body>

<!-- ✅ Hero -->
<section class="about-hero" data-aos="fade-up" data-aos-duration="1500">
    <div class="content px-4">
        <h1><?php echo $data['hero']['title']; ?></h1>
        <p class="mt-4"><?php echo $data['hero']['description']; ?></p>
        <a href="contactus.php" class="inline-block bg-[#b87333] text-white font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-[#a0662d] transition-colors duration-300">
            Contact Us
        </a>
    </div>
</section>

<!-- ✅ Section 1 Light -->
<section class="py-16 bg-gray-100 text-gray-800">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        <div data-aos="fade-right">
            <h2 class="text-4xl font-bold mb-4 font-playfair-display text-black">
                <?php echo $data['our_mission']['title']; ?>
            </h2>
            <p class="text-gray-700 leading-relaxed">

                <?php echo $data['our_mission']['description']; ?>

            </p>
        </div>
        <div data-aos="fade-left">
            <img src="<?php echo $data['our_mission']['image']; ?>" alt="Mission" class="rounded-lg shadow-lg w-full h-80 object-cover">
        
        </div>
    </div>
</section>


<!-- ✅ Section 2 Dark -->
<?php
$data['who_we_are']['image'] = "https://media.istockphoto.com/id/1227094223/photo/diverse-employees-team-engaged-in-teamwork-in-modern-boardroom.jpg?s=612x612&w=0&k=20&c=5obt-CMgklV7_5dqUGgna33pBe8PsJVauJHXmXTQVdE=";
$data['who_we_are']['title'] = "Who We Are";
$data['who_we_are']['description'] = "We are a dedicated team of professional lawyers committed to providing exceptional legal services.";
?>

<section class="py-16 bg-[#1a202c] text-white">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        <div class="order-2 md:order-1" data-aos="fade-right">
            <h2 class="text-4xl font-bold mb-4 font-playfair-display text-white">
                <?php echo $data['who_we_are']['title']; ?>
            </h2>
            <p class="text-white leading-relaxed">
                <?php echo $data['who_we_are']['description']; ?>
            </p>
        </div>

        <div class="order-1 md:order-2" data-aos="fade-left">
            <img 
                src="<?php echo $data['who_we_are']['image']; ?>" 
                alt="Who We Are" 
                class="rounded-lg shadow-lg w-full h-80 object-cover transition-transform duration-700 "
            >
        </div>
    </div>
</section>

<!-- ✅ Section 3 Gray -->
<section class="py-16 bg-gray-100 text-gray-800">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-4 font-playfair-display"><?php echo $data['why_choose_us']['title']; ?></h2>
        <p class="text-gray-600 mb-12 max-w-3xl mx-auto"><?php echo $data['why_choose_us']['description']; ?></p>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                <i class="fas fa-balance-scale"></i>
                <h5 class="font-semibold text-xl mt-4">Integrity & Ethics</h5>
                <p class="text-gray-500 mt-2">Upholding the highest standards of professional conduct and integrity.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                <i class="fas fa-handshake"></i>
                <h5 class="font-semibold text-xl mt-4">Personalized Service</h5>
                <p class="text-gray-500 mt-2">Tailoring our legal approach to meet your unique needs and goals.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="500">
                <i class="fas fa-gavel"></i>
                <h5 class="font-semibold text-xl mt-4">Unwavering Advocacy</h5>
                <p class="text-gray-500 mt-2">Fighting tirelessly to protect your rights and achieve the best outcome.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 1000, once: true });</script>
</body>
</html>
