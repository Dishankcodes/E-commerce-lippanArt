<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us | Auraloom</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

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
a{text-decoration:none;color:inherit}

/* ================= NAVBAR ================= */
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
  letter-spacing:1px;
}
nav{
  display:flex;
  justify-content:center;
  gap:36px;
}
nav a{
  position:relative;
  font-size:13px;
  letter-spacing:1.5px;
  text-transform:uppercase;
  color:var(--text-muted);
  padding-bottom:6px;
}
nav a::after{
  content:"";
  position:absolute;
  left:0;
  bottom:0;
  width:0%;
  height:1px;
  background:var(--accent);
  transition:.35s ease;
}
nav a:hover::after,
nav a.active::after{
  width:100%;
}
nav a:hover,
nav a.active{
  color:var(--text-main);
}
.header-btn{
  padding:10px 22px;
  background:var(--accent);
  color:#fff;
  font-size:13px;
  letter-spacing:1px;
}

/* ================= HERO ================= */
.hero{
  min-height:90vh;
  padding-top:120px;
  background:
    radial-gradient(circle at center, rgba(196,106,59,.18), transparent 60%),
    linear-gradient(to bottom,var(--bg-dark),var(--bg-soft));
  display:flex;
  align-items:center;
  text-align:center;
}
.hero-inner{
  max-width:900px;
  margin:auto;
  padding:0 30px;
}
.hero h1{
  font-family:'Playfair Display',serif;
  font-size:54px;
  margin-bottom:20px;
}
.hero p{
  font-size:18px;
  color:var(--text-muted);
  line-height:1.7;
}

/* ================= STORY ================= */
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
  max-width:760px;
  line-height:1.8;
  font-size:16px;
}

/* ================= VALUES ================= */
.values{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:50px;
  margin-top:60px;
}
.value-card{
  border-top:1px solid var(--border-soft);
  padding-top:30px;
}
.value-card h3{
  font-family:'Playfair Display',serif;
  font-size:22px;
  margin-bottom:10px;
}
.value-card p{
  font-size:14px;
}

/* ================= CRAFT SECTION ================= */
.craft{
  background:linear-gradient(to bottom,var(--bg-soft),var(--bg-dark));
}
.craft-inner{
  max-width:1100px;
  margin:auto;
}
.craft-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:80px;
  align-items:center;
  margin-top:60px;
}
.craft-text h3{
  font-family:'Playfair Display',serif;
  font-size:34px;
  margin-bottom:20px;
}
.craft-text p{
  font-size:16px;
  line-height:1.8;
}
.craft-art img{
  width:260px;
  animation:slowRotate 90s linear infinite;
  filter:drop-shadow(0 35px 60px rgba(0,0,0,.7));
}
@keyframes slowRotate{
  from{transform:rotate(0deg)}
  to{transform:rotate(360deg)}
}

/* ================= CTA ================= */
.cta{
  text-align:center;
  padding:120px 30px;
}
.cta h2{
  font-family:'Playfair Display',serif;
  font-size:40px;
  margin-bottom:20px;
}
.cta a{
  display:inline-block;
  margin-top:20px;
  padding:14px 36px;
  background:var(--accent);
  color:#fff;
  letter-spacing:1px;
}

/* ================= MOBILE ================= */
@media(max-width:900px){
  header{padding:0 30px}
  nav{display:none}
  .section{padding:80px 30px}
  .craft-grid{grid-template-columns:1fr;text-align:center}
  .craft-art img{margin:auto}
  .hero h1{font-size:40px}
}
</style>
</head>

<body>

<header>
  <div class="logo">Auraloom</div>
  <nav>
    <a href="index.php">Home</a>
    <a href="collection.php">Shop</a>
    <a href="custom-order.php">Custom</a>
    <a href="b2b.php">B2B</a>
    <a href="about.php" class="active">About</a>
    <a href="contact.php">Contact</a>
  </nav>
  <a href="cart.php" class="header-btn">Cart</a>
</header>

<section class="hero">
  <div class="hero-inner">
    <h1>Rooted in Tradition.<br>Crafted for Today.</h1>
    <p>
      Auraloom is a celebration of heritage — where the ancient
      Lippan art of Kutch meets contemporary spaces across India and beyond.
    </p>
  </div>
</section>

<section class="section">
  <h2>Our Story</h2>
  <p>
    Born from the salt deserts of Kutch, Lippan art has always been more than decoration —
    it is identity, resilience, and storytelling in clay and mirrors.
    <br><br>
    At Auraloom, we work closely with artisans to preserve this craft while
    reimagining it for modern homes, cafés, hotels, offices, and soulful spaces.
  </p>

  <div class="values">
    <div class="value-card">
      <h3>Authentic Craft</h3>
      <p>Every piece is shaped by hand using natural materials and traditional techniques.</p>
    </div>
    <div class="value-card">
      <h3>Modern Aesthetic</h3>
      <p>Designed to complement contemporary interiors without losing its roots.</p>
    </div>
    <div class="value-card">
      <h3>Custom & B2B</h3>
      <p>From homes to large installations, we create pieces that fit your vision.</p>
    </div>
    <div class="value-card">
      <h3>Slow & Sustainable</h3>
      <p>No mass production — only mindful, handcrafted creation.</p>
    </div>
  </div>
</section>

<section class="section craft">
  <div class="craft-inner">
    <div class="craft-grid">
      <div class="craft-text">
        <h3>Crafted by Hand, Guided by Soul</h3>
        <p>
          Each Auraloom creation passes through skilled hands —
          from raw clay preparation to mirror placement and finishing.
          The result is not just art, but a living piece of culture.
        </p>
      </div>
      <div class="craft-art">
        <img src="a.png" alt="Lippan Art">
      </div>
    </div>
  </div>
</section>

<section class="cta">
  <h2>Let’s Create Something Meaningful</h2>
  <p class="text-muted">Custom designs · B2B projects · One-of-a-kind artworks</p>
  <a href="custom-order.php">Start a Custom Order</a>
</section>

</body>
</html>
