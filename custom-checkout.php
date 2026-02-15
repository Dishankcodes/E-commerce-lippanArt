<?php
session_start();
include("db.php");

/* =============================
   LOGIN CHECK
============================= */
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];

/* =============================
   VALIDATE ORDER ID
============================= */
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$order_id = (int) $_GET['id'];

/* =============================
   FETCH CUSTOM ORDER
   (ADMIN-SET AMOUNT ONLY)
============================= */
$order_q = mysqli_query($conn, "
    SELECT *
    FROM custom_orders
    WHERE id = '$order_id'
      AND email = (
        SELECT email FROM customers WHERE id = '$customer_id'
      )
      AND payment_status = 'Requested'
    LIMIT 1
");

$order = mysqli_fetch_assoc($order_q);
if (!$order) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Payment Status | Auraloom</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <style>
            body{
                background:#0f0d0b;
                font-family:Poppins,sans-serif;
                color:#f3ede7;
                display:flex;
                align-items:center;
                justify-content:center;
                min-height:100vh;
            }
            .card{
                background:#171411;
                border:1px solid rgba(255,255,255,.12);
                border-radius:14px;
                padding:40px;
                max-width:420px;
                text-align:center;
            }
            .icon{
                font-size:40px;
                margin-bottom:10px;
            }
            .btn{
                display:inline-block;
                margin-top:20px;
                padding:12px 26px;
                background:#c46a3b;
                color:#fff;
                text-decoration:none;
                border-radius:10px;
                font-size:14px;
            }
            .muted{
                color:#b9afa6;
                font-size:14px;
                margin-top:10px;
            }
        </style>
    </head>
    <body>

        <div class="card">
            <div class="icon">✅</div>
            <h2>Payment Already Handled</h2>
            <p class="muted">
                This payment request is no longer active.<br>
                Either the payment is already completed or the request expired.
            </p>

            <a href="order-history.php" class="btn">
                Go to My Orders
            </a>
        </div>

    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Custom Order Payment | Auraloom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: #0f0d0b;
            color: #f3ede7;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 520px;
            margin: 80px auto;
            background: #171411;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 16px;
            padding: 30px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
        }

        .muted {
            color: #b9afa6;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .box {
            background: #1b1815;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .box strong {
            color: #f3ede7;
        }

        .amount {
            font-size: 26px;
            color: #c46a3b;
            font-weight: 600;
            margin: 20px 0;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #c46a3b;
            color: #fff;
            border: none;
            font-size: 14px;
            letter-spacing: 1px;
            cursor: pointer;
            border-radius: 10px;
        }

        .btn:hover {
            background: #a85830;
        }

        .secure {
            text-align: center;
            font-size: 12px;
            color: #b9afa6;
            margin-top: 14px;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Custom Order Payment</h1>
        <p class="muted">Admin-approved quotation</p>

        <div class="box">
            <p><strong>Order Type:</strong><br>
                <?= htmlspecialchars($order['order_type']) ?>
            </p>

            <p style="margin-top:10px">
                <strong>Description:</strong><br>
                <?= nl2br(htmlspecialchars($order['idea'])) ?>
            </p>
        </div>

        <div class="box">
            <strong>Order ID:</strong> #<?= $order['id'] ?><br>
            <strong>Status:</strong> Payment Requested
        </div>

        <div class="amount">
            ₹<?= number_format($order['amount'], 2) ?>
        </div>

        <!-- PAYMENT SUBMIT -->
        <form method="post" action="pay-custom.php">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <button class="btn">Proceed to Pay</button>
        </form>

        <p class="secure">🔒 Secure payment · Amount fixed by admin</p>

    </div>

</body>

</html>