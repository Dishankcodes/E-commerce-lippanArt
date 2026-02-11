<?php
session_start();
include("db.php");

/* =============================
   Initialize Cart
============================= */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* =============================
   ADD TO CART
============================= */
if (isset($_POST['add_to_cart'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    $product_query = mysqli_query($conn, "SELECT stock FROM products WHERE id='$product_id'");
    $product_data = mysqli_fetch_assoc($product_query);

    if (!$product_data) {
        header("Location: cart.php");
        exit();
    }

    // Restrict quantity to available stock
    if ($quantity > $product_data['stock']) {
        $quantity = $product_data['stock'];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;

        // Prevent exceeding stock
        if ($_SESSION['cart'][$product_id] > $product_data['stock']) {
            $_SESSION['cart'][$product_id] = $product_data['stock'];
        }

    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    header("Location: cart.php");
    exit();
}

/* =============================
   REMOVE ITEM
============================= */
if (isset($_GET['remove'])) {
    $product_id = (int) $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

/* =============================
   UPDATE QUANTITY
============================= */
if (isset($_POST['update_cart'])) {

    foreach ($_POST['quantities'] as $id => $qty) {

        $id = (int) $id;
        $qty = (int) $qty;

        $product_query = mysqli_query($conn, "SELECT stock FROM products WHERE id='$id'");
        $product_data = mysqli_fetch_assoc($product_query);

        if (!$product_data)
            continue;

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {

            // Restrict to stock
            if ($qty > $product_data['stock']) {
                $qty = $product_data['stock'];
            }

            $_SESSION['cart'][$id] = $qty;
        }
    }

    header("Location: cart.php");
    exit();
}

/* =============================
   APPLY COUPON
============================= */
if (isset($_POST['apply_coupon'])) {

    $coupon_code = $_POST['coupon_code'];

    $coupon_query = mysqli_query(
        $conn,
        "SELECT * FROM coupons 
         WHERE code='$coupon_code' 
         AND status='active' 
         AND expiry_date >= CURDATE()"
    );

    if (mysqli_num_rows($coupon_query) > 0) {
        $_SESSION['coupon'] = mysqli_fetch_assoc($coupon_query);
        $coupon_success = "Coupon Applied Successfully!";
    } else {
        unset($_SESSION['coupon']);
        $coupon_error = "Invalid or Expired Coupon!";
    }
}

/* Remove coupon if cart empty */
if (empty($_SESSION['cart'])) {
    unset($_SESSION['coupon']);
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

if (isset($_POST['quantities'])) {

    foreach ($_POST['quantities'] as $id => $qty) {

        $id = (int) $id;
        $qty = (int) $qty;

        $product_query = mysqli_query($conn, "SELECT stock FROM products WHERE id='$id'");
        $product_data = mysqli_fetch_assoc($product_query);

        if (!$product_data)
            continue;

        $available_stock = $product_data['stock'];

        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } elseif ($qty > $available_stock) {

            // Restrict to max stock
            $_SESSION['cart'][$id] = $available_stock;

            // Set stock message
            $_SESSION['stock_error'] = $product_data['stock'] . " units available only.";


        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }

    header("Location: cart.php");
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h3>Your Shopping Cart</h3>
        <?php
        if (isset($_SESSION['stock_error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['stock_error'] . "</div>";
            unset($_SESSION['stock_error']);
        }
        ?>


        <?php if (empty($_SESSION['cart'])) { ?>

            <div class="alert alert-warning">Your cart is empty.</div>

        <?php } else { ?>

            <form method="POST">

                <table class="table table-bordered mt-3 align-middle">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th width="120">Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>

                    <?php foreach ($_SESSION['cart'] as $id => $qty):

                        $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
                        $product = mysqli_fetch_assoc($product_query);

                        if (!$product)
                            continue;

                        $total = $product['price'] * $qty;
                        ?>

                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo $qty; ?>" min="1"
                                    max="<?php echo $product['stock']; ?>" class="form-control quantity-input">
                            </td>
                            <td>₹<?php echo number_format($total, 2); ?></td>
                            <td>
                                <a href="?remove=<?php echo $id; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Remove this product?')">
                                    Remove
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </table>

                <div class="text-end">
                    <button type="submit" name="update_cart" class="btn btn-secondary">
                        Update Cart
                    </button>
                </div>

            </form>

            <hr>

            <div class="row mt-4">
                <div class="col-md-6"></div>

                <div class="col-md-6">
                    <div class="card p-4 shadow">

                        <h5>Apply Coupon</h5>

                        <form method="POST" class="d-flex mb-3">
                            <input type="text" name="coupon_code" class="form-control me-2" placeholder="Enter coupon code">
                            <button type="submit" name="apply_coupon" class="btn btn-dark">Apply</button>
                        </form>

                        <?php
                        if (isset($coupon_success))
                            echo "<p class='text-success'>$coupon_success</p>";
                        if (isset($coupon_error))
                            echo "<p class='text-danger'>$coupon_error</p>";
                        ?>

                        <hr>

                        <p><strong>Subtotal:</strong> ₹<?php echo number_format($grand_total, 2); ?></p>
                        <p><strong>Discount:</strong> ₹<?php echo number_format($discount, 2); ?></p>
                        <h5><strong>Estimated Total:</strong> ₹<?php echo number_format($final_total, 2); ?></h5>

                        <a href="checkout.php" class="btn btn-dark w-100 mt-3">
                            Proceed to Checkout
                        </a>

                    </div>
                </div>
            </div>

        <?php } ?>

    </div>

    <script>
        document.querySelectorAll(".quantity-input").forEach(input => {
            let timeout;
            input.addEventListener("input", function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.form.submit();
                }, 400);
            });
        });

    </script>

</body>

</html>