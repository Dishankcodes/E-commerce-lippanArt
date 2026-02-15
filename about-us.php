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

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg-dark:#0f0d0b;
  --bg-soft:#171411;
  --card-bg:#1b1815;
  --text-main:#f3ede7;
  --text-muted:#b9afa6;
  --accent:#c46a3b;
  --accent-hover:#a85830;
  --border-soft:rgba(255,255,255,.12);
}

/* RESET */
*{margin:0;padding:0;box-sizing:border-box}

body{
  font-family:'Poppins',sans-serif;
  background:var(--bg-dark);
  color:var(--text-main);
  line-height: 1.6;
}

a{text-decoration:none;color:inherit;transition: 0.3s ease;}

/* ================= HEADER (Glass-morphism) ================= */
header {
  position: fixed;
  top: 0;
  width: 100%;
  height: 80px;
  z-index: 1000;
  background: rgba(15, 13, 11, 0.75);
  backdrop-filter: blur(15px);
  border-bottom: 1px solid var(--border-soft);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 80px;
}

.logo {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
  letter-spacing: 1px;
}

nav {
  display: flex;
  gap: 40px;
}

nav a {
  font-size: 12px;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--text-muted);
  position: relative;
  padding-bottom: 5px;
}

nav a:hover, nav a.active { color: var(--text-main); }
nav a::after {
  content: "";
  position: absolute;
  left: 0; bottom: 0;
  width: 0%; height: 1px;
  background: var(--accent);
  transition: 0.4s ease;
}
nav a:hover::after, nav a.active::after { width: 100%; }

.header-btn {
  padding: 12px 24px;
  background: var(--accent);
  color: #fff;
  font-size: 12px;
  letter-spacing: 1px;
  text-transform: uppercase;
  border: none;
  cursor: pointer;
  transition: 0.3s;
}
.header-btn:hover { background: var(--accent-hover); }

/* ================= HERO ================= */
.hero{
  min-height: 85vh;
  padding-top:120px;
  background:
    radial-gradient(circle at center, rgba(196,106,59,.15), transparent 60%),
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
  font-family:'Playfair Display', serif;
  font-size: 52px;
  margin-bottom:20px;
  line-height: 1.2;
}
.hero p{
  font-size: 18px;
  color:var(--text-muted);
  max-width: 650px;
  margin: 0 auto;
}

/* ================= SECTIONS ================= */
.section{
  padding:120px 80px;
}
.section h2{
  font-family:'Playfair Display', serif;
  font-size: 38px;
  margin-bottom: 25px;
  color: var(--text-main);
}
.section p{
  color:var(--text-muted);
  max-width:800px;
  font-size:16px;
  line-height: 1.8;
}

/* ================= VALUES GRID ================= */
.values{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px, 1fr));
  gap:40px;
  margin-top:60px;
}
.value-card{
  background: var(--card-bg);
  border: 1px solid var(--border-soft);
  padding: 40px;
  transition: 0.4s ease;
}
.value-card:hover {
  border-color: var(--accent);
  transform: translateY(-5px);
}
.value-card h3{
  font-family:'Playfair Display', serif;
  font-size: 22px;
  margin-bottom: 15px;
  color: var(--accent);
}
.value-card p{
  font-size:14px;
  color: var(--text-muted);
}

/* ================= CRAFT SECTION ================= */
.craft{
  background: linear-gradient(to bottom, var(--bg-soft), var(--bg-dark));
}
.craft-inner{
  max-width:1200px;
  margin:auto;
}
.craft-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:80px;
  align-items:center;
}
.craft-text h3{
  font-family:'Playfair Display', serif;
  font-size: 34px;
  margin-bottom:20px;
}
.craft-art img{
  width:100%;
  max-width: 280px;
  animation:slowRotate 90s linear infinite;
  filter:drop-shadow(0 35px 60px rgba(0,0,0,.7));
  display: block;
  margin: 0 auto;
}

/* ================= INSTAGRAM SECTION ================= */
.instagram-section {
    background: var(--bg-dark);
    border-top: 1px solid var(--border-soft);
}
.insta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.insta-item {
    aspect-ratio: 1/1;
    background: var(--bg-soft);
    border: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    transition: 0.3s;
}
.insta-item:hover {
    border-color: var(--accent);
}
.insta-item span {
    font-family: 'Playfair Display', serif;
    font-size: 11px;
    letter-spacing: 2px;
    color: var(--accent);
    text-transform: uppercase;
}
.insta-link {
    display: inline-block;
    margin-top: 30px;
    font-size: 13px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-main);
    border-bottom: 1px solid var(--accent);
    padding-bottom: 4px;
}
.insta-link:hover { color: var(--accent); }

