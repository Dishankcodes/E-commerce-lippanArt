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
  SELECT o.*
  FROM orders o
  JOIN customers c ON c.email = o.customer_email
  WHERE o.id = $order_id
    AND o.order_status = 'Delivered'
    AND c.id = $user_id
"));

if (!$order) {
  die("Invalid order");
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

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card: #1b1815;
      --text: #f3ede7;
      --muted: #b9afa6;
      --accent: #c46a3b;
      --border: rgba(255, 255, 255, .12);
    }

    * {
      box-sizing: border-box
    }

    body {
      background: var(--bg-dark);
      color: var(--text);
      font-family: Poppins, sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 80px auto;
      padding: 0 20px;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 32px;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      margin-bottom: 6px;
    }

    .sub {
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 24px;
    }

    label {
      font-size: 13px;
      letter-spacing: 1px;
      color: var(--muted);
      text-transform: uppercase;
    }

    select,
    textarea {
      width: 100%;
      background: var(--bg-soft);
      border: 1px solid var(--border);
      color: var(--text);
      padding: 12px;
      margin-top: 8px;
      border-radius: 10px;
      font-family: Poppins, sans-serif;
    }

    select:focus,
    textarea:focus {
      outline: none;
      border-color: var(--accent);
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    .btn {
      margin-top: 22px;
      width: 100%;
      padding: 14px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 14px;
      letter-spacing: 1px;
      border-radius: 12px;
      cursor: pointer;
      transition: .3s;
    }

    .btn:hover {
      background: #a95a32;
    }

    .back {
      display: block;
      margin-top: 18px;
      text-align: center;
      font-size: 13px;
      color: var(--muted);
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="card">

      <h2>Review Product</h2>
      <p class="sub"><?= htmlspecialchars($product['name']) ?></p>

      <form method="post" action="submit-review.php">

        <input type="hidden" name="order_id" value="<?= $order_id ?>">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">

        <label>Your Rating</label>
        <select name="rating" required>
          <option value="">Select rating</option>
          <option value="5">★★★★★ Excellent</option>
          <option value="4">★★★★ Very Good</option>
          <option value="3">★★★ Good</option>
          <option value="2">★★ Fair</option>
          <option value="1">★ Poor</option>
        </select>

        <br><br>

        <label>Your Experience</label>
        <textarea name="review_text" required placeholder="Share your experience with this artwork..."></textarea>

        <button type="submit" class="btn">
          Submit Review
        </button>

      </form>

      <a href="order-history.php" class="back">← Back to My Orders</a>

    </div>
  </div>

</body>

</html>