<?php
session_start();
include("db.php");
date_default_timezone_set('Asia/Kolkata');
require_once 'PHPMailer/mailer.php';

/* =============================
   PREFILL CUSTOMER DETAILS
============================= */
$prefill_name = '';
$prefill_email = '';
$prefill_phone = '';

$customer_id = $_SESSION['customer_id'] ?? null;

if ($customer_id) {
    $res = mysqli_query($conn, "
        SELECT name, email, phone 
        FROM customers 
        WHERE id = $customer_id 
        LIMIT 1
    ");

    if ($res && mysqli_num_rows($res) === 1) {
        $u = mysqli_fetch_assoc($res);
        $prefill_name = $u['name'];
        $prefill_email = $u['email'];
        $prefill_phone = $u['phone'];
    }
}

/* =============================
   CART CHECK
============================= */
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

/* =============================
   CALCULATE TOTALS
============================= */
$pricing = $_SESSION['pricing'] ?? null;

if (!$pricing) {
    header("Location: cart.php");
    exit;
}

$grand_total = $pricing['subtotal'];
$discount_percent = $pricing['discount_percent'];
$discount_amount = $pricing['discount_amount'];
$final_total = $pricing['final_total'];

/* =============================
   PLACE ORDER
============================= */
$stock_issue = false;
$stock_products = [];

if (isset($_POST['place_order'])) {

    foreach ($_SESSION['cart'] as $id => $qty) {
        $p = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT name, stock FROM products WHERE id=$id")
        );

        if (!$p || $p['stock'] < $qty) {
            $stock_issue = true;
            $stock_products[] = $p['name'] ?? 'Product';
        }
    }

    /* =============================
      CONTINUE ONLY IF STOCK OK
   ============================= */
    if (!$stock_issue) {

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        // Keep logged-in email safe
        if ($customer_id) {
            $email = $prefill_email;
        }

        $customer_id_sql = $customer_id ? "'$customer_id'" : "NULL";

        /* INSERT ORDER */
        mysqli_query($conn, "
            INSERT INTO orders (
                customer_id,
                customer_name,
                customer_email,
                customer_phone,
                address,
                total_amount,
                discount_amount,
                final_amount
            ) VALUES (
                $customer_id_sql,
                '$name',
                '$email',
                '$phone',
                '$address',
                '$grand_total',
                '$discount_amount',
                '$final_total'
            )
        ");

        $order_id = mysqli_insert_id($conn);

        // =============================
// SEND EMAIL TO ALL ADMINS
// =============================


        /* INSERT ORDER ITEMS + UPDATE STOCK */
        foreach ($_SESSION['cart'] as $id => $qty) {

            $product = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT price FROM products WHERE id = $id")
            );

            mysqli_query($conn, "
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES ('$order_id', '$id', '$qty', '{$product['price']}')
            ");

            mysqli_query($conn, "
                UPDATE products
                SET stock = stock - $qty
                WHERE id = $id
            ");
        }
        /* =============================
           ADMIN EMAIL NOTIFICATION
        ============================= */

        // Fetch ordered items
        $orderItems = [];
        $resItems = mysqli_query($conn, "
    SELECT p.name, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = $order_id
");

        while ($row = mysqli_fetch_assoc($resItems)) {
            $orderItems[] = $row;
        }

        // Build items table
        $itemsHtml = '';
        foreach ($orderItems as $item) {
            $itemsHtml .= "
    <tr>
        <td style='padding:8px; border-bottom:1px solid #eee;'>
            {$item['name']}
        </td>
        <td style='padding:8px; text-align:center; border-bottom:1px solid #eee;'>
            {$item['quantity']}
        </td>
        <td style='padding:8px; text-align:right; border-bottom:1px solid #eee;'>
            ₹" . number_format($item['price'], 2) . "
        </td>
    </tr>
    ";
        }

        $orderDate = date("d M Y, h:i A");

        // Email body
        $emailBody = "
<h2> New Order Received</h2>

<p><b>Order ID:</b> #$order_id</p>
<p><b>Customer:</b> $name</p>
<p><b>Email:</b> $email</p>
<p><b>Phone:</b> $phone</p>
<p><b>Date:</b> $orderDate</p>
<p><b>Shipping Address:</b><br>
    <span style=\"white-space:pre-line;\">$address</span>
</p>


<table width='100%' cellpadding='0' cellspacing='0'
       style='border-collapse:collapse; margin-top:15px;'>
    <thead>
        <tr>
            <th align='left'>Product</th>
            <th align='center'>Qty</th>
            <th align='right'>Price</th>
        </tr>
    </thead>
    <tbody>
        $itemsHtml
    </tbody>
</table>

<p style='margin-top:15px; font-size:16px;'>
    <b>Total Amount:</b> ₹" . number_format($final_total, 2) . "
</p>
";

        // Send to all admins
        $admins = mysqli_query($conn, "SELECT email FROM admins");
        while ($a = mysqli_fetch_assoc($admins)) {
            sendMail(
                $a['email'],
                "🛒 New Order Received (#$order_id)",
                $emailBody
            );
        }



        /* =============================
CUSTOMER ORDER CONFIRMATION EMAIL
============================= */

        // Reuse items list (simpler for customer)
        $customerItemsHtml = '';
        foreach ($orderItems as $item) {
            $customerItemsHtml .= "<li>{$item['name']} × {$item['quantity']}</li>";
        }

        $customerEmailBody = "
<h2> Order Confirmed!</h2>

<p>Hi <b>$name</b>,</p>

<p>Thank you for shopping with <b>Auraloom</b>.  
Your order has been placed successfully.</p>

<p><b>Order ID:</b> #$order_id</p>
<p><b>Order Date:</b> $orderDate</p>

<h3>Items Ordered</h3>
<ul>
    $customerItemsHtml
</ul>

<p><b>Subtotal:</b> ₹" . number_format($grand_total, 2) . "</p>";

        if ($discount_amount > 0) {
            $customerEmailBody .= "<p><b>Discount:</b> − ₹" . number_format($discount_amount, 2) . "</p>";
        }

        $customerEmailBody .= "
<p><b>Total Paid:</b> ₹" . number_format($final_total, 2) . "</p>

<p><b>Shipping Address:</b><br>
" . nl2br(htmlspecialchars($address)) . "</p>

<p style='margin-top:20px;'>
You will receive another email when your order status changes.
</p>

<p>
Warm regards,<br>
<b>Auraloom Team</b>
</p>
";

        // ✅ Send confirmation to customer
        sendMail(
            $email,
            "🧾 Order Confirmation – Auraloom (#$order_id)",
            $customerEmailBody
        );


        /* CLEAN SESSION */
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);

        $_SESSION['last_order_id'] = $order_id;

        // Redirect user immediately
        header("Location: order-success.php");
        fastcgi_finish_request(); // <-- KEY LINE (non-blocking)

        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | AURALOOM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ================= HEADER ================= */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            height: 80px;
            z-index: 1000;
            background: rgba(15, 13, 11, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-soft);
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            padding: 0 80px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            letter-spacing: 2px;
            color: var(--text-main);
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 34px;
        }

        nav a {
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            text-decoration: none;
            position: relative;
            padding-bottom: 6px;
        }

        nav a:hover {
            color: var(--text-main);
        }

        nav a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0%;
            height: 1px;
            background: var(--accent);
            transition: 0.35s ease;
        }

        nav a:hover::after {
            width: 100%;
        }

        .back-cart-btn {
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--accent);
            text-decoration: none;
            border-bottom: 1px solid var(--accent);
        }

        /* ================= LAYOUT ================= */
        .page-wrap {
            padding-top: 140px;
            padding-bottom: 100px;
        }

        h3 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            margin-bottom: 40px;
            color: var(--text-main);
        }

        h5 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--accent);
        }

        /* ================= FORM VISIBILITY FIXES ================= */
        label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 5px;
        }

        .form-control,
        textarea {
            background: transparent !important;
            border: none !important;
            border-bottom: 1px solid var(--border-soft) !important;
            border-radius: 0 !important;
            color: var(--text-main) !important;
            padding: 12px 0 !important;
            font-size: 15px !important;
            margin-bottom: 25px;
        }

        .form-control:focus,
        textarea:focus {
            box-shadow: none !important;
            border-bottom-color: var(--accent) !important;
        }

        .form-control::placeholder {
            color: #555;
            font-size: 14px;
        }

        /* ================= SQUARE BUTTONS ================= */
        .btn-brand-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 16px;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
            transition: 0.4s;
            width: 100%;
            border-radius: 0 !important;
            /* Square Style */
        }

        .btn-brand-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            color: #fff;
        }

        .btn-brand-outline {
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 12px 24px;
            background: transparent;
            margin-bottom: 35px;
            border-radius: 0 !important;
            /* Square Style */
            transition: 0.3s;
        }

        .btn-brand-outline:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* ================= SUMMARY CARD ================= */
        .checkout-card {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-soft) !important;
            padding: 40px !important;
            border-radius: 0 !important;
            color: var(--text-main);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 15px;
            color: var(--text-main);
        }

        .summary-row .label {
            color: var(--text-muted);
        }

        .total-row {
            color: var(--accent) !important;
            font-size: 24px !important;
            font-weight: 500 !important;
            border-top: 1px solid var(--border-soft);
            padding-top: 20px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-family: 'Playfair Display', serif;
        }

        @media (max-width: 900px) {
            header {
                padding: 0 30px;
            }

            nav {
                display: none;
            }

            .page-wrap {
                padding-top: 110px;
            }

            h3 {
                font-size: 32px;
            }
        }

        .checkout-items {
            margin-top: 25px;
        }

        .checkout-item {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 15px;
            align-items: center;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .item-name {
            font-size: 14px;
        }

        .item-price {
            font-size: 12px;
            color: var(--text-muted);
        }

        .qty-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-soft);
        }

        .qty-control button {
            width: 26px;
            height: 26px;
            background: transparent;
            border: none;
            color: var(--text-main);
            cursor: pointer;
        }

        .qty-control span {
            width: 28px;
            text-align: center;
            font-size: 13px;
        }

        .item-total {
            font-weight: 500;
        }

        .stock-warning {
            background: rgba(255, 107, 107, 0.08);
            border: 1px solid rgba(255, 107, 107, 0.3);
            padding: 25px;
            margin-bottom: 40px;
        }

        .stock-warning strong {
            color: #ff6b6b;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .stock-warning p {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 8px;
        }

        .stock-actions {
            margin-top: 18px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-outline {
            padding: 10px 22px;
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-primary {
            padding: 10px 22px;
            background: var(--accent);
            color: #fff;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .item-remove {
            margin-top: 6px;
        }

        .item-remove a {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }

        .item-remove a:hover {
            color: #ff6b6b;
            border-bottom-color: #ff6b6b;
        }

        .checkout-item {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 15px;
            align-items: center;
        }

        .item-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .item-remove {
            margin-top: 4px;
        }

        .item-remove a {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }

        .item-remove a:hover {
            color: #ff6b6b;
            border-bottom-color: #ff6b6b;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">AURALOOM</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="collection.php">Shop</a>
            <a href="custom-order.php">Custom</a>
            <a href="b2b.php">B2B</a>
            <a href="about-us.php">About</a>
        </nav>
        <a href="cart.php" class="back-cart-btn">← Back to Cart</a>
    </header>

    <div class="container page-wrap">
        <h3>Shipping Details</h3>

        <div class="row g-5">
            <div class="col-md-6">
                <?php if ($stock_issue): ?>
                    <div style="
                        background:rgba(255,107,107,.08);
                 border:1px solid rgba(255,107,107,.3);
                     padding:22px;
                     margin-bottom:30px;
                ">
                        <strong style="color:#ff6b6b; letter-spacing:1px;">
                            Some items are no longer available
                        </strong>

                        <p style="font-size:13px; color:var(--text-muted); margin-top:6px;">
                            <?= implode(', ', $stock_products) ?> exceeded available stock.
                            Please update your cart or continue shopping.
                        </p>

                        <div style="margin-top:14px; display:flex; gap:12px; flex-wrap:wrap;">
                            <a href="cart.php" class="btn-outline">Update Cart</a>
                            <a href="collection.php" class="btn-primary">Add More Products</a>
                        </div>
                    </div>
                <?php endif; ?>


                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Your Name"
                        value="<?= htmlspecialchars($prefill_name) ?>" required>

                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com"
                        value="<?= htmlspecialchars($prefill_email) ?>" required>

                    <label>Contact Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX"
                        value="<?= htmlspecialchars($prefill_phone) ?>" required>

                    <label>Shipping Address</label>
                    <textarea name="address" class="form-control" placeholder="Street, City, Pincode..." rows="4"
                        required></textarea>

                    <button type="button" onclick="getLocation()" class="btn btn-brand-outline">
                        📍 Detect Current Location
                    </button>

                    <button type="submit" name="place_order" class="btn btn-brand-primary">
                        Complete Order
                    </button>
                </form>
            </div>

            <div class="col-md-6">
                <div class="card checkout-card">
                    <h5>Order Summary</h5>
                    <div class="checkout-items">

                        <?php foreach ($_SESSION['cart'] as $id => $qty):
                            $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, price, stock FROM products WHERE id=$id"));
                            if (!$p)
                                continue;
                            ?>
                            <div class="checkout-item">

                                <!-- LEFT COLUMN -->
                                <div class="item-info">
                                    <div class="item-name">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </div>

                                    <div class="item-price">
                                        ₹<?= number_format($p['price'], 2) ?>
                                    </div>

                                    <!-- REMOVE (same behavior as cart.php) -->
                                    <div class="item-remove">
                                        <a href="cart.php?remove=<?= $id ?>"
                                            onclick="return confirm('Remove this item from cart?')">
                                            Remove
                                        </a>
                                    </div>
                                </div>

                                <!-- QTY -->
                                <div class="qty-control">
                                    <button type="button" onclick="updateQty(<?= $id ?>, -1, <?= $p['stock'] ?>)">−</button>
                                    <span id="qty-<?= $id ?>"><?= $qty ?></span>
                                    <button type="button" onclick="updateQty(<?= $id ?>, 1, <?= $p['stock'] ?>)">+</button>
                                </div>
                                <div class="stock-msg" id="stock-msg-<?= $id ?>"
                                    style="display:none; font-size:11px; color:#ff6b6b; margin-top:6px;">
                                </div>

                                <!-- TOTAL -->
                                <div class="item-total" id="item-total-<?= $id ?>">
                                    ₹<?= number_format($p['price'] * $qty, 2) ?>
                                </div>

                            </div>


                        <?php endforeach; ?>

                    </div>

                    <hr style="margin:25px 0; border-color:var(--border-soft)">
                    <div class="summary-row mt-4">
                        <span class="label">Subtotal</span>
                        <span id="checkout-subtotal">₹<?= number_format($grand_total, 2) ?></span>
                    </div>

                    <div class="summary-row" id="discount-row"
                        style="<?= $discount_amount > 0 ? 'display:flex;' : 'display:none;' ?>; color:#7dd87d;">
                        <span class="label">
                            Discount (<span id="discount-percent"><?= $discount_percent ?></span>% OFF)
                        </span>
                        <span>− ₹<span id="discount-amount"><?= number_format($discount_amount, 2) ?></span></span>
                    </div>



                    <div class="summary-row">
                        <span class="label">Shipping</span>
                        <span style="color: #7dd87d;">Complimentary</span>
                    </div>

                    <div class="total-row">
                        <span>Total Payable</span>
                        <span id="checkout-total">₹<?= number_format($final_total, 2) ?></span>
                    </div>

                    <p
                        style="font-size:11px; color:var(--text-muted); margin-top:40px; text-align:center; letter-spacing:1px; text-transform: uppercase;">
                        Handcrafted in India · Secure Checkout
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation not supported.");
            }
        }

        function showPosition(position) {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&zoom=18&lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    let address = data.display_name;
                    if (confirm("Detected address:\n" + address + "\n\nUse this address?")) {
                        document.querySelector("textarea[name='address']").value = address;
                    }
                })
                .catch(error => {
                    alert("Unable to fetch address automatically.");
                });
        }
    </script>
