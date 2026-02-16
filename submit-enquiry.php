<?php
session_start();
include("db.php");
date_default_timezone_set('Asia/Kolkata');
require_once 'PHPMailer/mailer.php';


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



$enquiryDate = date("d M Y, h:i A");

/* =============================
   ADMIN EMAIL
============================= */

$adminBody = "
<h2>📩 New B2B Enquiry Received</h2>

<p><b>Business Name:</b> {$business_name}</p>
<p><b>Business Type:</b> {$business_type}</p>
<p><b>Estimated Quantity:</b> {$quantity}</p>

<p><b>Contact Email:</b> {$email}</p>
<p><b>Phone:</b> {$phone}</p>

<p><b>Reference Type:</b> " . ($reference_type ?: '—') . "</p>
<p><b>Reference Details:</b> " . ($reference_value ?: '—') . "</p>

<p><b>Message:</b><br>
" . nl2br(htmlspecialchars($message ?: '—')) . "
</p>

<p><b>Submitted On:</b> {$enquiryDate}</p>
";

/* Send to all admins */
$admins = mysqli_query($conn, "SELECT email FROM admins");
while ($a = mysqli_fetch_assoc($admins)) {
  sendMail(
    $a['email'],
    " New B2B Enquiry ~ {$business_name}",
    $adminBody
  );
}

/* =============================
   CUSTOMER EMAIL
============================= */

$customerBody = "
<h2>Thank You for Contacting Auraloom</h2>

<p>Hi <b>{$business_name}</b>,</p>

<p>We received your B2B enquiry successfully.  
Our team will review your requirements and contact you shortly.</p>

<h3>Enquiry Summary</h3>

<ul>
  <li><b>Business Type:</b> {$business_type}</li>
  <li><b>Estimated Quantity:</b> {$quantity}</li>
  <li><b>Submitted On:</b> {$enquiryDate}</li>
</ul>

<p><b>Your Message:</b><br>
" . nl2br(htmlspecialchars($message ?: '—')) . "
</p>

<p>
Warm regards,<br>
<b>Auraloom Team</b>
</p>
";

sendMail(
  $email,
  " We’ve Received Your B2B Enquiry ~ Auraloom",
  $customerBody
);

/* ===== REDIRECT ===== */
header("Location: b2b.php?success=1");
exit;
