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

    // Insert Order
    mysqli_query($conn, "INSERT INTO orders 
        (customer_name, customer_email, customer_phone, address, total_amount, discount_amount, final_amount)
        VALUES
        ('$name','$email','$phone','$address','$grand_total','$discount','$final_total')");

    $order_id = mysqli_insert_id($conn);

    // Insert Order Items + Reduce Stock
    foreach ($_SESSION['cart'] as $id => $qty) {

        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
        $product = mysqli_fetch_assoc($product_query);

        if (!$product)
            continue;

        $price = $product['price'];

        // Save order item
        mysqli_query($conn, "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES
            ('$order_id','$id','$qty','$price')");

        // Reduce stock
        $new_stock = $product['stock'] - $qty;

        mysqli_query($conn, "UPDATE products 
            SET stock='$new_stock'
            WHERE id='$id'");
    }

    // Clear cart + coupon
    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);

    $seller_phone = "919510809453"; // your seller number
    $customer_phone = $phone;

    $message = "New Order #%23$order_id\n";
    $message .= "Customer: $name\n";
    $message .= "Phone: $phone\n";
    $message .= "Total: ₹$final_total\n\n";
    $message .= "Items:\n";

    foreach ($_SESSION['cart'] as $id => $qty) {
        $product_query = mysqli_query($conn, "SELECT name FROM products WHERE id='$id'");
        $product = mysqli_fetch_assoc($product_query);
        $message .= $product['name'] . " x " . $qty . "\n";
    }

    $encoded_message = urlencode($message);

    // Send to seller
    header("Location: https://wa.me/$seller_phone?text=$encoded_message");
    exit();

}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   </head>

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

                <div class="card p-4 shadow">

                    <h5>Order Summary</h5>
                    <hr>

                    <p>Subtotal: ₹<?php echo number_format($grand_total, 2); ?></p>
                    <p>Discount: ₹<?php echo number_format($discount, 2); ?></p>

                    <hr>
                    <h5>Total Payable: ₹<?php echo number_format($final_total, 2); ?></h5>

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