<?php
session_start();
include("db.php");

/* ===== PREFILL LOGIC ===== */
$prefill_business = '';
$prefill_email = '';
$prefill_phone = '';

if (isset($_SESSION['customer_id'])) {
  $uid = (int) $_SESSION['customer_id'];

  $res = mysqli_query($conn, "
    SELECT name, email, phone 
    FROM customers 
    WHERE id = $uid 
    LIMIT 1
  ");

  if ($res && mysqli_num_rows($res) === 1) {
    $u = mysqli_fetch_assoc($res);
    $prefill_business = $u['name'];   // using name as business/contact name
    $prefill_email = $u['email'];
    $prefill_phone = $u['phone'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>B2B Enquiry | Auraloom</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500;600&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, .2);
      --input-focus-bg: rgba(255, 255, 255, 0.03);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      -webkit-font-smoothing: antialiased;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      line-height: 1.8;
    }

    a {
      text-decoration: none;
      color: inherit;
      transition: 0.4s;
    }

    /* ================= HEADER ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 80px;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border-soft);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 80px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      letter-spacing: 2px;
      font-weight: 600;
    }

    nav {
      display: flex;
      justify-content: center;
      gap: 35px;
    }

    nav a {
      font-size: 11px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--text-muted);
      position: relative;
      padding-bottom: 6px;
      font-weight: 500;
    }

    nav a.active,
    nav a:hover {
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
      transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    nav a:hover::after,
    nav a.active::after {
      width: 100%;
    }

    /* ================= PAGE LAYOUT ================= */
    .page {
      padding-top: 160px;
      padding-bottom: 120px;
      max-width: 1100px;
      margin: auto;
      padding-left: 40px;
      padding-right: 40px;
    }

    h1 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      margin-bottom: 18px;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .page-intro {
      color: var(--text-muted);
      max-width: 680px;
      margin-bottom: 70px;
      font-size: 16px;
      font-weight: 300;
    }

    /* ================= FORM STYLING ================= */
    .form-wrap {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 45px 50px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .full {
      grid-column: 1 / -1;
    }

    label {
      font-size: 10.5px;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 10px;
      font-weight: 600;
    }

    input,
    select,
    textarea {
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-soft);
      padding: 14px 10px;
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      font-weight: 300;
      transition: 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    select option {
      background: var(--bg-soft);
      color: var(--text-main);
    }

    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-bottom-color: var(--accent);
      background-color: var(--input-focus-bg);
    }

    input::placeholder,
    textarea::placeholder {
      color: rgba(185, 175, 166, 0.6);
    }

    textarea {
      height: 120px;
      resize: none;
    }

    input[type="file"] {
      border-bottom: none;
      font-size: 12px;
      padding: 15px 0;
      color: var(--text-muted);
    }

    /* ================= SQUARE SUBMIT BUTTON ================= */
    .submit-btn {
      grid-column: 1 / -1;
      margin-top: 40px;
      padding: 18px 55px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 0;
      transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
      width: fit-content;
      justify-self: start;
    }

    .submit-btn:hover {
      background: var(--accent-hover);
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
    }

    /* ================= RESPONSIVE ================= */
    @media(max-width:900px) {
      header {
        padding: 0 30px;
        height: 75px;
      }

      nav {
        display: none;
      }

      .page {
        padding: 140px 24px 80px;
      }

      .form-wrap {
        grid-template-columns: 1fr;
        gap: 35px;
      }

      h1 {
        font-size: 32px;
      }

      .submit-btn {
        width: 100%;
        justify-self: center;
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
      <a href="contact.php">Contact</a>
    </nav>
  </header>

  <section class="page">
    <h1>B2B Enquiry</h1>
    <p class="page-intro">
      Tell us about your business and requirements.
      Our team will get in touch with pricing, timelines & design options.
    </p>

    <form method="post" action="submit-enquiry.php" enctype="multipart/form-data">
      <div class="form-wrap">

        <div class="form-group">
          <label>Business Name *</label>
          <input type="text" name="business_name" required placeholder="Company name"
            value="<?= htmlspecialchars($prefill_business) ?>">

        </div>

        <div class="form-group">
          <label>Business Type *</label>
          <select name="business_type" required>
            <option value="">Select industry</option>
            <option>Café / Restaurant</option>
            <option>Hotel / Resort</option>
            <option>Office / Corporate</option>
            <option>Interior Designer</option>
            <option>Builder / Architect</option>
            <option>Retail Store</option>
            <option>Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Estimated Quantity *</label>
          <input type="number" name="quantity" placeholder="e.g. 10, 50, 100" required>
        </div>

        <div class="form-group">
          <label>Phone Number *</label>
          <input type="tel" name="phone" required placeholder="+91 XXXX XXX XXX"
            value="<?= htmlspecialchars($prefill_phone) ?>">

        </div>

        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" required placeholder="business@email.com"
            value="<?= htmlspecialchars($prefill_email) ?>">

        </div>

        <div class="form-group">
          <label>Reference Type</label>
          <select name="reference_type">
            <option value="">None</option>
            <option value="product_id">Product ID (from website)</option>
            <option value="product_name">Product Name</option>
            <option value="collection">Collection Name</option>
            <option value="whatsapp">Will share on WhatsApp</option>
            <option value="image">Upload Reference Image</option>
          </select>
        </div>

        <div class="form-group">
          <label>Reference Details</label>
          <input type="text" name="reference_value" placeholder="e.g. Mandala Peacock #04">
        </div>

        <div class="form-group full">
          <label>Upload Reference Image (optional)</label>
          <input type="file" name="reference_image" accept="image/*">
        </div>

        <div class="form-group full">
          <label>Project Details / Requirements</label>
          <textarea name="message"
            placeholder="Describe the space type, desired dimensions, or specific themes..."></textarea>
        </div>

        <button class="submit-btn">Submit Enquiry</button>

      </div>
    </form>
  </section>

</body>

</html>