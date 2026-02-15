<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];
$order_id = (int) ($_GET['id'] ?? 0);

/* FETCH ORDER */
$order = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM custom_orders
    WHERE id = '$order_id'
      AND customer_id = '$customer_id'
      AND payment_status = 'Requested'
    LIMIT 1
"));

if (!$order) {
    header("Location: order-history.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Confirm Payment | Auraloom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: #0f0d0b;
            color: #f3ede7;
            font-family: Poppins, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: #171411;
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 50px;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
        }

        .muted {
            color: #b9afa6;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .amount {
            font-size: 42px;
            color: #c46a3b;
            margin: 30px 0;
            font-family: 'Playfair Display', serif;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: #c46a3b;
            color: #fff;
            border: none;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
        }

        .success {
            display: none;
        }

        .check {
            font-size: 60px;
            color: #7dd87d;
        }
    </style>
</head>

<body>

    <div class="card" id="paymentBox">

        <h1>Confirm Payment</h1>
        <p class="muted">Admin-approved custom order</p>

        <p><strong>Order #<?= $order['id'] ?></strong></p>

        <div class="amount">
            ₹<?= number_format($order['amount'], 2) ?>
        </div>

        <button class="btn" onclick="confirmPayment()">
            ✅ Payment Done
        </button>

        <p class="muted" style="margin-top:20px">
            This confirms that you’ve completed the payment
        </p>
    </div>

    <div class="card success" id="successBox">
        <div class="check">✔</div>
        <h1>Payment Successful</h1>
        <p class="muted">
            Your order is now confirmed.<br>
            Our team will start working on it.
        </p>

        <button class="btn" onclick="window.location='order-history.php'">
            Go to My Orders
        </button>
    </div>

    <script>
        function confirmPayment() {
            fetch("pay-custom.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "order_id=<?= $order['id'] ?>"
            })
                .then(() => {
                    document.getElementById("paymentBox").style.display = "none";
                    document.getElementById("successBox").style.display = "block";
                });
        }
    </script>

</body>

</html>