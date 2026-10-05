<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Helper function for fetching section
function getSection($conn, $section_name) {
    $stmt = $conn->prepare("SELECT * FROM homepage_sections WHERE section_name = ? LIMIT 1");
    $stmt->bind_param("s", $section_name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Fetch all sections
$hero = getSection($conn, 'hero');
$about = getSection($conn, 'about');
$testimonials = $conn->query("SELECT * FROM homepage_sections WHERE section_name LIKE 'testimonial_%'");
$section1 = getSection($conn, 'section1');
$section2 = getSection($conn, 'section2');
$section3 = getSection($conn, 'section3');
$cta = getSection($conn, 'cta');

// Fetch all lawyers (for logged-in users only)
$lawyers = [];
if (isset($_SESSION['user_id'])) {
    $lawyers_sql = "SELECT id, name, specialty, profile_image, bio FROM lawyers ORDER BY name ASC";
    $lawyers_result = $conn->query($lawyers_sql);
    if ($lawyers_result) {
        while ($row = $lawyers_result->fetch_assoc()) {
            $lawyers[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lawyer Website</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
      body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f8f9fa; 
        color: #212529; 
        scroll-behavior: smooth;
      }
      h2, h3 { 
        font-family: 'Merriweather', serif; 
        color: #212529;
      }
      h1{
         font-family: 'Merriweather', serif; 
        color: #f8f9fa;
      }
      .hero-section {
          background-image: url('<?= $hero['image'] ?? "images/backgroung.jpg" ?>');
          background-size: cover; 
          background-position: center; 
          background-attachment: fixed;
          height: 90vh;
          display: flex; 
          justify-content: center; 
          align-items: center; 
          position: relative;
          color: white;
      }
      .hero-section::before {
          content: ""; 
          position: absolute; 
          top: 0; left: 0; 
          width: 100%; height: 100%;
          background: rgba(0, 0, 0, 0.6); 
          z-index: -1;
      }
      .hover-animate {
          transition: all 0.3s ease-in-out;
      }
      .hover-animate:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
  </style>
</head>
<body>

    <!-- HERO -->
    <section class="hero-section text-white relative px-4 py-16 md:py-24" data-aos="fade-zoom-in" data-aos-duration="1200">
        <div class="z-10 text-center max-w-7xl mx-auto">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold mb-4 tracking-wide"><?= $hero['heading'] ?? "Your Trusted Legal Partner" ?></h1>
            <p class="text-base sm:text-lg md:text-xl mb-8 max-w-3xl mx-auto"><?= $hero['content'] ?? "Providing expert legal services with integrity and dedication." ?></p>
            <a href="<?= $hero['button_link'] ?? "book_appointment.php" ?>" class="inline-block bg-white text-[#0a192f] font-bold py-3 px-8 sm:py-4 sm:px-10 rounded-full shadow-lg hover:bg-gray-200 transition-all duration-300 hover-animate">
                <?= $hero['button_text'] ?? "Request a Free Consultation" ?>
            </a>
        </div>
    </section>
    
    <!-- ABOUT -->
    <section class="py-16 md:py-24 bg-white w-full px-4 md:px-6" data-aos="fade-up" data-aos-duration="1000">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            <img src="<?= $about['image'] ?? "images/firm.jpg" ?>" alt="Lawyer Team" class="rounded-xl shadow-2xl w-full h-[300px] md:h-[450px] object-cover hover-animate">
            <div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 md:mb-6"><?= $about['heading'] ?? "About Our Firm" ?></h2>
                <p class="text-base sm:text-lg mb-6 md:mb-8 leading-relaxed"><?= $about['content'] ?? "With decades of combined experience, our team provides trusted legal counsel." ?></p>
                <a href="<?= $about['button_link'] ?? "aboutus.php" ?>" class="inline-block bg-gray-800 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-gray-700 transition-all duration-300 hover-animate">
                    <?= $about['button_text'] ?? "See Details" ?>
                </a>
            </div>
        </div>
    </section>

    <!-- LAWYERS (Visible only if logged in) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <section class="py-16 md:py-24 bg-[#f1f3f5] w-full px-4 md:px-6 lawyers-section" data-aos="fade-up" data-aos-duration="1000">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-8 md:mb-16">Our Expert Lawyers</h2>
            <?php if (!empty($lawyers)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
                    <?php foreach ($lawyers as $lawyer): ?>
                        <div class="bg-white rounded-xl p-6 sm:p-8 shadow-xl text-center flex flex-col items-center hover-animate" data-aos="zoom-in" data-aos-delay="150">
                            <?php 
                                $image_path = !empty($lawyer['profile_image']) ? 'uploads/' . htmlspecialchars($lawyer['profile_image']) : 'images/default.jpg';
                            ?>
                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($lawyer['name']) ?>" class="w-24 h-24 sm:w-32 sm:h-32 rounded-full mx-auto mb-4 object-cover border-2 sm:border-4 border-gray-300">
                            <h3 class="text-lg sm:text-xl font-semibold mb-1"><?= htmlspecialchars($lawyer['name']) ?></h3>
                            <p class="text-blue-600 text-sm italic mb-4"><?= htmlspecialchars($lawyer['specialty']) ?></p>
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-left line-clamp-3">
                                <?= htmlspecialchars(substr($lawyer['bio'], 0, 150)) . (strlen($lawyer['bio']) > 150 ? '...' : '') ?>
                            </p>
                            <a href="lawyer_profile.php?id=<?= $lawyer['id'] ?>" class="mt-4 inline-block bg-[#0a192f] text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-800 transition-all duration-300 hover-animate">View Profile</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No lawyers found at this time.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONIALS -->
    <section class="py-16 md:py-24 bg-white w-full px-4 md:px-6" data-aos="fade-up" data-aos-duration="1000">
        <div class="max-w-7xl mx-auto text-center">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12 mb-12 md:mb-16">
                <?php while($row = $testimonials->fetch_assoc()): ?>
                    <div class="bg-[#f8f9fa] rounded-xl p-6 sm:p-8 shadow-xl text-center hover-animate" data-aos="flip-left">
                        <img src="<?= $row['image'] ?? 'images/default.jpg' ?>" alt="<?= $row['heading'] ?>" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-4 sm:mb-6 object-cover border-2 sm:border-4 border-gray-300">
                        <h3 class="text-lg sm:text-xl font-semibold mb-2"><?= $row['heading'] ?></h3>
                        <p class="text-gray-500 text-sm italic mb-4"><?= $row['button_text'] ?></p>
                        <p class="text-gray-700 text-sm sm:text-base leading-relaxed">"<?= $row['content'] ?>"</p>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div class="bg-[#f8f9fa] rounded-xl p-6 sm:p-8 shadow-xl text-center hover-animate" data-aos="fade-up">
                    <i class="fa-solid fa-gavel text-4xl sm:text-5xl text-blue-600 mb-4 sm:mb-6"></i>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2">Expert Legal Advice</h3>
                    <p class="text-gray-600 text-sm">Professional guidance for all your legal needs with utmost integrity.</p>
                </div>
                <div class="bg-[#f8f9fa] rounded-xl p-6 sm:p-8 shadow-xl text-center hover-animate" data-aos="fade-up" data-aos-delay="150">
                    <i class="fa-solid fa-scale-balanced text-4xl sm:text-5xl text-blue-600 mb-4 sm:mb-6"></i>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2">Fair Representation</h3>
                    <p class="text-gray-600 text-sm">Ensuring justice and fair outcomes for every client we represent.</p>
                </div>
                <div class="bg-[#f8f9fa] rounded-xl p-6 sm:p-8 shadow-xl text-center hover-animate" data-aos="fade-up" data-aos-delay="300">
                    <i class="fa-solid fa-handshake text-4xl sm:text-5xl text-blue-600 mb-4 sm:mb-6"></i>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2">Trusted Relationships</h3>
                    <p class="text-gray-600 text-sm">Building long-term trust with our clients through transparency and dedication.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1 -->
    <section class="py-16 md:py-24 bg-[#f1f3f5] w-full px-4 md:px-6" data-aos="fade-right">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            <div class="order-1 md:order-2"><img src="<?= $section1['image'] ?? "images/aterny.jpg" ?>" alt="Legal Expertise" class="rounded-xl shadow-2xl w-full h-[300px] md:h-[450px] object-cover hover-animate"></div>
            <div class="order-2 md:order-1">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 md:mb-6"><?= $section1['heading'] ?? "Experienced Attorneys" ?></h2>
                <p class="text-base sm:text-lg mb-6 md:mb-8 leading-relaxed"><?= $section1['content'] ?? "Our team consists of highly skilled lawyers with decades of combined experience." ?></p>
                <a href="<?= $section1['button_link'] ?? "aboutus.php" ?>" class="inline-block bg-gray-800 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-gray-700 transition-all duration-300 hover-animate"><?= $section1['button_text'] ?? "Read More" ?></a>
            </div>
        </div>
    </section>

    <!-- SECTION 2 -->
    <section class="py-16 md:py-24 bg-white w-full px-4 md:px-6" data-aos="fade-left">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            <div><img src="<?= $section2['image'] ?? "images/client.jpg" ?>" alt="Client Support" class="rounded-xl shadow-2xl w-full h-[300px] md:h-[450px] object-cover hover-animate"></div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 md:mb-6"><?= $section2['heading'] ?? "Client-Centered Approach" ?></h2>
                <p class="text-base sm:text-lg mb-6 md:mb-8 leading-relaxed"><?= $section2['content'] ?? "We prioritize open communication and transparency." ?></p>
                <a href="<?= $section2['button_link'] ?? "service.php" ?>" class="inline-block bg-gray-800 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-gray-700 transition-all duration-300 hover-animate"><?= $section2['button_text'] ?? "View Services" ?></a>
            </div>
        </div>
    </section>

    <!-- SECTION 3 -->
    <section class="py-16 md:py-24 bg-[#f1f3f5] w-full px-4 md:px-6" data-aos="fade-up">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            <div class="order-1 md:order-2"><img src="<?= $section3['image'] ?? "images/court.jpg" ?>" alt="Court Representation" class="rounded-xl shadow-2xl w-full h-[300px] md:h-[450px] object-cover hover-animate"></div>
            <div class="order-2 md:order-1">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4 md:mb-6"><?= $section3['heading'] ?? "Proven Track Record" ?></h2>
                <p class="text-base sm:text-lg mb-6 md:mb-8 leading-relaxed"><?= $section3['content'] ?? "From small cases to complex litigation, we deliver results." ?></p>
                <a href="<?= $section3['button_link'] ?? "contactus.php" ?>" class="inline-block bg-gray-800 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-gray-700 transition-all duration-300 hover-animate"><?= $section3['button_text'] ?? "Contact Us" ?></a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-white py-16 md:py-24 text-center px-4 md:px-6" data-aos="zoom-in">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 md:mb-6"><?= $cta['heading'] ?? "Need Legal Help?" ?></h2>
            <p class="mb-8 md:mb-10 text-base sm:text-lg text-gray-700 max-w-2xl mx-auto leading-relaxed"><?= $cta['content'] ?? "Schedule your free consultation today." ?></p>
            <a href="<?= $cta['button_link'] ?? "contactus.php" ?>" class="inline-block bg-[#0a192f] text-white px-8 py-3 sm:px-10 sm:py-4 rounded-full font-bold hover:bg-gray-800 transition-all duration-300 hover-animate"><?= $cta['button_text'] ?? "Contact Us" ?></a>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<!-- AOS Animation Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
</body>
</html>
