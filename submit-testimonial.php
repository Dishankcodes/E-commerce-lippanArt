<?php
session_start();
include("db.php");

/* ===== VALIDATION ===== */
if (!isset($_POST['order_id'], $_POST['message'])) {
  die("Invalid request");
}

$order_id = (int) $_POST['order_id'];
$message = trim($_POST['message']);

if ($order_id <= 0 || $message === '') {
  die("Invalid testimonial data");
}

/* ===== VERIFY ORDER EXISTS ===== */
$res = mysqli_query(
  $conn,
  "SELECT customer_name FROM orders WHERE id = $order_id LIMIT 1"
);

$order = mysqli_fetch_assoc($res);

if (!$order) {
  die("Order not found");
}

/* ===== INSERT TESTIMONIAL (PENDING) ===== */
mysqli_query($conn, "
  INSERT INTO testimonials
    (customer_name, message, approved, created_at)
  VALUES
    (
      '" . mysqli_real_escape_string($conn, $order['customer_name']) . "',
      '" . mysqli_real_escape_string($conn, $message) . "',
      0,
      NOW()
    )
");

/* ===== REDIRECT BACK TO TRACK ORDER ===== */
header("Location: track-order.php?id=$order_id&testimonial=success");
exit;
