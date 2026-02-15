<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: b2b.php");
  exit;
}

$business_name  = mysqli_real_escape_string($conn, $_POST['business_name']);
$business_type  = mysqli_real_escape_string($conn, $_POST['business_type']);
$quantity       = (int) $_POST['quantity'];
$phone          = mysqli_real_escape_string($conn, $_POST['phone']);
$email          = mysqli_real_escape_string($conn, $_POST['email']);
$message        = mysqli_real_escape_string($conn, $_POST['message']);

$reference_type  = mysqli_real_escape_string($conn, $_POST['reference_type'] ?? '');
$reference_value = mysqli_real_escape_string($conn, $_POST['reference_value'] ?? '');

$reference_image = null;

/* ===== IMAGE UPLOAD ===== */
if (!empty($_FILES['reference_image']['name'])) {

  $dir = "uploads/b2b/";
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }

  $ext = pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION);
  $filename = "b2b_" . time() . "_" . rand(1000,9999) . "." . $ext;

  if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $dir . $filename)) {
    $reference_image = $filename;
  }
}

/* ===== INSERT ===== */
mysqli_query($conn, "
  INSERT INTO b2b_enquiries
  (business_name, business_type, quantity, phone, email, message,
   reference_type, reference_value, reference_image, status, created_at)
  VALUES
  (
    '$business_name',
    '$business_type',
    $quantity,
    '$phone',
    '$email',
    '$message',
    '$reference_type',
    '$reference_value',
    '$reference_image',
    'new',
    NOW()
  )
");

/* ===== REDIRECT ===== */
header("Location: b2b.php?success=1");
exit;
