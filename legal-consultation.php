<?php // Legal Consultation - Enhanced Page ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Legal Consultation — Premier Law Chambers</title>
  <style>
    :root{
      --bg:#0b0b0b;
      --panel:rgba(20,20,20,0.85);
      --muted:#c0c0c0;
      --accent:#fff;
      --radius:18px;
      --shadow:0 8px 30px rgba(0,0,0,0.7);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:"Segoe UI",Inter,Arial,sans-serif;
      background:linear-gradient(160deg,#0b0b0b,#1a1a1a,#000);
      color:var(--accent);
      line-height:1.7;
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:30px;
    }
    .container{
      max-width:900px;
      padding:40px;
      background:var(--panel);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      backdrop-filter:blur(10px);
      animation:fadeIn .8s ease;
    }
    h1{
      text-align:center;
      font-size:36px;
      margin-bottom:12px;
    }
    h2{
      text-align:center;
      font-size:20px;
      color:var(--muted);
      margin-bottom:28px;
    }
    p{margin-bottom:18px;font-size:16px;color:#e8e8e8}
    strong{color:#fff}
    ul{margin:20px 0 25px 25px}
    li{margin-bottom:10px;position:relative;padding-left:14px}
    li::before{
      content:"▹";
      position:absolute;left:-10px;
      color:#fff;
    }
    .btn{
      display:inline-block;
      margin-top:20px;
      padding:12px 22px;
      border-radius:10px;
      background:#fff;
      color:#000;
      text-decoration:none;
      font-weight:600;
      font-size:15px;
      transition:.3s;
      box-shadow:0 4px 15px rgba(255,255,255,0.2);
    }
    .btn:hover{
      background:#f0f0f0;
      transform:translateY(-3px);
      box-shadow:0 6px 20px rgba(255,255,255,0.3);
    }
    @keyframes fadeIn{
      from{opacity:0;transform:translateY(20px)}
      to{opacity:1;transform:translateY(0)}
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>📋 Legal Consultation</h1>
    <h2>Clarity and Guidance You Can Trust</h2>
    <p>
      Our legal consultation services empower you to make informed decisions 
      with confidence. We provide practical guidance on a wide range of matters.
    </p>
    <p><strong>Consultation Areas:</strong></p>
    <ul>
      <li>Risk assessments and compliance reviews</li>
      <li>Corporate and individual advisory sessions</li>
      <li>Legal audits and due diligence</li>
      <li>Contractual obligations and liabilities</li>
      <li>Preventive legal strategies</li>
    </ul>
    <p>
      Every consultation is tailored to your needs, providing clear, 
      actionable advice that saves time and reduces risk.
    </p>
    <a href="service.php" class="btn">← Back to Services</a>
  </div>
</body>
</html>
