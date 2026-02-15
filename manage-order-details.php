<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Order not found");
}

$order_id = (int) $_GET['id'];

/* ===== ORDER INFO ===== */
$order_q = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");
$order = mysqli_fetch_assoc($order_q);

if (!$order) {
  die("Order not found");
}

/* ===== ORDER ITEMS ===== */
$items = mysqli_query($conn, "
  SELECT oi.*, p.name, p.image
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Order #<?= $order_id ?> | Admin</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card-bg: #1b1815;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, .12);
      --success: #7dd87d;
      --warning: #ffb347;
      --danger: #ff6b6b;
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
    }

    .container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 30px;
    }

    h3 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 30px;
    }

    /* ===== CARD ===== */
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border-soft);
      padding: 26px;
      margin-bottom: 40px;
      line-height: 1.7;
    }

    .card strong {
      display: inline-block;
      width: 90px;
      color: var(--text-main);
    }

    /* ===== BADGES ===== */
    .badge {
      padding: 4px 10px;
      font-size: 11px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-left: 6px;
    }

    .badge.success {
      background: rgba(125, 216, 125, .15);
      color: var(--success);
    }

    .badge.warning {
      background: rgba(255, 179, 71, .15);
      color: var(--warning);
    }

    .badge.danger {
      background: rgba(255, 107, 107, .15);
      color: var(--danger);
    }

    /* ===== TABLE ===== */
    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
    }

    th,
    td {
      padding: 14px;
      border-bottom: 1px solid var(--border-soft);
      vertical-align: middle;
    }

    th {
      font-size: 12px;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--text-muted);
      text-align: left;
    }

    td img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border: 1px solid var(--border-soft);
    }

    tfoot td {
      font-weight: 500;
    }

    /* ===== BUTTON ===== */
    .btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 24px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      text-decoration: none;
      font-size: 13px;
      letter-spacing: 1px;
      transition: .3s;
    }

    .btn:hover {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    /* ===== MOBILE ===== */
    @media(max-width:900px) {

      table,
      thead,
      tbody,
      th,
      td,
      tr {
        display: block;
      }

      th {
        display: none;
      }

      td {
        padding: 12px 0;
      }
    }
  </style>
</head>

<body>

  <div class="container">

    <h3>📦 Order #<?= $order_id ?></h3>

    <div class="card">
      <strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
      <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?><br>
      <strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?><br>
      <strong>Status:</strong>
      <span class="badge 
        <?= $order['order_status'] == 'Delivered' ? 'success' :
          ($order['order_status'] == 'Cancelled' ? 'danger' : 'warning') ?>">
        <?= htmlspecialchars($order['order_status']) ?>
      </span>
      <br><br>
      <strong>Address:</strong><br>
      <?= nl2br(htmlspecialchars($order['address'])) ?>
    </div>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Image</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $grand = 0;
        while ($i = mysqli_fetch_assoc($items)) {
          $total = $i['quantity'] * $i['price'];
          $grand += $total;
          ?>
          <tr>
            <td><?= htmlspecialchars($i['name']) ?></td>
            <td><img src="uploads/<?= htmlspecialchars($i['image']) ?>"></td>
            <td><?= $i['quantity'] ?></td>
            <td>₹<?= number_format($i['price'], 2) ?></td>
            <td>₹<?= number_format($total, 2) ?></td>
          </tr>
        <?php } ?>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="4" style="text-align:right;">Grand Total</td>
          <td>₹<?= number_format($grand, 2) ?></td>
        </tr>
      </tfoot>
    </table>

    <a href="manage_orders.php" class="btn">← Back to Orders</a>

  </div>

</body>

</html>