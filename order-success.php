<?php
session_start();
include("db.php");

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];

// Fetch order
$order = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id'")
);

// Fetch items
$items = mysqli_query(
    $conn,
    "SELECT oi.*, p.name 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id='$order_id'"
);

// Clear session order id (important)
unset($_SESSION['last_order_id']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Placed | Auraloom</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400&display=swap" rel="stylesheet">
  <style>
    body{
      background:#0f0d0b;
      color:#f3ede7;
      font-family:Poppins,sans-serif;
    }
    .box{
      max-width:700px;
      margin:80px auto;
      padding:40px;
      background:#171411;
      border:1px solid rgba(255,255,255,.12);
      border-radius:14px;
      text-align:center;
    }
    h1{font-family:Playfair Display}
    .btn{
      display:inline-block;
      margin:12px;
      padding:12px 24px;
      background:#c46a3b;
      color:#fff;
      text-decoration:none;
    }
    .muted{color:#b9afa6;font-size:14px}
  </style>
</head>

<body>

<div class="box">
  <h1>🎉 Order Placed Successfully</h1>
  <p class="muted">Order ID: <strong>#<?= $order_id ?></strong></p>

  <hr style="margin:30px 0;border-color:rgba(255,255,255,.12)">

  <h3>Order Preview</h3>

  <?php while($item = mysqli_fetch_assoc($items)): ?>
    <p><?= $item['name'] ?> × <?= $item['quantity'] ?></p>
  <?php endwhile; ?>

  <p style="margin-top:20px">
    <strong>Total Paid:</strong> ₹<?= number_format($order['final_amount'],2) ?>
  </p>

  <div style="margin-top:30px">
    <a href="track-order.php?id=<?= $order_id ?>" class="btn">Track Order</a>
    <a href="index.php" class="btn">Go to Home</a>
  </div>
</div>

</body>
</html>
