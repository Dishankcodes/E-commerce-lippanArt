<?php
session_start();
include("db.php");

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit;
}

$cid = (int) ($_GET['id'] ?? 0);
$customer_id = (int) $_SESSION['customer_id'];

$custom = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT *
  FROM custom_orders
  WHERE id = $cid
    AND email = (
      SELECT email FROM customers WHERE id = $customer_id
    )
  LIMIT 1
"));

if (!$custom) {
  die("Custom order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Request #<?= $custom['id'] ?> | Auraloom</title>

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
      max-width: 820px;
      margin: 70px auto;
      padding: 0 20px;
    }

    /* TITLE */
    h1 {
      font-family: 'Playfair Display', serif;
      font-size: 36px;
      margin-bottom: 10px;
    }

    .muted {
      color: var(--text-muted);
      font-size: 14px;
    }

    /* STATUS */
    .status {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      background: #1b1815;
      color: var(--accent);
      margin-top: 10px;
    }

    /* CARD */
    .card {
      background: var(--bg-soft);
      border: 1px solid var(--border);
      padding: 30px;
      margin-top: 40px;
    }

    .card-row {
      margin-bottom: 18px;
      font-size: 14px;
    }

    .card-row strong {
      color: var(--text-main);
    }

    /* IDEA BOX */
    .idea {
      background: #1b1815;
      border-left: 3px solid var(--accent);
      padding: 20px;
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    /* BUTTONS */
    .actions {
      margin-top: 30px;
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
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

    /* STATUS COLORS */
    .status.approved {
      color: #7dd87d
    }

    .status.pending {
      color: #ffb347
    }

    .status.rejected {
      color: #ff6b6b
    }

    @media(max-width:600px) {
      header {
        padding: 18px 20px
      }

      h1 {
        font-size: 30px
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
    <a href="custom-order.php" class="muted">← Custom Orders</a>
  </header>

  <div class="container">

    <h1>Custom Request #<?= $custom['id'] ?></h1>
    <p class="muted">Submitted on <?= date("d M Y", strtotime($custom['created_at'])) ?></p>

    <span class="status <?= htmlspecialchars($custom['status']) ?>">
      <?= ucfirst($custom['status']) ?>
    </span>

    <div class="card">

      <div class="card-row">
        <strong>Order Type:</strong>
        <?= htmlspecialchars($custom['order_type']) ?>
      </div>

      <div class="card-row">
        <strong>Estimated Budget:</strong>
        ₹<?= htmlspecialchars($custom['budget']) ?>
      </div>

      <hr style="margin:20px 0;border:0;border-top:1px solid var(--border)">

      <div class="card-row">
        <strong>Your Idea / Requirements</strong>
      </div>

      <div class="idea">
        <?= nl2br(htmlspecialchars($custom['idea'])) ?>
      </div>

    </div>

    <div class="actions">

      <?php if ($custom['status'] === 'approved'): ?>
        <a href="custom-checkout.php?id=<?= $custom['id'] ?>" class="btn primary">
          💳 Proceed to Payment
        </a>
      <?php else: ?>
        <span class="muted">
          Payment will be enabled once your request is approved.
        </span>
      <?php endif; ?>

      <a href="custom-order.php" class="btn secondary">
        New Custom Order
      </a>

    </div>

  </div>

</body>

</html>