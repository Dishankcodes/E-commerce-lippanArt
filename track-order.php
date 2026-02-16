<?php
include("db.php");

$order_id = (int) ($_GET['id'] ?? 0);

if ($order_id <= 0) {
  header("Location: index.php");
  exit();
}

/* FETCH ORDER */
$order_q = mysqli_query(
  $conn,
  "SELECT * FROM orders WHERE id='$order_id' LIMIT 1"
);
$order = mysqli_fetch_assoc($order_q);

if (!$order) {
  echo "<h2 style='color:#fff;text-align:center;margin-top:80px'>Order not found</h2>";
  exit();
}

$items = mysqli_query(
  $conn,
  "SELECT 
      oi.quantity,
      p.name,
      p.image
   FROM order_items oi
   JOIN products p ON oi.product_id = p.id
   WHERE oi.order_id='$order_id'"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Track Order | AURALOOM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
      --border-soft: rgba(255, 255, 255, 0.12);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: var(--bg-dark);
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
    }

    /* --- HEADER (Brand Standard) --- */
    header {
      position: fixed;
      top: 0; width: 100%; height: 80px;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.85);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid var(--border-soft);
      display: grid;
      grid-template-columns: auto 1fr auto;
      align-items: center;
      padding: 0 80px;
    }

    .logo { font-family: 'Playfair Display', serif; font-size: 28px; letter-spacing: 2px; color: var(--text-main); text-decoration: none; }

    nav { display: flex; justify-content: center; gap: 35px; }
    nav a {
      font-size: 12px; letter-spacing: 2px; text-transform: uppercase;
      color: var(--text-muted); position: relative; padding-bottom: 5px; text-decoration: none;
    }
    nav a:hover { color: var(--text-main); }
    nav a::after {
      content: ""; position: absolute; left: 0; bottom: 0;
      width: 0%; height: 1px; background: var(--accent); transition: 0.35s ease;
    }
    nav a:hover::after { width: 100%; }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 11px;
      letter-spacing: 1px;
      text-transform: uppercase;
      text-decoration: none;
    }

    /* --- PAGE CONTENT --- */
    .page-wrap { padding-top: 140px; padding-bottom: 80px; }

    .box {
      max-width: 800px;
      margin: 0 auto;
      padding: 60px;
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
    }

    h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 10px; font-weight: 500; }
    h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 25px; color: var(--accent); }

    .muted { color: var(--text-muted); font-size: 14px; }
    
    .status-pill {
      display: inline-block;
      padding: 8px 20px;
      border: 1px solid var(--accent);
      color: var(--accent);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 600;
      margin-top: 15px;
    }

    /* --- PROGRESS TRACKER --- */
    .timeline {
      display: flex;
      justify-content: space-between;
      margin: 60px 0;
      position: relative;
    }

    .timeline::before {
      content: "";
      position: absolute;
      top: 16px; left: 0; width: 100%; height: 1px;
      background: var(--border-soft);
      z-index: 1;
    }

    .t-step { position: relative; z-index: 2; text-align: center; width: 25%; }

    .t-dot {
      width: 32px; height: 32px;
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
      border-radius: 50%;
      margin: 0 auto 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; color: var(--text-muted); transition: 0.4s ease;
    }

    .t-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; }

    .t-step.active .t-dot { border-color: var(--accent); background: var(--accent); color: #fff; }
    .t-step.active .t-label { color: var(--text-main); font-weight: 500; }

    /* --- ARTWORK CARDS --- */
    .item-card {
      display: flex;
      align-items: center;
      gap: 25px;
      margin: 20px 0;
      padding: 20px;
      background: var(--card-bg);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .item-card img {
      width: 80px; height: 80px;
      object-fit: cover;
      border: 1px solid var(--border-soft);
    }

    .item-card strong { font-family: 'Playfair Display', serif; font-size: 18px; letter-spacing: 0.5px; color: var(--text-main); }

    /* --- ACTION BUTTONS (Brand Square) --- */
    .btn {
      display: inline-block;
      padding: 15px 30px;
      background: var(--accent);
      color: #fff;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .btn:hover { background: var(--accent-hover); transform: translateY(-2px); }

    .btn.secondary {
      background: transparent;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      margin-right: 12px;
    }

    .btn.secondary:hover { border-color: var(--accent); color: var(--accent); }

    .btn-wa { background: #075e54; display: inline-flex; align-items: center; gap: 10px; margin-top: 20px; }

    hr { border: none; border-top: 1px solid var(--border-soft); margin: 40px 0; }

    .alert-success {
      margin-bottom: 30px; padding: 18px;
      border: 1px solid #7dd87d; background: rgba(125,216,125,.05);
      color: #7dd87d; font-size: 14px; text-align: center;
    }

    @media (max-width: 768px) {
      header { padding: 0 30px; }
      nav { display: none; }
      .box { padding: 40px 25px; }
      .timeline { flex-direction: column; gap: 40px; margin: 40px 0; }
      .timeline::before { display: none; }
      .t-step { width: 100%; text-align: left; display: flex; align-items: center; gap: 20px; }
      .t-dot { margin: 0; }
    }
  </style>
</head>

<body>

  <header>
    <a href="index.php" class="logo">AURALOOM</a>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Shop</a>
      <a href="custom-order.php">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About</a>
    </nav>
    <a href="order-history.php" class="header-btn">My Orders</a>
  </header>

  <div class="page-wrap">
    <div class="box">
      <?php if (isset($_GET['testimonial']) && $_GET['testimonial'] === 'success'): ?>
        <div class="alert-success">
          <i class="fas fa-check-circle"></i> Thank you! Your testimonial was submitted and is awaiting approval.
        </div>
      <?php endif; ?>

      <h1>Order Tracker</h1>
      <p class="muted">Masterpiece ID: <strong>#AUR-<?= $order_id ?></strong></p>

      <div class="status-pill">
        <?= ucfirst($order['order_status']) ?>
      </div>


      <?php if ($order['order_status'] === 'Cancelled'): ?>
        <p style="color:#ff6b6b; margin-top:25px; font-weight: 500;">
          <i class="fas fa-exclamation-circle"></i> This order has been cancelled.
        </p>
      <?php endif; ?>
      <p class="muted">  You will receive an email when your order status changes </p>

      <div class="timeline">
        <div class="t-step <?= in_array($order['order_status'], ['Placed', 'Processing', 'Shipped', 'Delivered']) ? 'active' : '' ?>">
          <div class="t-dot"><i class="fas fa-check"></i></div>
          <div class="t-label">Order Placed</div>
        </div>
        <div class="t-step <?= in_array($order['order_status'], ['Processing', 'Shipped', 'Delivered']) ? 'active' : '' ?>">
          <div class="t-dot"><i class="fas fa-hammer"></i></div>
          <div class="t-label">Crafting</div>
        </div>
        <div class="t-step <?= in_array($order['order_status'], ['Shipped', 'Delivered']) ? 'active' : '' ?>">
          <div class="t-dot"><i class="fas fa-truck-fast"></i></div>
          <div class="t-label">In Transit</div>
        </div>
        <div class="t-step <?= $order['order_status'] == 'Delivered' ? 'active' : '' ?>">
          <div class="t-dot"><i class="fas fa-house-chimney"></i></div>
          <div class="t-label">Delivered</div>
        </div>
      </div>

      <hr>

      <h3>Order Summary</h3>
      <?php while ($item = mysqli_fetch_assoc($items)): ?>
        <div class="item-card">
          <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
          <div style="flex:1">
            <strong><?= htmlspecialchars($item['name']) ?></strong>
            <div class="muted">Handcrafted Quantity: <?= $item['quantity'] ?></div>
          </div>
        </div>
      <?php endwhile; ?>

      <hr>

      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <p style="font-size: 16px; font-weight: 400;">Total Investment: <span style="color:var(--accent); font-size: 22px; font-family: 'Playfair Display', serif; margin-left: 10px;">₹<?= number_format($order['final_amount'], 2) ?></span></p>
        <div>
          <a href="collection.php" class="btn secondary">Shop More</a>
          <a href="index.php" class="btn secondary">Home</a>
        </div>
      </div>

      <div style="margin-top:50px; padding-top: 35px; border-top: 1px dashed var(--border-soft);">
        <p class="muted" style="margin-bottom: 15px;">Need real-time support regarding your masterpiece?</p>
        <a href="https://wa.me/919876543210?text=Hi, regarding my order #AUR-<?= $order_id ?>" class="btn btn-wa">
          <i class="fab fa-whatsapp"></i> WhatsApp Support
        </a>
      </div>

    </div>
  </div>

</body>
</html>