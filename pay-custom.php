<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id']))
    exit;

$order_id = (int) $_POST['order_id'];
$customer_id = (int) $_SESSION['customer_id'];

mysqli_query($conn, "
  UPDATE custom_orders
  SET 
    payment_status = 'Paid',
    status = 'completed'
  WHERE id = '$order_id'
    AND customer_id = '$customer_id'
    AND payment_status = 'Requested'
");

header("Location: order-history.php");
exit;
