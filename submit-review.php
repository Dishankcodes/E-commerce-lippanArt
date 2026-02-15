<?php
session_start();
include("db.php");

/* =============================
   LOGIN CHECK
============================= */
if (!isset($_SESSION['customer_id'])) {
  die("Login required");
}

$user_id = (int) $_SESSION['customer_id'];

/* =============================
   INPUTS
============================= */
$product_id = (int) ($_POST['product_id'] ?? 0);
$order_id = (int) ($_POST['order_id'] ?? 0);
$rating = (int) ($_POST['rating'] ?? 0);
$review = trim($_POST['review_text'] ?? '');

if ($product_id <= 0 || $order_id <= 0 || $rating < 1 || $rating > 5 || empty($review)) {
  die("Invalid review data");
}

/* =============================
   VERIFY ORDER OWNERSHIP + DELIVERY
============================= */
$order = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT o.id
    FROM orders o
    JOIN customers c ON c.email = o.customer_email
    WHERE o.id = $order_id
      AND o.order_status = 'Delivered'
      AND c.id = $user_id
"));

if (!$order) {
  die("Invalid order");
}

/* =============================
   PREVENT DUPLICATE REVIEW
============================= */
$check = mysqli_query($conn, "
    SELECT id
    FROM product_reviews
    WHERE product_id = $product_id
      AND order_id   = $order_id
      AND user_id    = $user_id
    LIMIT 1
");

if (mysqli_num_rows($check) > 0) {
  die("You already reviewed this product.");
}

/* =============================
   INSERT REVIEW
============================= */
$review_safe = mysqli_real_escape_string($conn, $review);

$ins = mysqli_query($conn, "
    INSERT INTO product_reviews
      (product_id, user_id, order_id, rating, review_text, status)
    VALUES
      ($product_id, $user_id, $order_id, $rating, '$review_safe', 'pending')
");

if (!$ins) {
  die("Error: " . mysqli_error($conn));
}

/* =============================
   REDIRECT
============================= */
header("Location: product.php?id=$product_id");
exit;
