<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = (int) $_SESSION['customer_id'];
$order_id = (int) ($_GET['order_id'] ?? 0);
$product_id = (int) ($_GET['product_id'] ?? 0);

/* VERIFY ORDER & DELIVERY */
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT * FROM orders
  WHERE id=$order_id
    AND order_status='Delivered'
"));

if (!$order) {
  die("Invalid order or order not yet delivered.");
}

/* PREVENT DUPLICATE REVIEW */
$check = mysqli_query($conn, "
  SELECT id FROM product_reviews
  WHERE order_id=$order_id
    AND product_id=$product_id
    AND user_id=$user_id
");

if (mysqli_num_rows($check) > 0) {
  die("You already reviewed this product.");
}

/* PRODUCT */
$product = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT name FROM products WHERE id=$product_id")
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Review Product | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

  <style>
    /* ================= BRAND VARIABLES ================= */
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card: #1b1815;
      --text: #f3ede7;
      --muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border: rgba(255, 255, 255, .12);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--bg-dark);
      color: var(--text);
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
    }

    /* ================= HEADER (Standardized) ================= */
    header {
      position: fixed;
      top: 0; width: 100%; height: 80px;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.85);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid var(--border);
      display: grid;
      grid-template-columns: auto 1fr auto;
      align-items: center;
      padding: 0 80px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      letter-spacing: 2px;
      text-decoration: none;
      color: var(--text);
    }

    nav { display: flex; justify-content: center; gap: 34px; }
    nav a {
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--muted);
      text-decoration: none;
      position: relative;
      padding-bottom: 6px;
      transition: 0.3s ease;
    }
    nav a:hover { color: var(--text); }
    nav a::after {
      content: ""; position: absolute; left: 0; bottom: 0;
      width: 0%; height: 1px; background: var(--accent); transition: 0.35s ease;
    }
    nav a:hover::after { width: 100%; }

    /* ================= MAIN CONTAINER ================= */
    .page-wrap {
      padding-top: 150px;
      padding-bottom: 100px;
    }

    .container {
      max-width: 600px;
      margin: auto;
      padding: 0 20px;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      padding: 50px 40px;
      transition: 0.3s;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .sub {
      color: var(--accent);
      font-size: 16px;
      margin-bottom: 40px;
      font-weight: 400;
      letter-spacing: 0.5px;
    }

    /* ================= FORM ELEMENTS ================= */
    .form-group {
        margin-bottom: 30px;
    }

    label {
      display: block;
      font-size: 11px;
      letter-spacing: 2px;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    select,
    textarea {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      padding: 12px 0;
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      transition: 0.3s ease;
    }

    select option {
        background: var(--bg-dark);
        color: var(--text);
    }

    select:focus,
    textarea:focus {
      outline: none;
      border-bottom-color: var(--accent);
    }

    textarea {
      min-height: 100px;
      resize: none;
    }

    /* ================= BUTTON (Consistent Square) ================= */
    .btn {
      width: 100%;
      padding: 16px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: 500;
      cursor: pointer;
      transition: .4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .btn:hover {
      background: var(--accent-hover);
      transform: translateY(-4px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .back {
      display: block;
      margin-top: 25px;
      text-align: center;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted);
      text-decoration: none;
      transition: 0.3s;
    }

    .back:hover { color: var(--accent); }

    @media (max-width: 900px) {
        header { padding: 0 30px; }
        nav { display: none; }
        .page-wrap { padding-top: 110px; }
    }
  </style>
</head>

<body>

  <header>
    <a href="index.php" class="logo">AURALOOM</a>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Shop</a>
      <a href="custom-order.php">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="contact_us.php">Contact</a>
    </nav>
    <a href="order-history.php" style="color: var(--accent); font-size: 13px; text-decoration: none; border-bottom: 1px solid var(--accent);">MY ORDERS</a>
  </header>

  <div class="page-wrap">
    <div class="container">
      <div class="card">

        <h2>Artpiece Review</h2>
        <p class="sub"><?= htmlspecialchars($product['name']) ?></p>

        <form method="post" action="submit-review.php">

          <input type="hidden" name="order_id" value="<?= $order_id ?>">
          <input type="hidden" name="product_id" value="<?= $product_id ?>">

          <div class="form-group">
            <label>Your Rating</label>
            <select name="rating" required>
              <option value="">Select rating</option>
              <option value="5">★★★★★ Excellent</option>
              <option value="4">★★★★ Very Good</option>
              <option value="3">★★★ Good</option>
              <option value="2">★★ Fair</option>
              <option value="1">★ Poor</option>
            </select>
          </div>

          <div class="form-group">
            <label>Share Your Thoughts</label>
            <textarea name="review_text" required placeholder="How does this artwork feel on your wall?"></textarea>
          </div>

          <button type="submit" class="btn">
            Submit Review
          </button>

        </form>

        <a href="order-history.php" class="back">← Return to My Orders</a>

      </div>
    </div>
  </div>

</body>
</html>