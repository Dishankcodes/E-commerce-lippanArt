<?php
session_start();
include("db.php");

if (!isset($_GET['id'], $_GET['action'])) {
    header("Location: cart.php");
    exit;
}

$product_id = (int) $_GET['id'];
$action = $_GET['action'];

/* Ensure cart item exists */
if (!isset($_SESSION['cart'][$product_id])) {
    header("Location: cart.php");
    exit;
}

/* Fetch product stock */
$product = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT stock, name FROM products WHERE id=$product_id LIMIT 1"
    )
);

if (!$product) {
    header("Location: cart.php");
    exit;
}

$current_qty = $_SESSION['cart'][$product_id];
$stock = (int) $product['stock'];

/* ---------- HANDLE ACTION ---------- */

if ($action === 'inc') {

    if ($current_qty < $stock) {
        $_SESSION['cart'][$product_id]++;
    } else {
        // ⚠️ Do NOT block — just inform
        $_SESSION['cart_error'] =
            "Only $stock unit(s) available for {$product['name']}";
    }

} elseif ($action === 'dec') {

    if ($current_qty > 1) {
        $_SESSION['cart'][$product_id]--;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
}

/* Go back where user came from */
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'cart.php'));
exit;
