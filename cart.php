<?php
session_start();
include("db.php");


/* =============================
   CART INIT
============================= */
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

/* =============================
   REMOVE ITEM
============================= */
if (isset($_GET['remove'])) {
  $removeId = (int) $_GET['remove'];
  unset($_SESSION['cart'][$removeId]);
  header("Location: cart.php");
  exit();
}
/* =============================
   AJAX QUANTITY UPDATE (FINAL FIX)
============================= */
if (isset($_POST['ajax_update'])) {

  $id = (int) $_POST['product_id'];
  $qty = (int) $_POST['quantity'];

  $res = mysqli_query($conn, "SELECT stock, price FROM products WHERE id='$id'");
  $p = mysqli_fetch_assoc($res);

  // Product missing or out of stock → remove
  if (!$p || $p['stock'] <= 0) {
    unset($_SESSION['cart'][$id]);
  } else {

    // Clamp quantity
    if ($qty > $p['stock']) {
      $qty = $p['stock'];
    }

    if ($qty <= 0) {
      unset($_SESSION['cart'][$id]);
    } else {
      $_SESSION['cart'][$id] = $qty;
    }
  }

  // 🔁 Recalculate cart totals (ALWAYS)
  $cart_total = 0;
  $cart_qty = 0;

  foreach ($_SESSION['cart'] as $cid => $cqty) {
    $qr = mysqli_query($conn, "SELECT price FROM products WHERE id='$cid'");
    if ($cp = mysqli_fetch_assoc($qr)) {
      $cart_total += $cp['price'] * $cqty;
      $cart_qty += $cqty;
    }
  }

  $discount_percent = 0;
  $discount_amount = 0;
  $final_total = $cart_total;

  if ($cart_total >= 5000) {
    $discount_percent = 5;
    $discount_amount = round(($cart_total * 5) / 100, 2);
    $final_total = $cart_total - $discount_amount;
  }
  echo json_encode([
    'status' => 'ok',
    'item_subtotal' => number_format($p['price'] * ($_SESSION['cart'][$id] ?? 0), 2),
    'cart_subtotal' => number_format($cart_total, 2),
    'final_total' => number_format($final_total, 2),
    'discount' => number_format($discount_amount, 2),
    'discount_pct' => $discount_percent,
    'cart_qty' => $cart_qty
  ]);
  exit;

}


/* =============================
   AUTO REMOVE OUT-OF-STOCK ITEMS
============================= */
foreach ($_SESSION['cart'] as $id => $qty) {
  $stockCheck = mysqli_query(
    $conn,
    "SELECT stock FROM products WHERE id='$id'"
  );
  $row = mysqli_fetch_assoc($stockCheck);

  if (!$row || $row['stock'] <= 0) {
    unset($_SESSION['cart'][$id]);
  }
}

/* =============================
   CALCULATE TOTALS
============================= */
$grand_total = 0;
$cart_count = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
  $q = mysqli_query(
    $conn,
    "SELECT price FROM products WHERE id='$id'"
  );
  if ($p = mysqli_fetch_assoc($q)) {
    $grand_total += $p['price'] * $qty;
    $cart_count += $qty; // total quantity (real e-commerce)
  }
}
/* =============================
   ADD TO CART (MISSING FIX)
============================= */
/* =============================
   ADD TO CART (FINAL STOCK-SAFE)
============================= */
if (isset($_POST['add_to_cart'])) {

  $product_id = (int) $_POST['product_id'];
  $add_qty = (int) ($_POST['quantity'] ?? 1);

  // Fetch product stock
  $res = mysqli_query(
    $conn,
    "SELECT stock FROM products WHERE id='$product_id' AND status='active'"
  );

  if ($p = mysqli_fetch_assoc($res)) {

    $stock = (int) $p['stock'];
    $in_cart = $_SESSION['cart'][$product_id] ?? 0;
    $remaining = $stock - $in_cart;

    // ❌ No stock left
    if ($remaining <= 0) {
      $_SESSION['cart_error'] = "Only $stock item(s) available.";
      header("Location: product.php?id=$product_id");
      exit;
    }

    // Clamp add qty to remaining stock
    if ($add_qty > $remaining) {
      $add_qty = $remaining;
      $_SESSION['cart_error'] = "Only $remaining item(s) available.";
    }

    $_SESSION['cart'][$product_id] = $in_cart + $add_qty;
  }

  header("Location: cart.php");
  exit();
}




/* =============================
   AUTO OFFER / DISCOUNT RULE
============================= */
$discount_percent = 0;
$discount_amount = 0;
$final_total = $grand_total;

