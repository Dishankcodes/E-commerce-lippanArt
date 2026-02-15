<?php
session_start();
include("db.php");

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];

// Fetch order
$order = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id'")
);

// Fetch items
$items = mysqli_query(
    $conn,
    "SELECT oi.*, p.name 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id='$order_id'"
);

// Clear session order id (important)
unset($_SESSION['last_order_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | Auraloom</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, .12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 750px;
            margin: 80px auto;
            padding: 50px;
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--accent);
        }

        .muted {
            color: var(--text-muted);
            font-size: 15px;
        }

        /* --- SUCCESS ANIMATION --- */
        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(196, 106, 59, 0.1);
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 25px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(196, 106, 59, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(196, 106, 59, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(196, 106, 59, 0); }
        }

        /* --- BUTTONS (Square consistent) --- */
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: var(--accent);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--text-main);
        }

        .btn-wa {
            background: #075e54;
            color: #fff;
            margin-top: 15px;
        }

        /* --- INFO BOX --- */
        .next-steps {
            margin-top: 40px;
            padding: 25px;
            background: var(--card-bg);
            border-left: 4px solid var(--accent);
            text-align: left;
        }

        .next-steps strong {
            color: var(--accent);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 8px;
        }

        /* --- TESTIMONIAL FORM --- */
        .testimonial-area {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid var(--border-soft);
        }

        textarea {
            width: 100%;
            background: transparent;
            border: 1px solid var(--border-soft);
            color: var(--text-main);
            padding: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            min-height: 130px;
            margin-bottom: 20px;
            resize: none;
            transition: 0.3s;
        }

        textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        hr { border: none; border-top: 1px solid var(--border-soft); margin: 35px 0; }

        @media (max-width: 768px) {
            .container { margin: 40px 20px; padding: 30px; }
            h1 { font-size: 28px; }
            .btn { width: 100%; margin: 10px 0; }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1>Order Confirmed</h1>
        <p class="muted">
            Thank you for choosing <strong>Auraloom</strong>.<br>
            Your handcrafted masterpiece is now in the making.
        </p>

        <div style="margin-top:30px; font-size:14px; letter-spacing: 0.5px;">
            <p>Invoice ID: <strong style="color:var(--text-main)">#AUR-<?= $order_id ?></strong></p>
            <p>Status: <span style="color:#9fd3a9; font-weight:500; text-transform:uppercase; font-size:12px;"><?= $order['order_status'] ?></span></p>
        </div>

        <hr>

        <h3>Summary</h3>
        <div style="max-width: 400px; margin: 0 auto;">
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px;">
                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                    <span style="color: var(--text-muted);">Handcrafted</span>
                </div>
            <?php endwhile; ?>

            <div style="margin-top:20px; padding-top:15px; border-top: 1px dashed var(--border-soft); display: flex; justify-content: space-between; font-weight: 500;">
                <span>Total Amount Paid:</span>
                <span style="color:var(--accent)">₹<?= number_format($order['final_amount'], 2) ?></span>
            </div>
        </div>

        <div class="next-steps">
            <strong>What happens next?</strong>
            <p class="muted" style="font-size: 13px;">
                Our artisans will begin preparing your order within <strong>24–48 hours</strong>. 
                You will receive a notification once your artwork is dispatched with real-time tracking details.
            </p>
        </div>

        <div style="margin-top:40px">
            <a href="track-order.php?id=<?= $order_id ?>" class="btn" style="width: 100%; margin-bottom: 15px;">
                Track My Masterpiece
            </a>
            <div style="display: flex; gap: 15px;">
                <a href="collection.php" class="btn btn-secondary" style="flex: 1;">Collections</a>
                <a href="index.php" class="btn btn-secondary" style="flex: 1;">Home</a>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <p class="muted" style="font-size: 13px;">Need assistance? We're available on WhatsApp.</p>
            <a href="https://wa.me/<?= $WHATSAPP_NUMBER ?>?text=Hi, regarding my order #AUR-<?= $order_id ?>" class="btn btn-wa">
                <i class="fab fa-whatsapp"></i> Chat with Us
            </a>
        </div>

        <div class="testimonial-area">
            <h3>Share Your Experience</h3>
            <p class="muted" style="margin-bottom: 25px;">Your feedback inspires our artisans to create even more magic.</p>

            <form method="post" action="submit-testimonial.php">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <textarea name="message" required placeholder="Tell us about your journey with Auraloom..."></textarea>
                <button class="btn" type="submit">Submit Feedback</button>
            </form>
        </div>

    </div>

</body>
</html>