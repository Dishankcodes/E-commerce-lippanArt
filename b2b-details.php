<?php
session_start();
include("db.php");

/* ===== LOGIN REQUIRED ===== */
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];
$b2b_id = (int) ($_GET['id'] ?? 0);

if ($b2b_id <= 0) {
    header("Location: account.php");
    exit;
}

/* ===== FETCH ENQUIRY (SECURE) ===== */
$res = mysqli_query($conn, "
    SELECT *
    FROM b2b_enquiries
    WHERE id = $b2b_id
      AND customer_id = $customer_id
    LIMIT 1
");

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: account.php");
    exit;
}

$b = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>B2B Enquiry #<?= $b['id'] ?> | Auraloom</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #0f0d0b;
            color: #f3ede7;
        }

        .container {
            max-width: 900px;
            margin: 120px auto;
            padding: 40px;
            background: #171411;
            border: 1px solid rgba(255, 255, 255, .12);
        }

        h2 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 20px;
        }

        .label {
            color: #b9afa6;
            font-size: 12px;
            margin-top: 18px;
        }

        .value {
            font-size: 15px;
            margin-top: 6px;
        }

        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 12px;
            border: 1px solid #c46a3b;
            color: #c46a3b;
            font-size: 11px;
            text-transform: uppercase;
        }

        img {
            max-width: 200px;
            border: 1px solid rgba(255, 255, 255, .2);
            margin-top: 10px;
        }

        .btn {
            margin-top: 30px;
            display: inline-block;
            padding: 12px 28px;
            border: 1px solid #c46a3b;
            color: #c46a3b;
            text-decoration: none;
        }

        .btn:hover {
            background: #c46a3b;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>B2B Enquiry #<?= $b['id'] ?></h2>
        <span class="badge"><?= ucfirst($b['status']) ?></span>

        <div class="label">Business Name</div>
        <div class="value"><?= htmlspecialchars($b['business_name']) ?></div>

        <div class="label">Business Type</div>
        <div class="value"><?= htmlspecialchars($b['business_type']) ?></div>

        <div class="label">Estimated Quantity</div>
        <div class="value"><?= $b['quantity'] ?></div>

        <div class="label">Contact</div>
        <div class="value">
            <?= htmlspecialchars($b['email']) ?><br>
            <?= htmlspecialchars($b['phone']) ?>
        </div>

        <?php if (!empty($b['message'])): ?>
            <div class="label">Project Requirements</div>
            <div class="value"><?= nl2br(htmlspecialchars($b['message'])) ?></div>
        <?php endif; ?>

        <?php if (!empty($b['reference_type'])): ?>
            <div class="label">Reference</div>
            <div class="value">
                <?= htmlspecialchars($b['reference_type']) ?> :
                <?= htmlspecialchars($b['reference_value']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($b['reference_image'])): ?>
            <div class="label">Reference Image</div>
            <img src="uploads/b2b/<?= $b['reference_image'] ?>">
        <?php endif; ?>

        <div class="label">Submitted On</div>
        <div class="value"><?= date("d M Y, h:i A", strtotime($b['created_at'])) ?></div>

        <a href="order-history.php" class="btn">← Back to Enquiries</a>

    </div>

</body>

</html>