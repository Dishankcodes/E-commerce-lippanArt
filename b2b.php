<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>B2B & Bulk Orders | Auraloom</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg-dark:#0f0d0b;
  --bg-soft:#171411;
  --text-main:#f3ede7;
  --text-muted:#b9afa6;
  --accent:#c46a3b;
  --border-soft:rgba(255,255,255,.12);
}

/* RESET */
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background:var(--bg-dark);
  color:var(--text-main);
}

/* ================= HEADER ================= */
header{
  position:fixed;
  top:0;
  width:100%;
  height:72px;
  z-index:1000;
  background:rgba(15,13,11,.85);
  backdrop-filter:blur(10px);
  border-bottom:1px solid var(--border-soft);
  display:grid;
  grid-template-columns:auto 1fr auto;
  align-items:center;
  padding:0 80px;
}

.logo{
  font-family:'Playfair Display',serif;
  font-size:28px;
  letter-spacing:2px;
}

nav{
  display:flex;
  justify-content:center;
  gap:34px;
}

nav a{
  font-size:13px;
  letter-spacing:1.5px;
  text-transform:uppercase;
  color:var(--text-muted);
  position:relative;
  padding-bottom:6px;
}

nav a::after{
  content:"";
  position:absolute;
  left:0;
  bottom:0;
  width:0;
  height:1px;
  background:var(--accent);
  transition:.35s;
}

nav a:hover::after,
nav a.active::after{width:100%}
nav a:hover,
nav a.active{color:var(--text-main)}

.header-btn{
  padding:10px 22px;
  background:var(--accent);
  color:#fff;
  font-size:13px;
}

/* ================= PAGE WRAP ================= */
.page-wrap{padding-top:140px}

/* ================= HERO ================= */
.hero{
  padding:120px 80px;
  background:
    radial-gradient(circle at right, rgba(196,106,59,.18), transparent 60%),
    linear-gradient(to bottom,var(--bg-dark),var(--bg-soft));
}

.hero h1{
  font-family:'Playfair Display',serif;
  font-size:52px;
  max-width:700px;
  margin-bottom:24px;
}

.hero p{
  color:var(--text-muted);
  font-size:17px;
  max-width:620px;
  line-height:1.7;
  margin-bottom:40px;
}

.hero a{
  display:inline-block;
  padding:14px 36px;
  background:var(--accent);
  color:#fff;
  font-size:13px;
  letter-spacing:1px;
}

/* ================= WHY B2B ================= */
.section{
  padding:120px 80px;
}

.section h2{
  font-family:'Playfair Display',serif;
  font-size:40px;
  margin-bottom:20px;
}

.section p{
  color:var(--text-muted);
  max-width:650px;
  margin-bottom:60px;
}

.why-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:40px;
}

.why-card{
  border-top:1px solid var(--border-soft);
  padding-top:30px;
}

.why-card h3{
  font-family:'Playfair Display',serif;
  font-size:22px;
  margin-bottom:10px;
}

/* ================= USE CASES ================= */
.use-cases{
  background:linear-gradient(to bottom,var(--bg-soft),var(--bg-dark));
}

.case-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:40px;
}

.case{
  padding:30px;
  border:1px solid var(--border-soft);
  background:#1b1815;
}

.case h4{
  font-family:'Playfair Display',serif;
  font-size:20px;
  margin-bottom:10px;
}

/* ================= PROCESS ================= */
.process{
  background:var(--bg-dark);
}

.process-steps{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:40px;
}

.step{
  border-left:2px solid var(--accent);
  padding-left:20px;
}

.step span{
  color:var(--accent);
  font-size:13px;
  letter-spacing:2px;
}

/* ================= CTA ================= */
.cta{
  text-align:center;
  padding:120px 40px;
  background:
    radial-gradient(circle at center, rgba(196,106,59,.25), transparent 60%);
}

.cta h2{
  font-family:'Playfair Display',serif;
  font-size:42px;
  margin-bottom:20px;
}

.cta a{
  display:inline-block;
  margin-top:30px;
  padding:16px 42px;
  background:var(--accent);
  color:#fff;
  font-size:14px;
  letter-spacing:2px;
}

