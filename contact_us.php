<?php
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  include("db.php"); // optional – remove if not saving to DB

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $purpose = $_POST['purpose'] ?? '';
  $other = trim($_POST['other_purpose'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($purpose === "Other" && $other !== "") {
    $purpose = $other;
  }

  if ($name && $email && $phone && $purpose && $message) {

    // OPTIONAL: Save to database
    $stmt = $conn->prepare("
  INSERT INTO contact_enquiries 
  (name, email, phone, purpose, message) 
  VALUES (?, ?, ?, ?, ?)
");

    $stmt->bind_param("sssss", $name, $email, $phone, $purpose, $message);
    $stmt->execute();
    $stmt->close();


    $success = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | Auraloom</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    /* ================= BRAND VARIABLES ================= */
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card-bg: #1b1815;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      /* Signature Rust */
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, 0.12);
      --whatsapp-green: #128c7e;
      /* Darker WhatsApp Green */
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
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
      transition: 0.3s ease;
    }

    /* ================= HEADER (Centered Navigation) ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 80px;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.75);
      backdrop-filter: blur(15px);
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
      gap: 35px;
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
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 12px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .header-btn:hover {
      background: var(--accent-hover);
    }

    /* ================= MAIN WRAPPER ================= */
    .page-wrap {
      padding-top: 150px;
      padding-bottom: 100px;
    }

    /* CONTACT SECTION */
    .contact-section {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 40px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    /* LEFT INFO */
    .contact-info h1 {
      font-family: 'Playfair Display', serif;
      font-size: 44px;
      margin-bottom: 20px;
      color: var(--text-main);
      font-weight: 500;
    }

    .contact-info p {
      font-size: 16px;
      color: var(--text-muted);
      margin-bottom: 40px;
      line-height: 1.8;
    }

    .info-box {
      border-left: 2px solid var(--accent);
      padding: 5px 0 5px 25px;
      margin-bottom: 35px;
    }

    .info-box h4 {
      font-size: 12px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 10px;
    }

    .info-box p {
      font-size: 14px;
      color: var(--text-main);
      margin-bottom: 0;
    }

    /* ================= WHATSAPP BUTTON (Standard Square) ================= */
    .whatsapp-btn {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 14px 28px;
      background: var(--whatsapp-green);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0;
      /* Square as requested */
      margin-top: 25px;
      font-weight: 500;
    }

    .whatsapp-btn:hover {
      background: #075e54;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    /* ================= FORM CONTAINER ================= */
    .contact-form {
      background: var(--card-bg);
      padding: 50px;
      border: 1px solid var(--border-soft);
    }

    .form-group {
      margin-bottom: 35px;
    }

    label {
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--text-muted);
      margin-bottom: 8px;
      font-weight: 500;
    }

    input,
    textarea {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-soft);
      padding: 12px 0;
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      transition: 0.3s ease;
    }

    textarea::placeholder {
      color: var(--text-muted);
      opacity: 1;
      /* Important for Firefox */
    }

    input::placeholder {
      color: var(--text-muted);
      opacity: 1;
    }

    input:focus,
    textarea:focus {
      outline: none;
      border-bottom-color: var(--accent);
    }

    textarea {
      resize: none;
      height: 110px;
    }

    /* ================= SUBMIT BUTTON (Standard Square) ================= */
    .submit-btn {
      width: 100%;
      padding: 16px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 0;
      /* Perfectly square */
      font-weight: 600;
      transition: 0.3s;
    }

    .submit-btn:hover {
      background: var(--accent-hover);
      transform: translateY(-4px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    /* RESPONSIVE */
    @media(max-width:900px) {
      header {
        padding: 0 30px;
        height: 70px;
      }

      nav {
        display: none;
      }

      .page-wrap {
        padding-top: 110px;
      }

      .contact-section {
        grid-template-columns: 1fr;
        gap: 60px;
        padding: 0 24px;
      }

      .contact-info h1 {
        font-size: 36px;
      }

      .contact-form {
        padding: 40px 30px;
      }
    }


    select {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-soft);
      padding: 12px 0;
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
    }

    select option {
      background: var(--card-bg);
      color: var(--text-main);
    }

    .instagram-btn {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 14px 28px;
      background: transparent;
      color: var(--text-main);
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      border: 1px solid var(--border-soft);
      margin-top: 15px;
    }

    .instagram-btn:hover {
      background: var(--accent);
      border-color: var(--accent);
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
    <div class="logo">AURALOOM</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Shop</a>
      <a href="custom-order.php">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About</a>
      <a href="contact_us.php" class="active">Contact</a>
    </nav>
    <a href="cart.php" class="header-btn"> <i class="bi bi-cart"></i>
      Cart</a>

  </header>

  <div class="page-wrap">
    <section class="contact-section">

      <div class="contact-info">
        <h1>Contact Us</h1>
        <?php if ($success): ?>
          <p style="color:#7ed957;margin-bottom:30px;">
            ✔ Your enquiry has been sent successfully. We’ll contact you soon.
          </p>
        <?php endif; ?>


        <p>
          For bulk orders, custom Lippan art, collaborations, or if you face any issue,
          feel free to reach out. Our team will get back to you as soon as possible.
        </p>

        <div class="info-box">
          <h4>Bulk Orders & Custom Work</h4>
          <p>Perfect for weddings, gifting & large installations.</p>
        </div>

        <div class="info-box">
          <h4>Support & Issues</h4>
          <p>Report payment, delivery or product-related concerns.</p>
        </div>

        <a href="https://wa.me/919999999999" target="_blank" class="whatsapp-btn">
          <i class="fab fa-whatsapp"></i> Contact on WhatsApp
        </a>

        <a href="https://www.instagram.com/auraloom" target="_blank" class="instagram-btn">
          <i class="fab fa-instagram"></i> Follow on Instagram
        </a>

      </div>

      <div class="contact-form">
        <form method="post" action="">

          <div class="form-group">
            <label>Full Name</label>
            <input type="text" placeholder="Your Name" name="name" required>
          </div>

          <div class="form-group">
            <label>Email Address</label>
            <input type="email" placeholder="email@example.com" name="email" required>
          </div>

          <div class="form-group">
            <label>Phone / WhatsApp Number</label>
            <input type="tel" placeholder="+91 XXXXX XXXXX" name="phone" required>
          </div>


          <div class="form-group">
            <label>Purpose of Contact</label>
            <select name="purpose" required>
              <option value="">Select Purpose</option>
              <option value="Payment Issue">Payment Issue</option>

              <option value="Bulk Order">Bulk Order</option>
              <option value="Custom Order">Custom Order</option>
              <option value="Cancel Order">Cancel Order</option>
              <option value="Delivery Issue">Delivery Issue</option>
              <option value="App / Website Issue">App / Website Issue</option>
              <option value="Collaboration">Collaboration</option>
              <option value="Other">Other (Specify)</option>
            </select>
          </div>
          <p id="purposeHint" style="font-size:13px;color:var(--text-muted);margin-top:-20px;"></p>

          <div class="form-group" id="otherPurposeBox" style="display:none;">
            <label>Selected Other, Please Specify</label>
            <input type="text" name="other_purpose" placeholder="Specify your purpose">
          </div>

          <div class="form-group">
            <label>Your Message</label>
            <textarea name="message" id="messageBox"
              placeholder="Please describe your issue or requirement in detail..." required></textarea>

          </div>

          <button class="submit-btn">Send Enquiry</button>
        </form>
      </div>

    </section>
  </div>
  <script src='https://www.noupe.com/embed/019c52c8cb7a7420bc671dfed07b11d3bc14.js'></script>

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
        <a href="logout.php">Switch Account</a>
      </div>
      <div>
        <h4>Business</h4>
        <a href="b2b.php">B2B Orders</a>
        <a href="https://wa.me/91XXXXXXXXXX">Collaborations</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="faq.html">FAQs</a>
      </div>
      <div>
        <h4>Contact</h4>

        <a href="mailto:hello@auraloom.in" style="">
          <i class="bi bi-envelope"></i>

          <span>Email</span>
        </a>


        <a href="https://wa.me/91XXXXXXXXXX" target="_blank">
          <i class="bi bi-whatsapp"></i>
          <span>WhatsApp</span>
        </a>


        <a href="https://www.instagram.com/_auraloom_art?igsh=bHk3eXM1bjBxc2g=" target="_blank">
          <i class="bi bi-instagram"></i>
          <span>Instagram</span>
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      © 2026 Auraloom · Handcrafted in India
    </div>
  </footer>
  <script>
    const purposeSelect = document.querySelector('select[name="purpose"]');
    const otherBox = document.getElementById('otherPurposeBox');
    const hint = document.getElementById('purposeHint');

    purposeSelect.addEventListener('change', function () {
      const value = this.value;

      // Reset
      otherBox.style.display = 'none';
      hint.innerHTML = '';

      if (value === 'Other') {
        otherBox.style.display = 'block';
      }

      if (value === 'Bulk Order') {
        hint.innerHTML =
          '💡 For faster assistance with bulk orders, please visit our <a href="b2b.php" style="color:var(--accent);">B2B Orders</a> page.';
      }

      if (value === 'Custom Order') {
        hint.innerHTML =
          '🎨 Looking for custom Lippan art? Our <a href="custom-order.php" style="color:var(--accent);">Custom Order</a> page is the best place to start.';
      }
    });
  </script>
  <script>
    const messageBox = document.getElementById('messageBox');

    purposeSelect.addEventListener('change', function () {
      const value = this.value;

      switch (value) {
        case 'Payment Issue':
          messageBox.placeholder =
            'Please mention your order ID, payment mode, and what issue you are facing.';
          break;

        case 'Delivery Issue':
          messageBox.placeholder =
            'Please mention your order ID and describe the delivery issue.';
          break;

        case 'Cancel Order':
          messageBox.placeholder =
            'Please mention your order ID and reason for cancellation.';
          break;

        case 'Bulk Order':
          messageBox.placeholder =
            'Please share quantity, delivery location, and expected date.';
          break;

        case 'Custom Order':
          messageBox.placeholder =
            'Describe your custom Lippan art requirement, size, colors, and deadline.';
          break;

        case 'App / Website Issue':
          messageBox.placeholder =
            'Please describe what is not working and when the issue occurs.';
          break;

        default:
          messageBox.placeholder =
            'Please describe your issue or requirement in detail...';
      }
    });
  </script>

</body>

</html>