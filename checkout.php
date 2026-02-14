<?php
session_start();
include("db.php");

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
        (customer_name, customer_email, customer_phone, address, total_amount, discount_amount, final_amount)
        VALUES
        ('$name','$email','$phone','$address','$grand_total','$discount','$final_total')");

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
</style>

<body>

    <div class="container mt-5">
        <h3>Checkout</h3>

        <div class="row">

            <div class="col-md-6">

                <form method="POST">

                    <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>

                    <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>

                    <input type="text" name="phone" class="form-control mb-3" placeholder="Phone Number" required>

                    <textarea name="address" class="form-control mb-3" placeholder="Full Address" rows="4"
                        required></textarea>
                    <button type="button" onclick="getLocation()" class="btn btn-outline-dark mb-2">
                        Use Current Location
                    </button>


                    <button type="submit" name="place_order" class="btn btn-dark w-100">
                        Place Order
                    </button>

                </form>

            </div>

            <div class="col-md-6">

                <div class="card checkout-card p-4 shadow-sm">
                    <h5>Order Summary</h5>
                    <hr>

                    <p>Subtotal: ₹<?= number_format($grand_total, 2) ?></p>

                    <?php if ($discount > 0): ?>
                        <p class="text-success">
                            <?= $discount_label ?> − ₹<?= number_format($discount, 2) ?>
                        </p>
                    <?php endif; ?>

                    <hr>
                    <h5 class="total">
                        Total Payable: ₹<?= number_format($final_total, 2) ?>
                    </h5>
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