</body>
<script>
    function updateQty(productId, delta, maxStock) {

        const qtySpan = document.getElementById('qty-' + productId);
        const msgBox = document.getElementById('stock-msg-' + productId);

        let currentQty = parseInt(qtySpan.innerText);
        let newQty = currentQty + delta;

        // reset message
        msgBox.style.display = 'none';
        msgBox.innerText = '';

        // ❌ below 1 (do nothing)
        if (newQty < 1) return;

        // ❌ exceeds stock → show message
        if (newQty > maxStock) {
            msgBox.innerText = `Only ${maxStock} item(s) available in stock`;
            msgBox.style.display = 'block';
            return;
        }

        // ✅ valid → call backend
        fetch('cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `ajax_update=1&product_id=${productId}&quantity=${newQty}`
        })
            .then(res => res.json())
            .then(data => {

                if (data.status !== 'ok') return;

                // Qty
                qtySpan.innerText = newQty;

                // Item total
                document.getElementById('item-total-' + productId).innerText =
                    '₹' + data.item_subtotal;

                // Subtotal
                document.getElementById('checkout-subtotal').innerText =
                    '₹' + data.cart_subtotal;

                // Discount
                const discountRow = document.getElementById('discount-row');

                if (parseFloat(data.discount) > 0) {
                    discountRow.style.display = 'flex';
                    document.getElementById('discount-amount').innerText = data.discount;
                    document.getElementById('discount-percent').innerText = data.discount_pct;
                } else {
                    discountRow.style.display = 'none';
                }

                // Final total
                document.getElementById('checkout-total').innerText =
                    '₹' + data.final_total;
            });
    }
</script>


</html>