if ($grand_total >= 5000) {
  $discount_percent = 5; // 5% offer
  $discount_amount = round(($grand_total * $discount_percent) / 100, 2);
  $final_total = $grand_total - $discount_amount;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Your Cart | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, .12);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
    }

    /* ================= HEADER ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 72px;
      z-index: 1000;
      background: rgba(15, 13, 11, .85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border-soft);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 80px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
    }

    nav {
      display: flex;
      align-items: center;
    }

    nav a {
      margin-left: 34px;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-muted);
      position: relative;
      padding-bottom: 6px;
    }

    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 1px;
      background: var(--accent);
      transition: .4s;
    }

    nav a:hover::after {
      width: 100%
    }

    nav a:hover {
      color: var(--text-main)
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
    }

    /* MOBILE NAV */
    .menu-toggle {
      width: 32px;
      height: 22px;
      display: none;
      flex-direction: column;
      justify-content: space-between;
      cursor: pointer;
    }

    .menu-toggle span {
      height: 2px;
      background: var(--text-main);
    }

    @media(max-width:768px) {
      .menu-toggle {
        display: flex
      }

      nav {
        position: fixed;
        top: 72px;
        left: 0;
        width: 100%;
        height: calc(100vh - 72px);
        background: rgba(15, 13, 11, .97);
        flex-direction: column;
        justify-content: center;
        transform: translateY(-120%);
        transition: .6s;
        text-decoration: none;
      }

      nav.active {
        transform: translateY(0)
      }

      nav a {
        margin: 14px 0;
        font-size: 26px;
        font-family: 'Playfair Display', serif;
      }
    }

    /* ================= PAGE ================= */
    .page-title {
      margin-top: 120px;
      text-align: center;
      padding: 40px 20px;
    }

    .cart-container {
      max-width: 1300px;
      margin: auto;
      padding: 0 60px 80px;
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 60px;
    }

    .cart-item {
      display: grid;
      grid-template-columns: 100px 1fr 140px;
      gap: 24px;
      padding: 30px 0;
      border-bottom: 1px solid var(--border-soft);
      align-items: center;
    }

    .cart-item img {
      width: 100%;
      height: 100px;
      object-fit: cover;
    }

    .item-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 10px;
    }

    .qty {
      display: flex;
      border: 1px solid var(--border-soft);
    }

    .qty input {
      width: 40px;
      background: none;
      border: none;
      color: #fff;
      text-align: center;
    }

    .qty button {
      width: 30px;
      background: none;
      border: none;
      color: #fff;
    }

    .summary {
      background: var(--bg-soft);
      padding: 30px;
      border: 1px solid var(--border-soft);
      position: sticky;
      top: 120px;
    }

    .checkout {
      width: 100%;
      padding: 14px;
      background: var(--accent);
      border: none;
      color: #fff;
      margin-top: 20px;
    }

    @media(max-width:900px) {
      .cart-container {
        grid-template-columns: 1fr;
        padding: 0 20px
      }
    }
  </style>
</head>

