<?php
session_start();
include "includes/db.php";
include "includes/header.php";

// Fetch services
$res = $conn->query("SELECT * FROM services ORDER BY id DESC");
$services = [];
if ($res) { 
    while($r = $res->fetch_assoc()) $services[] = $r; 
    $res->free(); 
}

$extra_services = [
  ['title'=>'Intellectual Property', 'subtitle'=>'Protecting Innovations', 'description'=>'Legal support for patents, trademarks, and copyright protection.','expertise'=>'Patents, Trademarks, Copyrights','icon'=>'fas fa-lightbulb'],
  ['title'=>'Immigration Law', 'subtitle'=>'Smooth Immigration Solutions', 'description'=>'Comprehensive immigration and visa assistance for individuals and businesses.','expertise'=>'Visas, Residency, Citizenship','icon'=>'fas fa-plane']
];

$services = array_merge($extra_services, $services);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Legal Services — Lawyer Firm</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
:root{
  --bg:#f8f8f8;
  --panel:#fff;
  --text:#111;
  --muted:#555;
  --border:#ddd;
  --accent:#111;
  --hover-tag-bg:#111;
  --hover-tag-text:#fff;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.7;overflow-x:hidden;}
.wrap{max-width:1200px;margin:0 auto;padding:60px 20px;}
.service-header {
  position: relative;
  background: url('images/service.jpg') no-repeat center center;
  background-size: cover;
  height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.service-header .overlay {
  background: rgba(0,0,0,0.55);
  color: #fff;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  animation: fadeIn 1.5s ease;
}
.service-header h1 {
  font-size: 36px;
  margin-bottom: 15px;
  color: #fff;
}
.service-header p.lead {
  color: #eee;
  font-size: 18px;
  max-width: 700px;
  margin: 0 auto;
}

/* Grid layout */
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:30px;}
.card{
  background:var(--panel);
  border-radius:16px;
  padding:25px;
  box-shadow:0 4px 10px rgba(0,0,0,0.06);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  transition: all 0.4s ease;
  border:1px solid var(--border);
  opacity:0;
  transform:translateY(40px);
}
.card.reveal{
  opacity:1;
  transform:translateY(0);
}
.card:hover{
  transform:translateY(-8px) scale(1.03);
  box-shadow:0 12px 25px rgba(0,0,0,0.12);
}
.card i.icon{
  font-size:32px;
  color:var(--accent);
  margin-bottom:15px;
  transition:all 0.4s ease;
}
.card:hover i.icon{color:#ff4c00; transform:scale(1.2);}
.card h3{font-size:22px;margin-bottom:8px;color:var(--accent);}
.card h4{font-size:16px;margin-bottom:12px;color:var(--muted);}
.card p{font-size:15px;color:var(--text);margin-bottom:15px;}
.expertise{display:flex;flex-wrap:wrap;margin-top:auto;}
.expertise span{
  display:inline-block;
  margin:5px 5px 0 0;
  padding:6px 14px;
  border-radius:25px;
  border:1px solid #ddd;
  font-size:13px;
  background:#fafafa;
  color:var(--text);
  transition:0.3s ease;
}
.card:hover .expertise span{
  background:var(--hover-tag-bg);
  color:var(--hover-tag-text);
  border-color:var(--hover-tag-bg);
}
.expertise span:hover{transform:scale(1.08);}
.achievements{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:25px;margin-top:70px;text-align:center;}
.ach{background:var(--panel);padding:25px;border-radius:16px;box-shadow:0 4px 10px rgba(0,0,0,0.06);opacity:0;transform:scale(0.8);transition:all 0.8s ease;}
.ach.reveal{opacity:1;transform:scale(1);}
.ach h3{font-size:28px;margin-bottom:8px;color:var(--accent);}
.ach p{font-size:14px;color:var(--muted);}
.point-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:25px;margin-top:40px;}
.point{
  background:var(--panel);
  padding:30px 25px;
  border-radius:16px;
  box-shadow:0 4px 12px rgba(0,0,0,0.08);
  text-align:center;
  transition:all 0.4s ease;
  border:1px solid #eee;
  opacity:0;
  transform:translateY(40px);
}
.point.reveal{opacity:1;transform:translateY(0);}
.point:hover{
  transform:translateY(-8px) scale(1.04);
  box-shadow:0 12px 28px rgba(0,0,0,0.12);
}
.point .icon-circle{
  width:70px;height:70px;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 15px;
  font-size:28px;
  color:#fff;
}
.point h3{font-size:20px;margin-bottom:10px;color:var(--accent);}
.point p{font-size:14px;color:var(--muted);line-height:1.6;}
.testimonial-section{margin-top:70px;}
.testimonial-section h2{text-align:center;margin-bottom:40px;color:var(--accent);}
.testimonial-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:25px;}
.testimonial-card{
  background:var(--panel);
  padding:25px;
  border-radius:16px;
  box-shadow:0 4px 12px rgba(0,0,0,0.06);
  transition:all 0.4s ease;
  position:relative;
  opacity:0;
  transform:translateY(40px);
}
.testimonial-card.reveal{opacity:1;transform:translateY(0);}
.testimonial-card:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 12px 25px rgba(0,0,0,0.1);}
.testimonial-card p{font-size:14px;color:var(--muted);margin-bottom:10px;}
.testimonial-card h4{font-size:16px;color:var(--accent);}
.testimonial-card span.stars{color:#ffb400;font-size:14px;}
.core-values{margin-top:80px;background:#111;color:#fff;padding:60px 20px;border-radius:16px;opacity:0;transform:translateY(50px);transition:all 1s ease;}
.core-values.reveal{opacity:1;transform:translateY(0);}
.core-values h2{text-align:center;font-size:28px;margin-bottom:40px;}
.values-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:30px;max-width:1000px;margin:0 auto;}
.value-card{background:#222;padding:25px;border-radius:12px;text-align:center;transition:0.3s;}
.value-card i{font-size:30px;margin-bottom:15px;display:block;}
.value-card h3{margin-bottom:10px;font-size:18px;}
.value-card p{font-size:14px;color:#bbb;}
.value-card:hover{transform:translateY(-6px) scale(1.05);box-shadow:0 8px 20px rgba(255,255,255,0.1);}
@keyframes fadeIn{
  from{opacity:0;transform:translateY(40px);}
  to{opacity:1;transform:translateY(0);}
}
</style>
</head>
<body>
<header class="service-header">
  <div class="overlay">
    <h1> Our Legal Services</h1>
    <p class="lead">Expert services protecting your rights and ensuring corporate compliance.</p>
  </div>
</header>

<div class="wrap">
<?php if(empty($services)): ?>
  <p style="text-align:center;color:#999;">No services available right now.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach($services as $s): ?>
      <div class="card">
        <i class="icon <?= isset($s['icon'])?$s['icon']:'fas fa-gavel'; ?>"></i>
        <h3><?=htmlspecialchars($s['title']);?></h3>
        <h4><?=htmlspecialchars($s['subtitle']);?></h4>
        <p><?=nl2br(htmlspecialchars($s['description']));?></p>
        <?php if(!empty($s['expertise'])): ?>
          <div class="expertise">
            <?php
              $tags = array_filter(array_map('trim', explode(',', $s['expertise'])));
              foreach($tags as $t) echo "<span>".htmlspecialchars($t)."</span>";
            ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<section>
  <h2 style="text-align:center;margin-top:70px;">Our Achievements</h2>
  <div class="achievements">
    <div class="ach"><h3 data-count="500">0</h3><p>Cases Won</p></div>
    <div class="ach"><h3 data-count="300">0</h3><p>Corporate Clients</p></div>
    <div class="ach"><h3 data-count="20">0</h3><p>Years of Experience</p></div>
    <div class="ach"><h3 data-count="95">0</h3><p>Client Satisfaction %</p></div>
  </div>
</section>

<section>
  <h2 style="text-align:center;margin-top:70px;">Why Choose Our Firm?</h2>
  <div class="point-grid">
    <div class="point"><div class="icon-circle" style="background:linear-gradient(135deg,#ff7e5f,#feb47b);"><i class="fas fa-gavel"></i></div><h3>⚖️ Experience</h3><p>20+ years of combined legal practice across diverse areas of law.</p></div>
    <div class="point"><div class="icon-circle" style="background:linear-gradient(135deg,#43cea2,#185a9d);"><i class="fas fa-search"></i></div><h3>🔍 Transparency</h3><p>Clear communication and complete visibility at every stage of your case.</p></div>
    <div class="point"><div class="icon-circle" style="background:linear-gradient(135deg,#ff512f,#dd2476);"><i class="fas fa-shield-alt"></i></div><h3>🛡️ Strong Advocacy</h3><p>Relentless courtroom representation tailored to protect your interests.</p></div>
    <div class="point"><div class="icon-circle" style="background:linear-gradient(135deg,#36d1dc,#5b86e5);"><i class="fas fa-hand-holding-heart"></i></div><h3>🤝 Personalized Care</h3><p>Every client is unique. We provide strategies that reflect your situation.</p></div>
  </div>
</section>

<section class="testimonial-section">
  <h2>What Our Clients Say</h2>
  <div class="testimonial-grid">
    <div class="testimonial-card"><p>"Excellent service! My case was handled professionally and with care."</p><h4>John Doe</h4><span class="stars">★★★★★</span></div>
    <div class="testimonial-card"><p>"Highly recommend this firm for corporate legal services."</p><h4>Sarah Williams</h4><span class="stars">★★★★☆</span></div>
    <div class="testimonial-card"><p>"They made the immigration process smooth and stress-free."</p><h4>Ali Khan</h4><span class="stars">★★★★★</span></div>
  </div>
</section>

<section class="core-values">
  <h2> Our Core Values</h2>
  <div class="values-grid">
    <div class="value-card"><i class="fas fa-handshake" style="color:#ffb400;"></i><h3>Integrity</h3><p>Honest, ethical, and transparent legal services at every step.</p></div>
    <div class="value-card"><i class="fas fa-balance-scale" style="color:#ff4c00;"></i><h3>Justice</h3><p>Committed to upholding justice and protecting client rights.</p></div>
    <div class="value-card"><i class="fas fa-users" style="color:#00c6ff;"></i><h3>Client First</h3><p>Your needs are our priority — we craft strategies for you.</p></div>
    <div class="value-card"><i class="fas fa-lightbulb" style="color:#ffd700;"></i><h3>Innovation</h3><p>Modern solutions with a forward-thinking approach to law.</p></div>
  </div>
</section>

</div>

<script>
// Scroll reveal animation
const observer = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){ e.target.classList.add('reveal'); }
  });
},{threshold:0.15});
document.querySelectorAll('.card, .ach, .point, .testimonial-card, .core-values').forEach(el=>observer.observe(el));

// Achievement counter
const counters=document.querySelectorAll('.ach h3');
const counterObserver=new IntersectionObserver(entries=>{
  entries.forEach(entry=>{
    if(entry.isIntersecting){
      counters.forEach(counter=>{
        let target=parseInt(counter.getAttribute('data-count'));
        let count=0;
        let step=Math.ceil(target/100);
        const update=()=>{
          count+=step;
          if(count>=target) counter.textContent=target;
          else {counter.textContent=count; requestAnimationFrame(update);}
        };
        requestAnimationFrame(update);
      });
    }
  });
},{threshold:0.5});
counterObserver.observe(document.querySelector('.achievements'));
</script>

<?php include "includes/footer.php"; ?>
</body>
</html>
