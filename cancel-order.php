<?php
session_start();
include("db.php");

/* LOGIN REQUIRED */
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$order_id = (int) ($_GET['id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

if ($order_id <= 0) {
  header("Location: order-history.php");
  exit();
}

/* GET USER EMAIL */
$user = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT email FROM customers WHERE id='$user_id'")
);

if (!$user) {
  header("Location: login.php");
  exit();
}

/* VERIFY ORDER OWNERSHIP + STATUS */
$order = mysqli_fetch_assoc(
  mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE id='$order_id' 
      AND customer_email='{$user['email']}'
    LIMIT 1
  ")
);

if (!$order) {
  die("Unauthorized access.");
}

/* ONLY PENDING CAN BE CANCELLED */
if ($order['order_status'] !== 'Pending') {
  die("This order can no longer be cancelled.");
}

/* CANCEL ORDER */
mysqli_query($conn, "
  UPDATE orders 
  SET 
    order_status='Cancelled',
    cancelled_at=NOW()
  WHERE id='$order_id'
");

/* OPTIONAL: RESTORE STOCK */
$items = mysqli_query(
  $conn,
  "SELECT product_id, quantity FROM order_items WHERE order_id='$order_id'"
);

while ($i = mysqli_fetch_assoc($items)) {
  mysqli_query($conn, "
    UPDATE products 
    SET stock = stock + {$i['quantity']}
    WHERE id='{$i['product_id']}'
  ");
}

header("Location: order-history.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=<?php if ($o['order_status'] !== 'Pending'): ?>
  <p class=" muted" style="margin-top:10px">
    Cancellation is not available at this stage.
    </p>

    <a href="https://wa.me/<?= $WHATSAPP_NUMBER ?>?text=Hi, I want to discuss cancellation for order #<?= $o['id'] ?>."
      class="btn secondary">
      💬 Talk to Support
    </a>
  <?php endif; ?>
  , initial-scale=1.0">
  <title>Document</title>
</head>

<body>

</body>

</html>