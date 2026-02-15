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
  // 🔐 SAVE PRICING IN SESSION (THIS IS THE FIX)
  $_SESSION['pricing'] = [
    'subtotal' => $cart_total,
    'discount_percent' => $discount_percent,
    'discount_amount' => $discount_amount,
    'final_total' => $final_total
  ];

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

$_SESSION['pricing'] = [
  'subtotal' => $grand_total,
  'discount_percent' => $discount_percent,
  'discount_amount' => $discount_amount,
  'final_total' => $final_total
];

/* =============================
   YOU MAY ALSO LIKE
============================= */

// Get product IDs already in cart
$cart_ids = array_keys($_SESSION['cart']);
$exclude = !empty($cart_ids) ? "AND id NOT IN (" . implode(',', $cart_ids) . ")" : "";

// Fetch random active products
$recommended = mysqli_query($conn, "
  SELECT id, name, price, image
  FROM products
  WHERE status='active'
  $exclude
  ORDER BY RAND()
  LIMIT 4
");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Your Cart | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ================= VARIABLES ================= */
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

    /* ================= GLOBAL RESET ================= */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
      transition: 0.3s;
    }

    button,
    input {
      font-family: inherit;
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
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 60px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      letter-spacing: 2px;
      color: var(--text-main);
    }

    nav {
      display: flex;
      gap: 40px;
    }

    nav a {
      font-size: 12px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--text-muted);
      position: relative;
      padding-bottom: 5px;
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
      transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    nav a:hover::after {
      width: 100%;
    }

    .header-btn {
      font-size: 12px;
      letter-spacing: 1px;
      text-transform: uppercase;
      background: var(--accent);
      color: #fff;
      padding: 10px 24px;
      border: none;
      transition: 0.3s;
    }

    .header-btn:hover {
      background: var(--accent-hover);
    }

    /* ================= PAGE LAYOUT ================= */
    .page-title {
      margin-top: 130px;
      text-align: center;
      padding-bottom: 40px;
      border-bottom: 1px solid var(--border-soft);
      margin-bottom: 60px;
    }

    .page-title h1 {
      font-family: 'Playfair Display', serif;
      font-size: 36px;
      font-weight: 500;
      letter-spacing: 1px;
    }

    .cart-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 60px 100px;
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 60px;
      align-items: start;
    }

    /* ================= CART ITEMS ================= */
    .cart-item {
      display: grid;
      grid-template-columns: 100px 1fr auto;
      gap: 25px;
      padding: 25px 0;
      border-bottom: 1px solid var(--border-soft);
      align-items: center;
    }

    .cart-item:first-child {
      border-top: 1px solid var(--border-soft);
    }

    .cart-item img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border: 1px solid var(--border-soft);
    }

    .cart-item h3 {
      font-family: 'Playfair Display', serif;
      font-size: 18px;
      margin-bottom: 6px;
      font-weight: 500;
    }

    /* ================= QUANTITY CONTROLS ================= */
    .item-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 12px;
    }

    .qty {
      display: flex;
      align-items: center;
      border: 1px solid var(--border-soft);
      border-radius: 4px;
      overflow: hidden;
    }

    .qty button {
      width: 35px;
      height: 35px;
      background: transparent;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 16px;
      transition: 0.2s;
    }

    .qty button:hover {
      background: rgba(255, 255, 255, 0.05);
      color: var(--text-main);
    }

    .qty input {
      width: 40px;
      height: 35px;
      background: transparent;
      border: none;
      color: var(--text-main);
      text-align: center;
      font-size: 14px;
    }

    /* ================= ORDER SUMMARY ================= */
    .summary {
      background: var(--card-bg);
      padding: 35px;
      border: 1px solid var(--border-soft);
      position: sticky;
      top: 120px;
    }

    .summary h2 {
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      margin-bottom: 25px;
      font-weight: 500;
    }

    .summary h3 {
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      font-size: 18px;
    }

    /* Checkout & Update Buttons */
    .checkout {
      width: 100%;
      padding: 14px;
      background: var(--accent);
      border: none;
      color: #fff;
      margin-top: 25px;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
      text-align: center;
      display: block;
    }

    .checkout:hover {
      background: var(--accent-hover);
    }

    /* ================= MOBILE RESPONSIVE ================= */
    .menu-toggle {
      width: 30px;
      height: 20px;
      display: none;
      flex-direction: column;
      justify-content: space-between;
      cursor: pointer;
    }

    .menu-toggle span {
      width: 100%;
      height: 2px;
      background: var(--text-main);
    }

    @media(max-width:900px) {
      header {
        padding: 0 20px;
        height: 70px;
      }

      nav {
        display: none;
      }

      .menu-toggle {
        display: flex;
      }

      .cart-container {
        grid-template-columns: 1fr;
        padding: 0 20px 60px;
        gap: 40px;
      }

      .cart-item {
        grid-template-columns: 80px 1fr;
        position: relative;
        align-items: start;
      }
    }


    @media(max-width:900px) {
      section {
        padding: 0 20px !important;
      }
    }


    .recommend-section {
      max-width: 1400px;
      margin: 80px auto 120px;
      padding: 0 60px;
    }

    .recommend-title {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      margin-bottom: 30px;
    }

    /* 🔥 KEY FIX: ALWAYS 3 SLOTS */
    .recommend-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    /* CARD */
    .recommend-card {
      background: #171411;
      border: 1px solid rgba(255, 255, 255, .12);
      padding: 18px;
      transition: 0.3s;
    }

    .recommend-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      margin-bottom: 12px;
    }

    .recommend-card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 16px;
      margin-bottom: 6px;
    }

    .recommend-card .price {
      color: #c46a3b;
      font-size: 14px;
      margin-bottom: 14px;
    }

    .recommend-card button {
      width: 100%;
      padding: 10px;
      background: #c46a3b;
      border: none;
      color: #fff;
      font-size: 12px;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
      .recommend-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .recommend-grid {
        grid-template-columns: 1fr;
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
    <div
      style="margin-top:100px; text-align:center; color:#ff6b6b; font-size:14px; background:rgba(255,107,107,0.1); padding:15px; border-bottom:1px solid rgba(255,107,107,0.2);">
      <?= htmlspecialchars($_SESSION['cart_error']) ?>
    </div>
    <?php unset($_SESSION['cart_error']); ?>
  <?php endif; ?>

  <div class="page-title">
    <h1>Your Cart</h1>
  </div>

  <div class="cart-container">

    <?php if (empty($_SESSION['cart'])): ?>

      <div style="text-align:center; color:var(--text-muted); grid-column: 1 / -1; padding: 60px 0;">
        <p style="font-size: 18px; margin-bottom: 20px;">Your cart is currently empty.</p>
        <div style="margin-bottom:30px;">
          <a href="collection.php" class="checkout"
            style="display:inline-block; width:auto; padding: 12px 30px; text-decoration:none;">
            Start Shopping
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
                <div style="font-size:11px; color:#ffb347; margin-bottom:5px;">
                  Only <?= $p['stock'] ?> left in stock
                </div>
              <?php endif; ?>

              <div style="color:var(--accent); font-size:15px; font-weight:500;">
                ₹<?= number_format($p['price'], 2) ?>
              </div>
            </div>

            <div class="item-actions">
              <div class="qty">
                <button type="button" onclick="changeQty(<?= $id ?>, -1, <?= $p['stock'] ?>)">−</button>

                <input type="number" id="qty-<?= $id ?>" value="<?= $qty ?>" min="1" max="<?= $p['stock'] ?>" readonly>

                <button type="button" onclick="changeQty(<?= $id ?>, 1, <?= $p['stock'] ?>)">+</button>
              </div>

              <div class="qty-error" id="error-<?= $id ?>" style="display:none; font-size:10px; color:#ffb347;">
              </div>

              <div id="subtotal-<?= $id ?>" style="font-size:14px; color:var(--text-main); font-weight:500;">
                ₹<?= number_format($p['price'] * $qty, 2) ?>
              </div>
              <a href="?remove=<?= $id ?>"
                style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted);">
                Remove
              </a>
            </div>

          </div>

        <?php endforeach; ?>

        <button type="submit" name="update_cart" class="checkout"
          style="background:transparent; border:1px solid var(--border-soft); color:var(--text-muted); width:auto; padding:10px 20px; font-size:11px; margin-top:30px;">
          Update Cart
        </button>

      </form>

    <?php endif; ?>

    <div class="summary">
      <h2>Order Summary</h2>

      <div
        style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:14px; color:var(--text-muted);">
        <span>Subtotal</span>
        <h3 id="cart-subtotal" style="font-size:14px; color:var(--text-main);">₹<?= number_format($grand_total, 2) ?>
        </h3>
      </div>

      <div id="discount-box" style="<?= $discount_amount > 0 ? '' : 'display:none;' ?>">
        <div style="margin-top:10px; color:#7dd87d; font-size:13px;">
          🎉 Offer Applied (<span id="discount-percent"><?= $discount_percent ?></span>% OFF)
        </div>
        <div
          style="display:flex; justify-content:space-between; color:var(--text-muted); font-size:13px; margin-top:5px;">
          <span>Discount</span>
          <span>− ₹<span id="discount-amount"><?= number_format($discount_amount, 2) ?></span></span>
        </div>
      </div>

      <hr style="margin:20px 0; border:0; border-top:1px solid var(--border-soft)">

      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:var(--text-muted); font-size:14px;">Total Payable</span>
        <h2 id="cart-total" style="color:var(--accent); margin:0; font-size:20px;">
          ₹<?= number_format($final_total, 2) ?>
        </h2>
      </div>

      <?php if ($final_total > 0 && !empty($_SESSION['cart'])): ?>
        <a href="checkout.php" class="checkout" style="text-decoration:none;">
          Proceed to Checkout
        </a>
      <?php else: ?>
        <button class="checkout" disabled style="opacity:0.5; cursor:not-allowed;">
          Proceed to Checkout
        </button>
      <?php endif; ?>

      <div style="margin-top:20px; text-align:center;">
        <a href="collection.php"
          style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">
          Add more products
        </a>
      </div>

    </div>

  </div>
  <?php if (mysqli_num_rows($recommended) > 0): ?>
    <section class="recommend-section">

      <h2 class="recommend-title">You may also like</h2>

      <div class="recommend-grid">

        <?php while ($r = mysqli_fetch_assoc($recommended)): ?>
          <div class="recommend-card">

            <a href="product.php?id=<?= $r['id'] ?>">
              <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
            </a>

            <h3><?= htmlspecialchars($r['name']) ?></h3>

            <div class="price">
              ₹<?= number_format($r['price'], 2) ?>
            </div>

            <form method="post" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $r['id'] ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" name="add_to_cart">
                <i class="bi bi-cart-plus"></i> Add to Cart
              </button>
            </form>

          </div>
        <?php endwhile; ?>

      </div>
    </section>
  <?php endif; ?>



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
<script>
  const toggle = document.getElementById("menuToggle");
  const nav = document.getElementById("navMenu");
  toggle.onclick = () => nav.classList.toggle("active");
</script>

</html>