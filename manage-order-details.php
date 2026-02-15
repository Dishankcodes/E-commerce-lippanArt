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
  <meta charset="UTF-8">
  <title>Order #<?= $order_id ?> | Auraloom Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* --- BRAND VARIABLES --- */
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --card-bg: #1b1815;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, 0.12);
      
      /* Status Colors */
      --st-pending: #ffb347;
      --st-processing: #6cbcff;
      --st-shipped: #a56eff;
      --st-delivered: #7dd87d;
      --st-cancelled: #ff6b6b;
    }

    /* --- GLOBAL OVERRIDES --- */
    body {
      background-color: var(--bg-dark) !important;
      color: var(--text-main) !important;
      font-family: 'Poppins', sans-serif !important;
    }

    h3, h5 {
        font-family: 'Playfair Display', serif !important;
        color: var(--text-main) !important;
        margin: 0 !important;
    }

    /* --- HEADER --- */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-soft);
    }

    .btn-back {
        border: 1px solid var(--accent);
        color: var(--accent);
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-back:hover {
        background-color: var(--accent);
        color: #fff;
    }

    /* --- INFO CARDS --- */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
    }

    .info-card {
        background: var(--bg-soft);
        border: 1px solid var(--border-soft);
        padding: 25px;
    }

    .info-card h5 {
        font-size: 18px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 10px;
        margin-bottom: 15px !important;
        color: var(--accent) !important;
    }

    .info-row {
        display: flex;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .info-label {
        width: 100px;
        color: var(--text-muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-value {
        flex: 1;
        color: var(--text-main);
    }

    /* --- STATUS BADGES --- */
    .badge-status {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .Pending { color: var(--st-pending); background: rgba(255, 179, 71, 0.15); border: 1px solid var(--st-pending); }
    .Processing { color: var(--st-processing); background: rgba(108, 188, 255, 0.15); border: 1px solid var(--st-processing); }
    .Shipped { color: var(--st-shipped); background: rgba(165, 110, 255, 0.15); border: 1px solid var(--st-shipped); }
    .Delivered { color: var(--st-delivered); background: rgba(125, 216, 125, 0.15); border: 1px solid var(--st-delivered); }
    .Cancelled { color: var(--st-cancelled); background: rgba(255, 107, 107, 0.15); border: 1px solid var(--st-cancelled); }

    /* --- PRODUCTS TABLE --- */
    .table-container {
        background: var(--bg-soft);
        border: 1px solid var(--border-soft);
        padding: 0;
    }

    .table {
        margin-bottom: 0;
        color: var(--text-muted);
    }

    .table th {
        background: rgba(255,255,255,0.03);
        border-bottom: 1px solid var(--border-soft);
        color: var(--accent);
        font-family: 'Playfair Display', serif;
        font-weight: 400;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
        padding: 15px;
    }

    .table td {
        background: transparent;
        border-bottom: 1px solid var(--border-soft);
        color: var(--text-main);
        padding: 15px;
        vertical-align: middle;
    }

    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border: 1px solid var(--border-soft);
        border-radius: 4px;
    }

    .grand-total-row td {
        background: rgba(196, 106, 59, 0.05);
        color: var(--accent);
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        padding: 20px 15px;
        border-bottom: none;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .info-grid { grid-template-columns: 1fr; }
        .table { min-width: 600px; }
        .table-container { overflow-x: auto; }
    }
  </style>
</head>

<body>

  <div class="container mt-5 mb-5">

    <div class="header-flex">
        <h3>📦 Order #<?= $order_id ?></h3>
        <a href="manage_orders.php" class="btn-back">← Back to Orders</a>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <h5>Customer Details</h5>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Ordered:</span>
                <span class="info-value"><?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></span>
            </div>
        </div>

        <div class="info-card">
            <h5>Shipping Info</h5>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value" style="line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($order['address'])) ?>
                </span>
            </div>
            <div class="info-row" style="margin-top: 20px; align-items: center;">
                <span class="info-label">Status:</span>
                <span class="badge-status <?= $order['order_status'] ?>">
                    <?= htmlspecialchars($order['order_status']) ?>
                </span>
            </div>
        </div>
    </div>

    <h5 style="margin-bottom: 20px;">Order Items</h5>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th width="12%">Image</th>
                    <th width="40%">Product Name</th>
                    <th width="13%" class="text-center">Qty</th>
                    <th width="15%" class="text-end">Price</th>
                    <th width="20%" class="text-end">Total</th>
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
                    <td>
                        <img src="uploads/<?= htmlspecialchars($i['image']) ?>" class="product-img" alt="Product">
                    </td>
                    <td>
                        <span style="font-weight: 500;"><?= htmlspecialchars($i['name']) ?></span>
                    </td>
                    <td class="text-center"><?= $i['quantity'] ?></td>
                    <td class="text-end" style="color:var(--text-muted)">₹<?= number_format($i['price'], 2) ?></td>
                    <td class="text-end">₹<?= number_format($total, 2) ?></td>
                  </tr>
                <?php } ?>
            </tbody>

            <tfoot>
                <tr class="grand-total-row">
                    <td colspan="4" class="text-end">Grand Total</td>
                    <td class="text-end">₹<?= number_format($grand, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

  </div>

</body>
</html>