<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$order_id = (int) ($_GET['id'] ?? 0);
$customer_id = (int) $_SESSION['customer_id'];

/* FETCH ORDER */
$order = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT *
  FROM orders
  WHERE id = $order_id
    AND customer_id = $customer_id
    AND order_source = 'store'
  LIMIT 1
"));

if (!$order) {
  die("Order not found.");
}

/* FETCH ITEMS */
$items = mysqli_query($conn, "
  SELECT oi.quantity, oi.price, p.name, p.image
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = $order_id
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order #<?= $order['id'] ?> | Auraloom</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card: #1b1815;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border: rgba(255, 255, 255, .12);
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

    /* HEADER */
    header {
      padding: 22px 60px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
    }

    a {
      text-decoration: none;
      color: inherit
    }

    /* CONTAINER */
    .container {
      max-width: 900px;
      margin: 60px auto;
      padding: 0 20px;
    }

    h1 {
      font-family: 'Playfair Display', serif;
      font-size: 36px;
      margin-bottom: 10px;
    }

    .muted {
      color: var(--text-muted);
      font-size: 14px;
    }

    /* STATUS BADGE */
    .status {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      background: #1b1815;
      color: var(--accent);
      margin-top: 10px;
    }

    /* ORDER ITEMS */
    .item {
      display: grid;
      grid-template-columns: 90px 1fr auto;
      gap: 20px;
      padding: 20px 0;
      border-bottom: 1px solid var(--border);
      align-items: center;
    }

    .item img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 6px;
    }

    .item-name {
      font-weight: 500;
    }

    .item-sub {
      font-size: 13px;
      color: var(--text-muted);
    }

    /* TOTAL */
    .total {
      text-align: right;
      font-size: 22px;
      margin-top: 30px;
      color: var(--accent);
      font-family: 'Playfair Display', serif;
    }

    /* ACTIONS */
    .actions {
      margin-top: 40px;
      display: flex;
      gap: 16px;
    }

    .btn {
      padding: 12px 26px;
      font-size: 13px;
      letter-spacing: 1px;
      border-radius: 30px;
      border: 1px solid var(--border);
      transition: .3s;
    }

    .btn.primary {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .btn.secondary {
      color: var(--text-muted);
    }

    .btn:hover {
      transform: translateY(-2px);
    }

    @media(max-width:600px) {
      .item {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .total {
        text-align: center
      }

      .actions {
        flex-direction: column
      }
    }
  </style>
</head>

<body>

  <header>
    <div class="logo">AURALOOM</div>
    <a href="order-history.php" class="muted">← My Orders</a>
  </header>

  <div class="container">

    <h1>Order #<?= $order['id'] ?></h1>
    <p class="muted">
      Placed on <?= date("d M Y", strtotime($order['created_at'])) ?>
    </p>

    <span class="status">
      <?= ucfirst($order['order_status']) ?>
    </span>

    <div style="margin-top:40px">

      <?php while ($i = mysqli_fetch_assoc($items)): ?>
        <div class="item">
          <img src="uploads/<?= htmlspecialchars($i['image']) ?>" alt="">
          <div>
            <div class="item-name"><?= htmlspecialchars($i['name']) ?></div>
            <div class="item-sub">
              Qty: <?= $i['quantity'] ?>
            </div>
          </div>
          <div class="item-sub">
            ₹<?= number_format($i['price'], 2) ?>
          </div>
        </div>
      <?php endwhile; ?>

    </div>

    <div class="total">
      Total Paid: ₹<?= number_format($order['final_amount'], 2) ?>
    </div>

    <div class="actions">
      <a href="track-order.php?id=<?= $order_id ?>" class="btn primary">
        Track Order
      </a>
      <a href="collection.php" class="btn secondary">
        Buy Again
      </a>
    </div>

  </div>

</body>

</html>