<body>

  <header>
    <div class="logo">Auraloom</div>

    <div class="menu-toggle" id="menuToggle">
      <span></span><span></span>
    </div>

    <nav id="navMenu">
      <a href="index.php">Home</a>
      <a href="collection.php">Shop</a>
      <a href="custom-order.php">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About</a>
      <a href="contact_us.php">Contact</a>
    </nav>

    <a href="cart.php" class="header-btn">
      <?php
      $cartQty = array_sum($_SESSION['cart']);
      ?>
      Cart (<span id="cart-count"><?= $cartQty ?></span>)
    </a>
  </header>
  <?php if (!empty($_SESSION['cart_error'])): ?>
    <div class="alert alert-warning">
      <?= htmlspecialchars($_SESSION['cart_error']) ?>
    </div>
    <?php unset($_SESSION['cart_error']); ?>
  <?php endif; ?>

  <div class="page-title">
    <h1>Your Cart</h1>
  </div>

  <div class="cart-container">

    <?php if (empty($_SESSION['cart'])): ?>

      <div style="text-align:center;color:var(--text-muted)">
        <p>Your cart is empty.</p>
        <div style="margin-bottom:30px;">
          <a href="collection.php" style="
    display:inline-block;
    font-size:13px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:var(--text-muted);
    border:1px solid var(--border-soft);
    padding:10px 18px;
    transition:.3s;
  " onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--text-main)'"
            onmouseout="this.style.borderColor='var(--border-soft)';this.style.color='var(--text-muted)'">
            ← Continue Shopping
          </a>
        </div>
      </div>

    <?php else: ?>

      <form method="post">

        <?php foreach ($_SESSION['cart'] as $id => $qty):

          $p = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT * FROM products WHERE id='$id'")
          );

          ?>

          <div class="cart-item">

            <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">

            <div>
              <h3><?= htmlspecialchars($p['name']) ?></h3>

              <?php if ($p['stock'] <= 3): ?>
                <div style="font-size:12px;color:#ffb347">
                  Only <?= $p['stock'] ?> left in stock
                </div>
              <?php endif; ?>

              <div style="color:var(--accent)">
                ₹<?= number_format($p['price'], 2) ?>
              </div>
            </div>

            <div class="item-actions">
              <div class="qty">
                <button type="button" onclick="changeQty(<?= $id ?>, -1, <?= $p['stock'] ?>)">−</button>

                <input type="number" id="qty-<?= $id ?>" value="<?= $qty ?>" min="1" max="<?= $p['stock'] ?>" readonly>

                <button type="button" onclick="changeQty(<?= $id ?>, 1, <?= $p['stock'] ?>)">+</button>
              </div>

              <div class="qty-error" id="error-<?= $id ?>" style="display:none;font-size:11px;color:#ffb347;margin-top:6px">
              </div>

              <div id="subtotal-<?= $id ?>">
                ₹<?= number_format($p['price'] * $qty, 2) ?>
              </div>
              <a href="?remove=<?= $id ?>" style="font-size:12px;color:var(--text-muted)">
                Remove
              </a>
            </div>

          </div>

        <?php endforeach; ?>

        <button class="checkout" name="update_cart">
          Update Cart
        </button>

      </form>

    <?php endif; ?>

    <div class="summary">
      <h2>Order Summary</h2>

      <p style="margin:10px 0;color:var(--text-muted)">Subtotal</p>
      <h3 id="cart-subtotal">₹<?= number_format($grand_total, 2) ?></h3>

      <div id="discount-box" style="<?= $discount_amount > 0 ? '' : 'display:none;' ?>">
        <p style="margin-top:14px;color:#9fd3a9;font-size:13px">
          🎉 Auto Offer Applied (<span id="discount-percent"><?= $discount_percent ?></span>% OFF)
        </p>
        <p style="color:var(--text-muted);font-size:13px">
          Discount − ₹<span id="discount-amount"><?= number_format($discount_amount, 2) ?></span>
        </p>
      </div>

      <hr style="margin:18px 0;border:0;border-top:1px solid var(--border-soft)">

      <p style="color:var(--text-muted)">Total Payable</p>
      <h2 id="cart-total" style="color:var(--accent)">
        ₹<?= number_format($final_total, 2) ?>
      </h2>

      <?php if ($final_total > 0 && !empty($_SESSION['cart'])): ?>
        <a href="checkout.php" class="checkout" style="display:block;text-align:center;text-decoration:none">
          Proceed to Checkout
        </a>
      <?php else: ?>
        <button class="checkout" disabled style="opacity:.5;cursor:not-allowed">
          Proceed to Checkout
        </button>
      <?php endif; ?>

    </div>


    <p style="margin-top:14px;text-align:center">
      <a href="collection.php" style="font-size:12px;color:var(--text-muted)">
        Add more products
      </a>
    </p>

  </div>

  <script>
    const toggle = document.getElementById("menuToggle");
    const nav = document.getElementById("navMenu");
    toggle.onclick = () => nav.classList.toggle("active");
  </script>

</body>
<script>
  function changeQty(productId, delta, maxStock) {

    const qtyInput = document.getElementById('qty-' + productId);
    const errorBox = document.getElementById('error-' + productId);
    let currentQty = parseInt(qtyInput.value);
    let newQty = currentQty + delta;

    errorBox.style.display = 'none';

    if (newQty < 1) return;

    if (newQty > maxStock) {
      errorBox.innerText = 'Only ' + maxStock + ' available in stock';
      errorBox.style.display = 'block';
      return;
    }

    fetch('cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `ajax_update=1&product_id=${productId}&quantity=${newQty}`
    })
      .then(res => res.json())
      .then(data => {

        if (data.status === 'ok') {

          // Qty
          qtyInput.value = newQty;

          // Item subtotal
          document.getElementById('subtotal-' + productId).innerText =
            '₹' + data.item_subtotal;

          // Cart subtotal
          document.getElementById('cart-subtotal').innerText =
            '₹' + data.cart_subtotal;

          // Final total
          document.getElementById('cart-total').innerText =
            '₹' + data.final_total;

          // Cart count
          document.getElementById('cart-count').innerText =
            data.cart_qty;

          // Discount UI
          if (parseFloat(data.discount) > 0) {
            document.getElementById('discount-box').style.display = 'block';
            document.getElementById('discount-amount').innerText = data.discount;
            document.getElementById('discount-percent').innerText = data.discount_pct;
          } else {
            document.getElementById('discount-box').style.display = 'none';
          }
        }

        if (data.status === 'limit') {
          errorBox.innerText = 'Only ' + data.max + ' available in stock';
          errorBox.style.display = 'block';
        }

        if (data.status === 'out') {
          location.reload();
        }
      });

  }
</script>

</html>