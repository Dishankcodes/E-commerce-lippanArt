<?php
session_start();
include("db.php");

$customer_id = $_SESSION['customer_id'] ?? null;

// ================= PREFILL USER DETAILS =================
$prefill_name = '';
$prefill_email = '';
$prefill_phone = '';

if (isset($_SESSION['customer_id'])) {
  $uid = (int) $_SESSION['customer_id'];

  $uRes = mysqli_query($conn, "
    SELECT name, email, phone 
    FROM customers 
    WHERE id = $uid 
    LIMIT 1
  ");

  if ($uRes && mysqli_num_rows($uRes) === 1) {
    $u = mysqli_fetch_assoc($uRes);
    $prefill_name = $u['name'];
    $prefill_email = $u['email'];
    $prefill_phone = $u['phone'];
  }
}

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
(customer_id, name, email, phone, order_type, budget, idea)
VALUES (?, ?, ?, ?, ?, ?, ?)
  ");
  mysqli_stmt_bind_param(
    $stmt,
    "issssss",
    $customer_id,
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, .12);
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
      background: rgba(15, 13, 11, .85);
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
      letter-spacing: 2px;
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
      padding: 10px 24px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1px;
      transition: 0.3s;
    }

    .header-btn:hover {
      background: var(--accent-hover);
    }

    /* ================= HERO / FORM SECTION ================= */
    .hero {
      min-height: 100vh;
      padding: 140px 0 80px;
      background:
        radial-gradient(circle at right, rgba(196, 106, 59, .15), transparent 60%),
        linear-gradient(to bottom, var(--bg-dark), var(--bg-soft));
      display: flex;
      align-items: center;
    }

    .hero-inner {
      width: 100%;
      max-width: 1300px;
      margin: auto;
      padding: 0 40px;
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 100px;
      align-items: center;
    }

    /* FORM BOX */
    .form-box h1 {
      font-family: 'Playfair Display', serif;
      font-size: 48px;
      margin-bottom: 15px;
      line-height: 1.1;
    }

    .form-box p {
      color: var(--text-muted);
      margin-bottom: 45px;
      font-size: 16px;
    }

    .input-group {
      margin-bottom: 30px;
    }

    .input-group label {
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--accent);
      margin-bottom: 8px;
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
      font-size: 15px;
      transition: 0.3s;
    }

    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-bottom-color: var(--accent);
    }

    select option {
      background: var(--bg-dark);
      color: var(--text-main);
    }

    textarea {
      height: 100px;
      resize: none;
    }

    .submit-btn {
      margin-top: 20px;
      padding: 15px 45px;
      background: var(--accent);
      border: none;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-size: 13px;
      cursor: pointer;
      transition: 0.3s;
    }

    .submit-btn:hover {
      background: var(--accent-hover);
      transform: translateY(-2px);
    }

    /* RIGHT SIDE INFO */
    .info-box h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 25px;
    }

    .info-box ul {
      list-style: none;
      margin-bottom: 50px;
    }

    .info-box li {
      margin-bottom: 18px;
      color: var(--text-muted);
      font-size: 15px;
    }

    .info-box li strong {
      color: var(--accent);
      margin-right: 10px;
    }

    .lippan-mini {
      width: 280px;
      opacity: .8;
      animation: slowRotate 80s linear infinite;
      filter: drop-shadow(0 30px 50px rgba(0, 0, 0, .5));
      display: block;
      margin: 40px auto 0;
    }

    @keyframes slowRotate {
      from {
        transform: rotate(0deg)
      }

      to {
        transform: rotate(360deg)
      }
    }

    /* ================= FOOTER ================= */
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
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 25px;
      color: var(--text-main);
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
      font-size: 12px;
      color: var(--text-muted);
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
      letter-spacing: 1px;
    }

    @media(max-width:900px) {
      header {
        padding: 0 30px
      }

      nav {
        display: none
      }

      .hero-inner {
        grid-template-columns: 1fr;
        gap: 60px;
        padding: 0 20px;
      }

      .form-box h1 {
        font-size: 36px;
      }
    }
  </style>
</head>

<body>

  <header>
    <a href="index.php" class="logo">Auraloom</a>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Collection</a>
      <a href="custom-order.php" class="active">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About us</a>
      <a href="contact_us.php">Contact</a>
    </nav>
    <a href="cart.php" class="header-btn">Cart <i class="bi bi-cart"></i>
    </a>
  </header>

  <section class="hero">
    <div class="hero-inner">

      <div class="form-box">
        <h1>Custom Order Request</h1>
        <p>Tell us your vision. Our artisans will bring it to life with traditional Kutch craftsmanship.</p>

        <?php if ($success): ?>
          <div
            style="background: rgba(124, 255, 178, 0.1); border: 1px solid #7CFFB2; padding: 15px; color: #7CFFB2; margin-bottom: 30px; font-size: 14px;">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="input-group">
            <label>Full Name *</label>
            <input type="text" name="name" required placeholder="Enter your name"
              value="<?= htmlspecialchars($prefill_name) ?>">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
              <label>Email Address *</label>
              <input type="email" name="email" required placeholder="email@example.com"
                value="<?= htmlspecialchars($prefill_email) ?>">
            </div>
            <div class="input-group">
              <label>Phone Number *</label>
              <input type="text" name="phone" required placeholder="+91 XXXX XXX XXX"
                value="<?= htmlspecialchars($prefill_phone) ?>">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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
              <input type="text" name="budget" placeholder="e.g. 10,000">
            </div>
          </div>

          <div class="input-group">
            <label>Describe Your Idea *</label>
            <textarea name="idea" required
              placeholder="Tell us about the size, colors, or theme you have in mind..."></textarea>
          </div>

          <button class="submit-btn">Submit Request</button>
        </form>
      </div>

      <div class="info-box">
        <h2>What Happens Next?</h2>
        <ul>
          <li><strong>01.</strong> Design team reviews your request</li>
          <li><strong>02.</strong> Sketches & references shared</li>
          <li><strong>03.</strong> Pricing & timeline discussion</li>
          <li><strong>04.</strong> Handcrafted creation begins ✨</li>
        </ul>

        <h2>Bulk & B2B Orders</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">
          Transforming cafés, hotels, and large residences with bespoke heritage installations. Custom branding and
          themes available.
        </p>

        <img src="a.png" alt="Lippan Art" class="lippan-mini">
      </div>

    </div>
  </section>

  <footer>
    <div class="footer-grid">
      <div>
        <h4>Auraloom</h4>
        <p>Handcrafted Lippan Art<br>Rooted in Kutch Tradition.</p>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="collection.php">Shop Collection</a>
        <a href="custom-order.php">Custom Art</a>
        <a href="about-us.php">About Us</a>
      </div>
      <div>
        <h4>Business</h4>
        <a href="b2b.php">B2B Orders</a>
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
      © 2026 Auraloom · Handcrafted with soul in India
    </div>
  </footer>
</body>

</html>