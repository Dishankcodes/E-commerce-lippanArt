<?php
session_start();
include("db.php");

/* ===== ONLY POST ALLOWED ===== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: b2b.php");
  exit;
}

/* ===== CUSTOMER ID (can be NULL) ===== */
$customer_id = $_SESSION['customer_id'] ?? null;

/* ===== SANITIZE INPUT ===== */
$business_name = trim($_POST['business_name']);
$business_type = trim($_POST['business_type']);
$quantity = (int) $_POST['quantity'];
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$message = trim($_POST['message'] ?? '');

$reference_type = trim($_POST['reference_type'] ?? '');
$reference_value = trim($_POST['reference_value'] ?? '');

$reference_image = null;

/* ===== IMAGE UPLOAD ===== */
if (!empty($_FILES['reference_image']['name'])) {

  $dir = "uploads/b2b/";
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }

  $ext = strtolower(pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION));
  $allowed = ['jpg', 'jpeg', 'png', 'webp'];

  if (in_array($ext, $allowed)) {
    $filename = "b2b_" . time() . "_" . rand(1000, 9999) . "." . $ext;

    if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $dir . $filename)) {
      $reference_image = $filename;
    }
  }
}

/* ===== INSERT QUERY ===== */
$stmt = mysqli_prepare($conn, "
  INSERT INTO b2b_enquiries
  (customer_id, business_name, business_type, quantity, phone, email, message, reference_type, reference_value, reference_image)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param(
  $stmt,
  "ississssss",
  $customer_id,
  $business_name,
  $business_type,
  $quantity,
  $phone,
  $email,
  $message,
  $reference_type,
  $reference_value,
  $reference_image
);

/* ===== EXECUTE ===== */
mysqli_stmt_execute($stmt);

/* ===== CLEAN UP ===== */
mysqli_stmt_close($stmt);

/* ===== REDIRECT ===== */
header("Location: b2b.php?success=1");
exit;
