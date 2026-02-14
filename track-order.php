<?php
include("db.php");

$id = (int)($_GET['id'] ?? 0);

$order = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT * FROM orders WHERE id='$id'")
);

if(!$order){
  die("Order not found");
}
?>

<h2>Order #<?= $id ?></h2>
<p>Status: <?= $order['tracking_status'] ?></p>
<p>Total: ₹<?= number_format($order['final_amount'],2) ?></p>
