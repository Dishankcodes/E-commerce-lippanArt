<?php
include("db.php");

$order_id = (int) ($_GET['id'] ?? 0);

if ($order_id <= 0) {
  header("Location: index.php");
  exit();
}

/* FETCH ORDER */
$order_q = mysqli_query(
  $conn,
  "SELECT * FROM orders WHERE id='$order_id' LIMIT 1"
);
$order = mysqli_fetch_assoc($order_q);

if (!$order) {
  echo "<h2 style='color:#fff;text-align:center;margin-top:80px'>Order not found</h2>";
  exit();
}

$items = mysqli_query(
  $conn,
  "SELECT 
      oi.quantity,
      p.name,
      p.image
   FROM order_items oi
   JOIN products p ON oi.product_id = p.id
   WHERE oi.order_id='$order_id'"
);

?>



<!DOCTYPE html>
<html>

<head>
  <title>Track Order | Auraloom</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400&display=swap"
    rel="stylesheet">
  <style>
    body {
      background: #0f0d0b;
      color: #f3ede7;
      font-family: Poppins, sans-serif;
    }

    .box {
      max-width: 720px;
      margin: 80px auto;
      padding: 40px;
      background: #171411;
      border: 1px solid rgba(255, 255, 255, .12);
      border-radius: 16px;
    }

    h1,
    h3 {
      font-family: 'Playfair Display', serif;
    }

    .muted {
      color: #b9afa6;
      font-size: 14px;
    }

    .status {
      display: inline-block;
      margin-top: 10px;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 13px;
      background: #1b1815;
      color: #c46a3b;
    }

    .timeline {
      display: flex;
      justify-content: space-between;
      margin: 30px 0;
      font-size: 13px;
      color: #b9afa6;
    }

    .timeline span.active {
      color: #9fd3a9;
      font-weight: 600;
    }

    .btn {
      display: inline-block;
      margin-top: 14px;
      padding: 12px 22px;
      background: #c46a3b;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
    }

    .btn.secondary {
      background: #1b1815;
      color: #f3ede7;
    }

    hr {
      border: none;
      border-top: 1px solid rgba(255, 255, 255, .12);
      margin: 30px 0;
    }
  </style>
</head>

<body>

  <div class="box">
    <?php if (isset($_GET['testimonial']) && $_GET['testimonial'] === 'success'): ?>
      <div style="
    margin-bottom:20px;
    padding:14px 18px;
    border:1px solid rgba(125,216,125,.3);
    background:rgba(125,216,125,.12);
    color:#7dd87d;
    font-size:14px;
  ">
        Thank you! Your testimonial was submitted and is awaiting approval.
      </div>
    <?php endif; ?>

    <h1>Track Your Order</h1>
    <p class="muted">Order ID: <strong>#
        <?= $order_id ?>
      </strong></p>

    <span class="status">
      <?= ucfirst($order['order_status']) ?>
    </span>
    <?php if ($order['order_status'] === 'Cancelled'): ?>
      <p style="color:#ff6b6b;margin-top:20px">
        This order was cancelled.
      </p>
    <?php endif; ?>

    <!-- TIMELINE -->
    <div class="timeline">
      <span
        class="<?= in_array($order['order_status'], ['Placed', 'Processing', 'Shipped', 'Delivered']) ? 'active' : '' ?>">✔
        Placed</span>
      <span class="<?= in_array($order['order_status'], ['Processing', 'Shipped', 'Delivered']) ? 'active' : '' ?>">🛠
        Processing</span>
      <span class="<?= in_array($order['order_status'], ['Shipped', 'Delivered']) ? 'active' : '' ?>">🚚 Shipped</span>
      <span class="<?= $order['order_status'] == 'Delivered' ? 'active' : '' ?>">🏡 Delivered</span>
    </div>

    <hr>

    <!-- ORDER ITEMS -->
    <h3>Order Items</h3>

    <?php while ($item = mysqli_fetch_assoc($items)): ?>
      <div style="
    display:flex;
    align-items:center;
    gap:16px;
    margin:14px 0;
    padding:12px;
    background:#1b1815;
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;
  ">

        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="
        width:70px;
        height:70px;
        object-fit:cover;
        border-radius:10px;
        border:1px solid rgba(255,255,255,.12);
      ">

        <div style="flex:1">
          <strong><?= htmlspecialchars($item['name']) ?></strong>
          <div class="muted" style="margin-top:4px">
            Quantity: <?= $item['quantity'] ?>
          </div>
        </div>

      </div>
    <?php endwhile; ?>

    <hr>

    <p>
      <strong>Total Paid:</strong>
      <span style="color:#c46a3b">
        ₹
        <?= number_format($order['final_amount'], 2) ?>
      </span>
    </p>

    <div style="margin-top:30px">
      <a href="collection.php" class="btn secondary">Explore More</a>
      <a href="index.php" class="btn secondary">Home</a>
    </div>

    <p class="muted" style="margin-top:20px">
      Need help? Contact us with your Order ID.
    </p>

    <a href="https://wa.me/919876543210?text=Hi, I want help with order #<?= $order_id ?>" class="btn"
      style="background:#25D366">
      WhatsApp Support
    </a>

  </div>

</body>

</html>