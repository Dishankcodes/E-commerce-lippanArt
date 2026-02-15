<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit();
}
$customer_id = (int) $_SESSION['customer_id'];


/* 📧 GET CUSTOMER EMAIL */
$stmt = mysqli_prepare(
  $conn,
  "SELECT email FROM customers WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$userRes = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($userRes);

if (!$user) {
  // Safety fallback
  header("Location: logout.php");
  exit();
}

$email = $user['email'];

/* 📦 FETCH ORDERS */
$stmt = mysqli_prepare(
  $conn,
  "SELECT * FROM orders 
   WHERE customer_email = ? 
   ORDER BY id DESC"
);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
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

    <?php if (mysqli_num_rows($orders) === 0): ?>
      <p class="muted">You haven’t placed any orders yet.</p>
    <?php endif; ?>

    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
      <div class="card">
        <p><strong>Order #<?= (int)$o['id'] ?></strong></p>
        <p>Status: <?= htmlspecialchars($o['tracking_status']) ?></p>
        <p>Total: ₹<?= number_format($o['final_amount'], 2) ?></p>
        <p>Date: <?= date("d M Y", strtotime($o['created_at'])) ?></p>

        <a href="track-order.php?id=<?= (int)$o['id'] ?>" class="btn">
          View / Track
        </a>
      </div>
    <?php endwhile; ?>
  </div>

</body>

</html>