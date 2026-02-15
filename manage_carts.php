<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit;
}

/* ===== STATS ===== */
$total_carts = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) total FROM carts
"))['total'];

$active_carts = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) total
  FROM carts
  WHERE updated_at >= NOW() - INTERVAL 1 DAY
"))['total'];

$abandoned_carts = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) total
  FROM carts
  WHERE updated_at < NOW() - INTERVAL 3 DAY
"))['total'];

/* ===== CART LIST ===== */
$carts = mysqli_query($conn, "
SELECT 
  ca.id AS cart_id,
  cu.name,
  cu.email,
  cu.phone,
  ca.updated_at,
  SUM(p.price * ci.quantity) cart_value,
  SUM(ci.quantity) total_items
FROM carts ca
JOIN customers cu ON ca.user_id = cu.id
LEFT JOIN cart_items ci ON ca.id = ci.cart_id
LEFT JOIN products p ON ci.product_id = p.id
GROUP BY ca.id
ORDER BY ca.updated_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Carts | Auraloom Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  
  <style>
    /* --- BRAND VARIABLES --- */
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --accent-hover: #a85830;
      --border-soft: rgba(255, 255, 255, 0.12);
      --st-active: #7dd87d;
      --st-abandoned: #ff6b6b;
      --whatsapp: #25d366;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: var(--bg-dark);
      color: var(--text-main);
      font-family: 'Poppins', sans-serif;
      padding: 60px 80px;
    }

    /* --- HEADER --- */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin: 0;
    }

    .btn-dash {
        border: 1px solid var(--accent);
        color: var(--accent);
        padding: 8px 22px;
        text-decoration: none;
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: 0.3s;
    }

    .btn-dash:hover {
        background-color: var(--accent);
        color: #fff;
    }

    /* --- STATS CARDS --- */
    .stats {
      display: flex;
      gap: 25px;
      margin-bottom: 50px;
    }

    .card {
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
      padding: 30px;
      flex: 1;
      transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        border-color: var(--accent);
    }

    .card h4 {
      font-size: 11px;
      color: var(--text-muted);
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 15px;
    }

    .card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      color: var(--accent);
      margin: 0;
      font-weight: 500;
    }

    /* --- TABLE --- */
    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--bg-soft);
    }

    th, td {
      padding: 18px 15px;
      border-bottom: 1px solid var(--border-soft);
      text-align: left;
    }

    th {
      font-size: 12px;
      color: var(--accent);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-family: 'Playfair Display', serif;
      background: rgba(255,255,255,0.03);
    }

    td {
      font-size: 14px;
      color: var(--text-main);
      vertical-align: middle;
    }

    tr:hover td {
      background-color: rgba(255,255,255,0.02);
    }

    /* --- STATUS BADGES --- */
    .status {
      padding: 6px 14px;
      font-size: 11px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
      display: inline-block;
    }

    .active {
      background: rgba(125, 216, 125, 0.15);
      color: var(--st-active);
      border: 1px solid var(--st-active);
    }

    .abandoned {
      background: rgba(255, 107, 107, 0.15);
      color: var(--st-abandoned);
      border: 1px solid var(--st-abandoned);
    }

    /* --- WHATSAPP BUTTON --- */
    .btn-wa {
      padding: 8px 16px;
      background: transparent;
      color: var(--whatsapp);
      border: 1px solid var(--whatsapp);
      text-decoration: none;
      font-size: 11px;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 1px;
      transition: 0.3s;
    }

    .btn-wa:hover {
      background: var(--whatsapp);
      color: #000;
    }

    small { color: var(--text-muted); font-size: 12px; }
  </style>
</head>

<body>

  <div class="header-flex">
    <h2>Manage Carts</h2>
    <a href="dashboard.php" class="btn-dash">← Back to Dashboard</a>
  </div>

  <div class="stats">
    <div class="card">
      <h4>Total Carts</h4>
      <h3><?= $total_carts ?></h3>
    </div>
    <div class="card">
      <h4>Active (24h)</h4>
      <h3><?= $active_carts ?></h3>
    </div>
    <div class="card">
      <h4>Abandoned</h4>
      <h3><?= $abandoned_carts ?></h3>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Customer</th>
        <th>Items</th>
        <th>Cart Value</th>
        <th>Last Active</th>
        <th>Status</th>
        <th>Contact</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($c = mysqli_fetch_assoc($carts)):
        $days = (time() - strtotime($c['updated_at'])) / 86400;
        $status = $days <= 1 ? 'active' : 'abandoned';
        ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($c['name']) ?></strong><br>
            <small><?= htmlspecialchars($c['email']) ?></small>
          </td>
          <td><?= $c['total_items'] ?? 0 ?> Items</td>
          <td style="color:var(--accent); font-weight:500;">₹<?= number_format($c['cart_value'] ?? 0) ?></td>
          <td><?= date("d M Y", strtotime($c['updated_at'])) ?></td>
          <td>
            <span class="status <?= $status ?>">
              <?= ucfirst($status) ?>
            </span>
          </td>
          <td>
            <a class="btn-wa" href="https://wa.me/91<?= $c['phone'] ?>?text=Hi <?= htmlspecialchars($c['name']) ?>, we noticed you left items in your Auraloom cart. Is there anything we can help you with?"
              target="_blank">
              WhatsApp
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>