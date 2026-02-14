<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get customer email
$user = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT email FROM customers WHERE id='$user_id'")
);

$email = $user['email'];

// Fetch orders
$orders = mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE customer_email='$email'
  ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>

<head>
  <title>My Orders | Auraloom</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400&display=swap"
    rel="stylesheet">
  <style>
    body {
      background: #0f0d0b;
      color: #f3ede7;
      font-family: Poppins, sans-serif;
    }

    .container {
      max-width: 1000px;
      margin: 80px auto;
    }

    .card {
      background: #171411;
      border: 1px solid rgba(255, 255, 255, .12);
      padding: 24px;
      margin-bottom: 20px;
      border-radius: 12px;
    }

    .btn {
      padding: 8px 14px;
      background: #c46a3b;
      color: #fff;
      text-decoration: none;
      font-size: 13px;
    }

    h1 {
      font-family: Playfair Display
    }
  </style>
</head>

<body>

  <div class="container">
    <h1>My Orders</h1>

    <?php if (mysqli_num_rows($orders) == 0): ?>
      <p>You haven’t placed any orders yet.</p>
    <?php endif; ?>

    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
      <div class="card">
        <p><strong>Order #<?= $o['id'] ?></strong></p>
        <p>Status: <?= $o['tracking_status'] ?></p>
        <p>Total: ₹<?= number_format($o['final_amount'], 2) ?></p>
        <p>Date: <?= date("d M Y", strtotime($o['created_at'])) ?></p>

        <a href="track-order.php?id=<?= $o['id'] ?>" class="btn">View / Track</a>
      </div>
    <?php endwhile; ?>
  </div>

</body>

</html>