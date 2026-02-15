<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = (int) $_POST['order_id'];
$customer_id = (int) $_SESSION['customer_id'];

/* VERIFY ORDER */
$order = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id
    FROM custom_orders
    WHERE id='$order_id'
      AND email = (
        SELECT email FROM customers WHERE id='$customer_id'
      )
      AND payment_status='Requested'
    LIMIT 1
"));
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

/* MARK AS PAID (SIMULATED PAYMENT) */
mysqli_query($conn, "
    UPDATE custom_orders
    SET payment_status='Paid'
    WHERE id='$order_id'
");

/* REDIRECT */
header("Location: order-history.php");
exit;
