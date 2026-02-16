<?php
session_start();
include("db.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>B2B & Bulk Orders | Auraloom</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    /* ================= BRAND VARIABLES ================= */
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card-bg: #1b1815;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, 0.12);
    }

    /* RESET */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      line-height: 1.6;
    }

    a {
      text-decoration: none;
      color: inherit;
      transition: 0.3s ease;
    }

    /* ================= HEADER ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 80px;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.75);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border-soft);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 80px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      letter-spacing: 1px;
    }

    nav {
      display: flex;
      gap: 35px;
    }

    nav a {
      font-size: 12px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-muted);
      position: relative;
      padding-bottom: 5px;
    }

    nav a:hover,
    nav a.active {
      color: var(--text-main);
    }

    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0%;
      height: 1px;
      background: var(--accent);
      transition: 0.4s ease;
    }

    nav a:hover::after,
    nav a.active::after {
      width: 100%;
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 12px;
      letter-spacing: 1px;
      border: none;
      transition: 0.3s ease;
      cursor: pointer;
    }

    .header-btn:hover {
      background: var(--accent-hover);
    }

    /* ================= PAGE WRAPPER ================= */
    .page-wrap {
      padding-top: 80px;
    }

    /* ================= HERO ================= */
    .hero {
      padding: 100px 80px;
      min-height: 70vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background:
        radial-gradient(circle at right, rgba(196, 106, 59, 0.12), transparent 55%),
        linear-gradient(to bottom, var(--bg-dark), var(--bg-soft));
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: 44px;
      max-width: 800px;
      margin-bottom: 20px;
      line-height: 1.3;
    }

    .hero p {
      color: var(--text-muted);
      font-size: 16px;
      max-width: 600px;
      margin-bottom: 40px;
    }

    /* BRAND PILL BUTTON */
    .btn-primary-brand {
      display: inline-block;
      padding: 14px 38px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      border-radius: 30px;
      transition: 0.4s ease;
      width: fit-content;
      border: none;
      cursor: pointer;
    }

    .btn-primary-brand:hover {
      background: var(--accent-hover);
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    /* ================= CONTENT SECTIONS ================= */
    .section {
      padding: 100px 80px;
    }

    .section h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 15px;
      color: var(--text-main);
    }

    .section-desc {
      color: var(--text-muted);
      max-width: 600px;
      margin-bottom: 60px;
      font-size: 15px;
    }

    /* WHY GRID */
    .why-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 40px;
    }

    .why-card {
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
    }

    .why-card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      margin-bottom: 12px;
      color: var(--accent);
    }

    .why-card p {
      color: var(--text-muted);
      font-size: 14px;
    }

    /* USE CASES */
    .use-cases {
      background: linear-gradient(to bottom, var(--bg-soft), var(--bg-dark));
    }

    .case-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 25px;
    }

    .case {
      padding: 40px;
      border: 1px solid var(--border-soft);
      background: var(--card-bg);
      transition: 0.4s ease;
    }

    .case:hover {
      border-color: var(--accent);
      transform: translateY(-5px);
    }

    .case h4 {
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      margin-bottom: 12px;
      color: var(--accent);
    }

    .case p {
      color: var(--text-muted);
      font-size: 14px;
    }

    /* PROCESS */
    .process-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }

    .step {
      border-left: 2px solid var(--accent);
      padding: 5px 0 5px 20px;
    }

    .step span {
      color: var(--accent);
      font-size: 11px;
      letter-spacing: 2px;
      text-transform: uppercase;
      display: block;
    }

    .step p {
      font-size: 14px;
      margin-top: 5px;
    }

    /* CTA */
    .cta {
      text-align: center;
      padding: 120px 40px;
      background: radial-gradient(circle at center, rgba(196, 106, 59, .12), transparent 70%);
    }

    .cta h2 {
      font-family: 'Playfair Display', serif;
      font-size: 38px;
      margin-bottom: 15px;
    }

    /* FOOTER */
    footer {
      background: #0b0a08;
      padding: 80px 80px 40px;
      border-top: 1px solid var(--border-soft);
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
    }

    footer h4 {
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    footer a,
    footer p {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 10px;
      display: block;
    }

    .footer-bottom {
      text-align: center;
      font-size: 12px;
      color: var(--text-muted);
      border-top: 1px solid var(--border-soft);
      padding-top: 25px;
      margin-top: 40px;
    }

    @media(max-width:900px) {
      header {
        padding: 0 30px;
        height: 70px;
      }

      nav {
        display: none;
      }

      .hero {
        padding: 80px 30px;
      }

      .section {
        padding: 60px 30px;
      }

      .hero h1 {
        font-size: 32px;
      }
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
      <a href="contact_us.php">Contact</a>
    </nav>
    <a href="enquire.php" class="header-btn">Enquire Now</a>
  </header>

  <div class="page-wrap">

    <?php if (isset($_GET['success'])): ?>
      <div
        style="max-width:900px; margin:40px auto; padding:25px; border:1px solid rgba(125,216,125,.3); background:rgba(125,216,125,.05); color:#7dd87d; border-radius:15px; text-align:center;">
        <h3 style="font-family:'Playfair Display'; margin-bottom:10px;">Submission Successful</h3>
        <p style="font-size:14px; margin-bottom:20px;">Your B2B enquiry has been received. Our concierge team will reach
          out shortly.</p>
          <p style="font-size:14px; margin-bottom:20px;">  Check your email for confirmation.</p>
        <a href="order-history.php" class="btn-primary-brand" style="padding: 10px 25px; font-size:11px;">Order Details</a>
      </div>
    <?php endif; ?>

    <section class="hero">
      <h1>Artisanal Lippan for<br>Luxury Business Spaces</h1>
      <p>Elevate your commercial interiors with handcrafted heritage art. Auraloom collaborates with designers to create
        timeless installations.</p>
      <a href="enquire.php" class="btn-primary-brand">Start a B2B Project</a>
    </section>

    <section class="section">
      <h2>The Business Advantage</h2>
      <p class="section-desc">Reliable craftsmanship paired with professional project management for commercial
        requirements.</p>

      <div class="why-grid">
        <div class="why-card">
          <h3>Scalable Designs</h3>
          <p>From lobby statement pieces to large-scale feature wall installations.</p>
        </div>
        <div class="why-card">
          <h3>Brand Alignment</h3>
          <p>Custom motifs and color palettes tailored to your corporate identity.</p>
        </div>
        <div class="why-card">
          <h3>Reliable Timelines</h3>
          <p>Dedicated production tracking to meet construction and launch schedules.</p>
        </div>
        <div class="why-card">
          <h3>Heritage Value</h3>
          <p>Authentic Kutch art that adds a unique soul to modern architecture.</p>
        </div>
      </div>
    </section>

    <section class="section use-cases">
      <h2>Industry Partnerships</h2>
      <div class="case-grid">
        <div class="case">
          <h4>Hospitality</h4>
          <p>Bespoke installations for luxury hotels, resorts, and premium guest suites.</p>
        </div>
        <div class="case">
          <h4>Fine Dining</h4>
          <p>Atmospheric statement walls for boutique cafes and upscale restaurants.</p>
        </div>
        <div class="case">
          <h4>Workspaces</h4>
          <p>Cultural branding and warm aesthetic for corporate lobbies and boardrooms.</p>
        </div>
        <div class="case">
          <h4>Architects</h4>
          <p>Custom design support for high-end residential and commercial projects.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <h2>Our B2B Process</h2>
      <div class="process-grid">
        <div class="step">
          <span>Step 01</span>
          <p>Requirement & Space Understanding</p>
        </div>
        <div class="step">
          <span>Step 02</span>
          <p>Concept Sketches & Estimates</p>
        </div>
        <div class="step">
          <span>Step 03</span>
          <p>Handcrafted Production</p>
        </div>
        <div class="step">
          <span>Step 04</span>
          <p>Delivery & Installation Support</p>
        </div>
      </div>
    </section>

    <section class="cta">
      <h2>Transform Your Space Today</h2>
      <p style="color: var(--text-muted); margin-bottom: 25px; font-size: 16px;">Discuss your volume requirements with
        our experts.</p>
      <a href="enquire.php" class="btn-primary-brand">Request B2B Quote</a>
    </section>

    <footer>
      <div class="footer-grid">
        <div>
          <h4>Auraloom</h4>
          <p>Handcrafted Lippan Art<br>Rooted in Kutch tradition.</p>
        </div>
        <div>
          <h4>Explore</h4>
          <a href="collection.php">Master Collection</a>
          <a href="about-us.php">Our Heritage</a>
        </div>
        <div>
          <h4>Business</h4>
          <a href="b2b.php">B2B Projects</a>
          <a href="#">Care Guide</a>
        </div>
        <div>
          <h4>Connect</h4>
          <p>hello@auraloom.in</p>
          <p>WhatsApp: +91 98765 43210</p>
        </div>
      </div>
      <div class="footer-bottom">
        © 2026 AURALOOM · Handcrafted in India
      </div>
    </footer>

  </div>
</body>

</html>