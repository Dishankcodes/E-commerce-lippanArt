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
<html>

<head>
  <title>Manage Carts | Admin</title>
  <style>
    body {
      background: #0f0d0b;
      color: #f3ede7;
      font-family: Poppins, sans-serif;
      padding: 40px;
    }

    h2 {
      font-family: Playfair Display, serif;
      margin-bottom: 20px
    }

    .stats {
      display: flex;
      gap: 20px;
      margin-bottom: 40px
    }

    .card {
      background: #171411;
      border: 1px solid rgba(255, 255, 255, .12);
      padding: 20px;
      flex: 1;
    }

    .card h4 {
      font-size: 12px;
      color: #b9afa6;
      letter-spacing: 1px
    }

    .card h3 {
      font-size: 36px;
      color: #c46a3b
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 14px;
      border-bottom: 1px solid rgba(255, 255, 255, .12);
      text-align: left;
    }

    th {
      font-size: 12px;
      color: #b9afa6;
      text-transform: uppercase
    }

    .status {
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 20px;
    }

    .active {
      background: #7dd87d22;
      color: #7dd87d
    }

    .abandoned {
      background: #ff6b6b22;
      color: #ff6b6b
    }

    .btn {
      padding: 6px 14px;
      background: #25D366;
      color: #fff;
      text-decoration: none;
      font-size: 12px;
      border-radius: 6px;
    }
  </style>
</head>

<body>

  <h2>Manage Carts</h2>

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
    <tr>
      <th>Customer</th>
      <th>Items</th>
      <th>Cart Value</th>
      <th>Last Active</th>
      <th>Status</th>
      <th>Contact</th>
    </tr>

    <?php while ($c = mysqli_fetch_assoc($carts)):
      $days = (time() - strtotime($c['updated_at'])) / 86400;
      $status = $days <= 1 ? 'active' : 'abandoned';
      ?>
      <tr>
        <td>
          <?= htmlspecialchars($c['name']) ?><br>
          <small><?= htmlspecialchars($c['email']) ?></small>
        </td>
        <td><?= $c['total_items'] ?? 0 ?></td>
        <td>₹<?= number_format($c['cart_value'] ?? 0) ?></td>
        <td><?= date("d M Y", strtotime($c['updated_at'])) ?></td>
        <td>
          <span class="status <?= $status ?>">
            <?= ucfirst($status) ?>
          </span>
        </td>
        <td>
          <a class="btn" href="https://wa.me/91<?= $c['phone'] ?>?text=Hi, you left items in your Auraloom cart."
            target="_blank">
            WhatsApp
          </a>
        </td>
      </tr>
    <?php endwhile; ?>

  </table>

</body>

</html>