@keyframes slowRotate{
  from{transform:rotate(0deg)}
  to{transform:rotate(360deg)}
}

/* ================= CTA ================= */
.cta{
  text-align:center;
  padding:120px 30px;
  background: radial-gradient(circle at center, rgba(196,106,59,.1), transparent 70%);
}
.cta h2{
  font-family:'Playfair Display', serif;
  font-size: 40px;
  margin-bottom:20px;
}
.cta a{
  display:inline-block;
  margin-top:25px;
  padding:16px 45px;
  background:var(--accent);
  color:#fff;
  font-size: 13px;
  letter-spacing:1.5px;
  text-transform: uppercase;
  font-weight: 500;
}
.cta a:hover {
  background: var(--accent-hover);
  transform: translateY(-3px);
}

/* ================= MOBILE ================= */
@media(max-width:900px){
  header{padding: 0 30px; height: 75px;}
  nav{display:none}
  .section{padding:80px 30px}
  .craft-grid{grid-template-columns:1fr; text-align:center; gap: 40px;}
  .craft-art img{width: 220px;}
  .hero h1{font-size:38px}
  .cta h2{font-size: 32px;}
  .insta-grid-wrapper { order: 2; }
}
</style>
</head>

<body>

<header>
  <div class="logo">AURALOOM</div>
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
      Auraloom is a celebration of heritage—where the ancient
      Lippan art of Kutch meets contemporary spaces with soulful craftsmanship.
    </p>
  </div>
</section>

<section class="section">
  <h2>Our Story</h2>
  <p>
    Born from the salt deserts of Kutch, Lippan art has always been more than decoration—it is identity, resilience, and storytelling in clay and mirrors.
    <br><br>
    At Auraloom, we work closely with master artisans to preserve this timeless craft while reimagining it for modern homes, luxury hotels, and artistic spaces.
  </p>

  <div class="values">
    <div class="value-card">
      <h3>Authentic Craft</h3>
      <p>Every piece is shaped by hand using natural materials and traditional techniques passed down through generations.</p>
    </div>
    <div class="value-card">
      <h3>Modern Aesthetic</h3>
      <p>Designed to complement contemporary luxury interiors without losing its deep cultural soul.</p>
    </div>
    <div class="value-card">
      <h3>Custom & B2B</h3>
      <p>From private residences to large-scale business installations, we create pieces tailored to your vision.</p>
    </div>
    <div class="value-card">
      <h3>Mindful Creation</h3>
      <p>We reject mass production. Every artwork is a slow, handcrafted journey that respects time and tradition.</p>
    </div>
  </div>
</section>

<section class="section craft">
  <div class="craft-inner">
    <div class="craft-grid">
      <div class="craft-text">
        <h3>Crafted by Hand,<br>Guided by Soul</h3>
        <p>Each Auraloom creation passes through skilled hands—from raw clay preparation to intricate mirror placement and the final finish.</p>
        <p>The result is more than just a product; it is a living piece of culture that brings character to every wall.</p>
      </div>
      <div class="craft-art">
        <img src="a.png" alt="Lippan Art">
      </div>
    </div>
  </div>
</section>

<section class="section instagram-section">
  <div class="craft-inner">
    <div class="craft-grid">
      <div class="insta-grid-wrapper">
        <div class="insta-grid">
          <div class="insta-item"><span>The Process</span></div>
          <div class="insta-item"><span>Raw Earth</span></div>
          <div class="insta-item"><span>Mirror Detail</span></div>
          <div class="insta-item"><span>Finished Art</span></div>
        </div>
      </div>
      <div class="insta-text">
        <h4 style="color:var(--accent); text-transform:uppercase; font-size:11px; letter-spacing:2px; margin-bottom:15px;">Digital Atelier</h4>
        <h2>Our Journey on Instagram</h2>
        <p>
          We believe in the beauty of the process as much as the final piece. On our Instagram, we share the rhythmic movement of hands on clay, the careful placement of mirrors, and the stories behind our latest custom installations.
          <br><br>
          Join our growing community and see how traditional Kutch art finds its place in modern luxury.
        </p>
        <a href="https://www.instagram.com/_auraloom_art?igsh=bHk3eXM1bjBxc2g=" target="_blank" class="insta-link">Follow @AURALOOM</a>
      </div>
    </div>
  </div>
</section>

<section class="cta">
  <h2>Let’s Create Something Meaningful</h2>
  <p class="text-muted">Custom designs · One-of-a-kind artworks · Commercial spaces</p>
  <a href="custom-order.php">Start a Custom Inquiry</a>
</section>

</body>
</html>