/* ================= RESPONSIVE ================= */
@media(max-width:900px){
  header{padding:0 30px}
  nav{display:none}
  .hero,.section{padding:80px 30px}
  .hero h1{font-size:36px}
}
</style>
</head>

<body>

<header>
  <div class="logo">AURALOOM</div>

  
  <nav>
    <a href="index.php">Home</a>
    <a href="collection.php">Collections</a>
    <a href="custom-order.php">Custom</a>
    <a href="b2b.php" class="active">B2B</a>
    <a href="about-us.php">About Us</a>
    <a href="contact.php">Contact</a>
  </nav>

  <a href="enquire.php" class="header-btn">Enquire</a>
</header>

<div class="page-wrap">
<?php if (isset($_GET['success'])): ?>
  <div style="
    max-width:900px;
    margin:0 auto 60px auto;
    padding:22px 26px;
    border:1px solid rgba(125,216,125,.35);
    background:rgba(125,216,125,.12);
    color:#7dd87d;
    font-size:15px;
    border-radius:14px;
    line-height:1.6;
  ">
    ✅ <strong>Your B2B enquiry has been successfully submitted.</strong><br><br>

    Our team has received your request and will contact you shortly with
    pricing, timelines, and design options.

    <div style="margin-top:18px">
      <a href="https://wa.me/919876543210?text=Hi, I just submitted a B2B enquiry on Auraloom."
         style="
           display:inline-block;
           padding:10px 22px;
           background:#25D366;
           color:#fff;
           font-size:13px;
           margin-right:12px;
         ">
        💬 WhatsApp Us
      </a>

      <a href="index.php"
         style="
           display:inline-block;
           padding:10px 22px;
           border:1px solid rgba(255,255,255,.25);
           color:#f3ede7;
           font-size:13px;
         ">
        Back to Home
      </a>
    </div>
  </div>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
  <h1>Art for Businesses & Large Spaces</h1>
  <p>
    Auraloom partners with cafés, hotels, offices, architects and developers
    to create handcrafted Lippan art installations that elevate spaces.
  </p>
  <a href="enquire.php">Start a B2B Project</a>
</section>

<!-- WHY -->
<section class="section">
  <h2>Why Choose Auraloom for B2B</h2>
  <p>
    From concept to installation, we offer end-to-end handcrafted solutions
    tailored for commercial environments.
  </p>

  <div class="why-grid">
    <div class="why-card">
      <h3>Custom Scale</h3>
      <p>Large format artworks, feature walls & branding elements.</p>
    </div>
    <div class="why-card">
      <h3>Reliable Timelines</h3>
      <p>Planned production & delivery for business schedules.</p>
    </div>
    <div class="why-card">
      <h3>Brand Integration</h3>
      <p>Colors, motifs & identity aligned with your brand.</p>
    </div>
    <div class="why-card">
      <h3>Crafted in India</h3>
      <p>Authentic Kutch heritage with a modern finish.</p>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="section use-cases">
  <h2>Who We Work With</h2>

  <div class="case-grid">
    <div class="case">
      <h4>Cafés & Restaurants</h4>
      <p>Statement walls & ambience-defining art.</p>
    </div>
    <div class="case">
      <h4>Hotels & Resorts</h4>
      <p>Lobby features, suites & experiential spaces.</p>
    </div>
    <div class="case">
      <h4>Offices & Studios</h4>
      <p>Reception walls & cultural branding.</p>
    </div>
    <div class="case">
      <h4>Builders & Architects</h4>
      <p>Art integration in premium projects.</p>
    </div>
  </div>
</section>

<!-- PROCESS -->
<section class="section process">
  <h2>Our B2B Process</h2>

  <div class="process-steps">
    <div class="step">
      <span>STEP 01</span>
      <p>Requirement & space understanding</p>
    </div>
    <div class="step">
      <span>STEP 02</span>
      <p>Concept sketches & estimates</p>
    </div>
    <div class="step">
      <span>STEP 03</span>
      <p>Handcrafted production</p>
    </div>
    <div class="step">
      <span>STEP 04</span>
      <p>Delivery & installation support</p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <h2>Let’s Create Something Timeless</h2>
  <a href="enquire.php">Discuss Your B2B Project</a>
</section>

</div>

</body>
</html>
