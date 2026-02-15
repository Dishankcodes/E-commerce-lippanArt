<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];

$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT name, email, phone FROM customers WHERE id=$customer_id")
);

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

/* =============================
   CALCULATE TOTALS
============================= */
$grand_total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
    $product = mysqli_fetch_assoc($product_query);

    if (!$product)
        continue;

    $grand_total += $product['price'] * $qty;
}

$discount = 0;
if (isset($_SESSION['coupon'])) {
    $discount = ($grand_total * $_SESSION['coupon']['discount_percent']) / 100;
}

$final_total = $grand_total - $discount;


/* =============================
   PLACE ORDER
============================= */
if (isset($_POST['place_order'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // 1️⃣ Insert order
    mysqli_query($conn, "INSERT INTO orders 
(customer_id, customer_name, customer_email, customer_phone, address, total_amount, discount_amount, final_amount)
VALUES
('$customer_id','$name','$email','$phone','$address','$grand_total','$discount','$final_total')");

    $order_id = mysqli_insert_id($conn);

    // 2️⃣ Build WhatsApp message FIRST
    $customer_message = "✅ *Order Confirmed!* \n\n";
    $customer_message .= "Hi $name 👋\n";
    $customer_message .= "Your order *#$order_id* has been placed successfully.\n\n";
    $customer_message .= "🧾 *Order Summary:*\n";

    foreach ($_SESSION['cart'] as $id => $qty) {

        $product = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT * FROM products WHERE id='$id'")
        );

        if (!$product || $product['stock'] < $qty) {
            die("Stock issue detected. Please try again.");
        }

        // 3️⃣ Save order items
        mysqli_query($conn, "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES
            ('$order_id','$id','$qty','{$product['price']}')");

        // 4️⃣ Reduce stock
        mysqli_query($conn, "UPDATE products 
            SET stock = stock - $qty
            WHERE id='$id'");

        $customer_message .= "• {$product['name']} × $qty\n";
    }

    $customer_message .= "\nSubtotal: ₹$grand_total";
    if ($discount > 0) {
        $customer_message .= "\nDiscount: −₹$discount";
    }
    $customer_message .= "\n*Total Paid: ₹$final_total*\n\n";
    $customer_message .= "🙏 Thank you for shopping with *Auraloom*";

    // 5️⃣ Clear cart ONLY ONCE
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);

    // 6️⃣ Normalize phone number (India)
    $customer_phone = preg_replace('/\D/', '', $phone);
    if (strlen($customer_phone) == 10) {
        $customer_phone = "91" . $customer_phone;
    }

    // // 7️⃣ Redirect to CUSTOMER WhatsApp
    // header("Location: https://wa.me/$customer_phone?text=" . urlencode($customer_message));
    // exit();

    $_SESSION['last_order_id'] = $order_id;
    header("Location: order-success.php");
    exit();

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    :root {
        --bg-dark: #0f0d0b;
        --bg-soft: #171411;
        --text-main: #f3ede7;
        --text-muted: #b9afa6;
        --accent: #c46a3b;
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
    }

    h3,
    h5 {
        font-family: 'Playfair Display', serif;
    }

    .container {
        max-width: 1100px;
    }

    .checkout-card {
        background: var(--bg-soft);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
    }

    .form-control,
    textarea {
        background: transparent;
        border: 1px solid var(--border-soft);
        color: var(--text-main);
    }

    .form-control::placeholder,
    textarea::placeholder {
        color: var(--text-muted);
    }

    .form-control:focus,
    textarea:focus {
        background: transparent;
        color: var(--text-main);
        border-color: var(--accent);
        box-shadow: none;
    }

    .btn-dark {
        background: var(--accent);
        border: none;
    }

    .btn-dark:hover {
        background: #a95a32;
    }

    .btn-outline-dark {
        border-color: var(--border-soft);
        color: var(--text-muted);
    }

    .btn-outline-dark:hover {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    .total {
        font-size: 22px;
        color: var(--accent);
    }

    .section-title {
        font-size: 14px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 14px;
    }

    .input-label {
        font-size: 12px;
        color: var(--text-muted);
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 18px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .order-summary {
            position: static;
        }
    }

    /* Typography levels */
    .text-primary {
        color: #f3ede7;
        /* main readable */
    }

    .text-secondary {
        color: #cfc6be;
        /* supporting */
    }

    .text-muted-soft {
        color: #9f948a;
        /* muted but visible */
    }

    /* Section headings */
    .section-title,
    .checkout-card h5 {
        color: #e6ddd5;
        letter-spacing: 1px;
    }

    /* Cart product name */
    .cart-item-name {
        color: #f3ede7;
        font-weight: 500;
    }

    /* Cart price line */
    .cart-item-price {
        color: #bfb4aa;
        font-size: 13px;
    }

    /* Quantity number */
    .cart-qty {
        color: #f3ede7;
        font-weight: 500;
    }

    /* Subtotal text */
    .order-summary p {
        color: #cfc6be;
    }

    /* Total payable */
    .total {
        color: #c46a3b;
        font-size: 24px;
        font-weight: 600;
    }

    .qty-btn {
        color: #f3ede7 !important;
        border-color: rgba(255, 255, 255, 0.25);
    }

    .qty-btn:hover {
        background: rgba(196, 106, 59, 0.15);
        border-color: var(--accent);
    }

    .checkout-card {
        box-shadow:
            inset 0 0 0 1px rgba(255, 255, 255, 0.08),
            0 10px 40px rgba(0, 0, 0, 0.6);
    }

    /* ===== RECOMMENDED PRODUCTS ===== */

    .recommend-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 18px;
    }

    .recommend-card {
        background: #1b1815;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        overflow: hidden;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .recommend-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .45);
    }

    .recommend-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .recommend-info {
        padding: 14px;
    }

    .recommend-name {
        font-size: 14px;
        font-weight: 500;
        color: #f3ede7;
        margin-bottom: 4px;
    }

    .recommend-price {
        font-size: 13px;
        color: #c46a3b;
        margin-bottom: 12px;
    }

    .recommend-actions {
        display: flex;
        gap: 10px;
    }

    .btn-view {
        flex: 1;
        text-align: center;
        padding: 7px 0;
        font-size: 12px;
        border: 1px solid rgba(255, 255, 255, .25);
        color: #cfc6be;
        border-radius: 20px;
        transition: .25s;
    }

    .btn-view:hover {
        background: rgba(255, 255, 255, .06);
        color: #fff;
    }

    .btn-add {
        flex: 1;
        background: var(--accent);
        border: none;
        color: #fff;
        font-size: 12px;
        padding: 7px 0;
        border-radius: 20px;
        cursor: pointer;
        transition: .25s;
    }

    .btn-add:hover {
        background: #a95a32;
    }
</style>

<body>

    <div class="container mt-5">
        <h3>Checkout</h3>

        <div class="row">

            <div class="col-md-6">
                <div class="card checkout-card p-4 mb-4">
                    <h5 class="section-title">Customer Details</h5>
                    <form method="POST">
                        <label class="input-label">Full Name</label>
                        <input type="text" name="name" class="form-control mb-3"
                            value="<?= htmlspecialchars($user['name']) ?>" required>

                        <label class="input-label">Email</label>
                        <input type="email" name="email" class="form-control mb-3"
                            value="<?= htmlspecialchars($user['email']) ?>" required>

                        <input type="text" name="phone" class="form-control mb-3"
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        <textarea name="address" class="form-control mb-3" placeholder="Full Address" rows="4" required
                            autofocus></textarea>
                        <small class="text-muted mb-2 d-block">
                            Please enter complete address including landmark
                        </small>

                        <button type="button" onclick="getLocation()" class="btn btn-outline-dark mb-2">
                            Use Current Location
                        </button>


                        <button type="submit" name="place_order" class="btn btn-dark w-100">
                            Place Order
                        </button>

                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card checkout-card p-4 mb-4">
                    <?php if (!empty($_SESSION['cart_error'])): ?>
                        <div class="alert alert-warning">
                            <?= htmlspecialchars($_SESSION['cart_error']) ?>
                        </div>
                        <?php unset($_SESSION['cart_error']); ?>
                    <?php endif; ?>

                    <h5>Your Cart</h5>
                    <hr>

                    <?php foreach ($_SESSION['cart'] as $id => $qty):
                        $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, price, image FROM products WHERE id=$id"));
                        if (!$p)
                            continue;
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="cart-item-name">
                                    <?= htmlspecialchars($p['name']) ?>
                                </div>

                                <div class="cart-item-price">
                                    ₹<?= number_format($p['price'], 2) ?> × <?= $qty ?>
                                    =
                                    ₹<?= number_format($p['price'] * $qty, 2) ?>
                                </div>


                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="update-cart.php?id=<?= $id ?>&action=dec"
                                    class="btn btn-sm btn-outline-dark qty-btn">−</a>
                                <span>
                                    <?= $qty ?>
                                </span>
                                <a href="update-cart.php?id=<?= $id ?>&action=inc"
                                    class="btn btn-sm btn-outline-dark qty-btn">+</a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <a href="cart.php" class="btn btn-outline-dark w-100 mt-2">
                        Edit Full Cart
                    </a>
                </div>

                <div class="card checkout-card p-4 shadow-sm">
                    <h5>Order Summary</h5>
                    <hr>

                    <p class="text-secondary">
                        Subtotal
                        <span style="float:right;">
                            ₹<?= number_format($grand_total, 2) ?>
                        </span>
                    </p>


                    <?php if ($discount > 0): ?>
                        <p class="text-success">
                            Discount − ₹<?= number_format($discount, 2) ?>
                        </p>
                    <?php endif; ?>

                    <hr>
                    <h5 class="total">
                        Total Payable: ₹<?= number_format($final_total, 2) ?>
                    </h5>
                </div>


            </div>
            <div class="card checkout-card p-4 mt-4">
                <h5 class="section-title">You may also like</h5>

                <div class="recommend-grid">
                    <?php
                    $suggest = mysqli_query($conn, "
      SELECT id, name, price, image
      FROM products
      WHERE status='active'
      ORDER BY RAND()
      LIMIT 3
    ");
                    ?>

                    <?php while ($s = mysqli_fetch_assoc($suggest)): ?>
                        <div class="recommend-card">

                            <img src="uploads/<?= htmlspecialchars($s['image']) ?>"
                                alt="<?= htmlspecialchars($s['name']) ?>">

                            <div class="recommend-info">
                                <div class="recommend-name">
                                    <?= htmlspecialchars($s['name']) ?>
                                </div>

                                <div class="recommend-price">
                                    ₹<?= number_format($s['price'], 2) ?>
                                </div>

                                <div class="recommend-actions">
                                    <a href="product.php?id=<?= $s['id'] ?>" class="btn-view">
                                        View
                                    </a>

                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <input type="hidden" name="product_id" value="<?= $s['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add">
                                            + Add
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    <?php endwhile; ?>
                </div>
            </div>


        </div>
    </div>

</body>
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

                if (confirm("Is this your address?\n\n" + address)) {
                    document.querySelector("textarea[name='address']").value = address;
                }
            })
            .catch(error => {
                alert("Unable to fetch address.");
            });
    }
</script>




</html>