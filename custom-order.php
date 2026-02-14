<?php
session_start();
include("db.php");

$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $type = $_POST['order_type'];
  $budget = trim($_POST['budget']);
  $idea = trim($_POST['idea']);

  $stmt = mysqli_prepare($conn, "
    INSERT INTO custom_orders 
    (name, email, phone, order_type, budget, idea)
    VALUES (?, ?, ?, ?, ?, ?)
  ");
  mysqli_stmt_bind_param(
    $stmt,
    "ssssss",
    $name,
    $email,
    $phone,
    $type,
    $budget,
    $idea
  );
  mysqli_stmt_execute($stmt);

  $success = "Your request has been sent. Our team will contact you shortly.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Custom Orders | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, .12);
    }

    /* RESET */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
    }

    a {
      text-decoration: none;
      color: inherit
    }

    /* ================= NAVBAR (SAME AS HOME) ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 72px;
      z-index: 1000;
      background: rgba(15, 13, 11, .85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border-soft);

      display: grid;
      grid-template-columns: auto 1fr auto;
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
      justify-content: center;
      gap: 36px;
    }

    nav a {
      position: relative;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-muted);
      padding-bottom: 6px;
    }

    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0%;
      height: 1px;
      background: var(--accent);
      transition: .35s ease;
    }

    nav a:hover::after,
    nav a.active::after {
      width: 100%;
    }

    nav a:hover,
    nav a.active {
      color: var(--text-main);
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1px;
    }

    /* ================= HERO ================= */
    .hero {
      min-height: 100vh;
      padding-top: 120px;
      background:
        radial-gradient(circle at right, rgba(196, 106, 59, .18), transparent 60%),
        linear-gradient(to bottom, var(--bg-dark), var(--bg-soft));
      display: flex;
      align-items: center;
    }

    .hero-inner {
      width: 100%;
      max-width: 1400px;
      margin: auto;
      padding: 0 80px;
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 80px;
      align-items: center;
    }

    /* LEFT FORM */
    .form-box h1 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      margin-bottom: 10px;
    }

    .form-box p {
      color: var(--text-muted);
      margin-bottom: 40px;
    }

    .input-group {
      margin-bottom: 26px;
    }

    label {
      font-size: 13px;
      color: var(--text-muted);
    }

    input,
    select,
    textarea {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-soft);
      padding: 12px 0;
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
    }

    /* FIX SELECT DROPDOWN */
    select {
      color: var(--text-main);
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      cursor: pointer;
    }

    select option {
      background: var(--bg-dark);
      color: var(--text-main);
    }


    textarea {
      resize: none;
      height: 90px
    }

    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-bottom-color: var(--accent);
    }

    .submit-btn {
      margin-top: 30px;
      padding: 14px 40px;
      background: var(--accent);
      border: none;
      color: #fff;
      letter-spacing: 1px;
      cursor: pointer;
    }

    /* RIGHT INFO */
    .info-box h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 20px;
    }

    .info-box ul {
      list-style: none;
      margin-bottom: 50px;
    }

    .info-box li {
      margin-bottom: 14px;
      color: var(--text-muted);
    }

    .info-box strong {
      color: var(--text-main);
    }

    /* DECOR IMAGE */
    .lippan-mini {
      margin-top: 40px;
      width: 220px;
      opacity: .9;
      animation: slowRotate 80s linear infinite;
      filter: drop-shadow(0 25px 40px rgba(0, 0, 0, .6));
    }

    @keyframes slowRotate {
      from {
        transform: rotate(0deg)
      }

      to {
        transform: rotate(360deg)
      }
    }

    /* MOBILE */
    @media(max-width:900px) {
      header {
        padding: 0 30px
      }

      nav {
        display: none
      }

      .hero-inner {
        grid-template-columns: 1fr;
        padding: 0 30px;
      }

      .lippan-mini {
        width: 160px;
        margin: auto;
      }
    }

    footer {
      background: #0b0a08;
      padding: 100px 80px 60px;
      border-top: 1px solid var(--border-soft);
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 50px;
      margin-bottom: 60px;
    }

    footer h4 {
      font-size: 14px;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    footer a,
    footer p {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 12px;
      display: block;
    }

    .footer-bottom {
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
    }
  </style>
</head>

<body>

  <header>
    <div class="logo">Auraloom</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Collection</a>
      <a href="custom-order.php" class="active">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About us</a>
      <a href="contact.php">Contact</a>
    </nav>
    <a href="cart.php" class="header-btn">Cart</a>
  </header>

  <section class="hero">
    <div class="hero-inner">

      <!-- LEFT -->
      <div class="form-box">
        <h1>Custom Order Request</h1>
        <p>Tell us your vision. Our artisans will bring it to life.</p>
        <?php if($success): ?>
  <p style="color:#7CFFB2;margin-bottom:20px;">
    <?= htmlspecialchars($success) ?>
  </p>
<?php endif; ?>

        <form method="post">

          <div class="input-group">
            <label>Your Name *</label>
            <input type="text" name="name" required>
          </div>

          <div class="input-group">
            <label>Email *</label>
            <input type="email" name="email" required>
          </div>

          <div class="input-group">
            <label>Phone *</label>
            <input type="text" name="phone" required>
          </div>

          <div class="input-group">
            <label>Order Type</label>
            <select name="order_type">
              <option>Home Decor</option>
              <option>Wall Installation</option>
              <option>Commercial / Cafe</option>
              <option>Corporate Gifting</option>
            </select>
          </div>

          <div class="input-group">
            <label>Approx Budget (₹)</label>
            <input type="text" name="budget">
          </div>

          <div class="input-group">
            <label>Describe Your Idea *</label>
            <textarea name="idea" required></textarea>
          </div>

          <button class="submit-btn">Submit Request</button>
        </form>
      </div>

      <!-- RIGHT -->
      <div class="info-box">
        <h2>What Happens Next?</h2>
        <ul>
          <li>• Design team reviews your request</li>
          <li>• Sketches & references shared</li>
          <li>• Pricing & timeline discussion</li>
          <li>• <strong>Handcrafted creation begins ✨</strong></li>
        </ul>

        <h2>Bulk & B2B Orders</h2>
        <p class="text-muted">
          Cafés, hotels, offices & large installations.<br>
          Custom sizes, themes & branding available.
        </p>

        <img src="a.png" alt="Lippan Art" class="lippan-mini">
      </div>

    </div>
  </section>
  <footer class="reveal">
    <div class="footer-grid">
      <div>
        <h4>Auraloom</h4>
        <p>Handcrafted Lippan Art<br>Rooted in Kutch</p>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="collection.php">Shop</a>
        <a href="custom-order.php">Custom Art</a>
        <a href="about-us.php">About Us</a>
      </div>
      <div>
        <h4>Business</h4>
        <a href="#">B2B Orders</a>
        <a href="#">Collaborations</a>
        <a href="#">Care Guide</a>
      </div>
      <div>
        <h4>Contact</h4>
        <p>Email: hello@auraloom.in</p>
        <p>WhatsApp: +91 XXXXX XXXXX</p>
      </div>
    </div>

    <div class="footer-bottom">
      © 2026 Auraloom · Handcrafted in India
    </div>
  </footer>
</body>

</html>