<?php
include("db.php");

$order_id = $_GET['order_id'];

$order = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM orders WHERE id='$order_id'"));
?>

<h3>Track Order #<?php echo $order_id; ?></h3>

<p>Status: <strong><?php echo $order['tracking_status']; ?></strong></p>
