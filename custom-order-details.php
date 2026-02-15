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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Unavailable | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{
      background:#0f0d0b;
      color:#f3ede7;
      font-family:Poppins,sans-serif;
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:100vh;
    }
    .card{
      background:#171411;
      border:1px solid rgba(255,255,255,.12);
      padding:40px;
      max-width:420px;
      text-align:center;
    }
    .icon{
      font-size:42px;
      color:#c46a3b;
      margin-bottom:15px;
    }
    .muted{
      color:#b9afa6;
      font-size:14px;
      margin-top:10px;
    }
    .btn{
      display:inline-block;
      margin-top:25px;
      padding:12px 26px;
      background:#c46a3b;
      color:#fff;
      text-decoration:none;
      font-size:13px;
    }
  </style>
</head>
<body>

  <div class="card">
    <div class="icon">
      <i class="bi bi-info-circle"></i>
    </div>
    <h2>Request Unavailable</h2>
    <p class="muted">
      This custom order is no longer accessible.<br>
      It may have been completed or does not belong to your account.
    </p>
    <a href="order-history.php" class="btn">
      Back to My Orders
    </a>
  </div>

</body>
</html>
<?php
exit;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Request #<?= $custom['id'] ?> | Auraloom</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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
    <a href="order-history.php" class="muted">← My Orders</a>
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

      <?php if (!empty($custom['amount'])): ?>
        <div class="card-row">
          <strong>Final Amount (Admin):</strong>
          <span style="color:#7dd87d;font-size:16px;">
            ₹<?= number_format($custom['amount'], 2) ?>
          </span>
        </div>

        <div class="card-row">
          <strong>Payment Status:</strong>
          <?= ucfirst($custom['payment_status']) ?>
        </div>
      <?php else: ?>
        <div class="card-row muted">
          Final amount will be shared after admin review.
        </div>
      <?php endif; ?>

      <hr style="margin:20px 0;border:0;border-top:1px solid var(--border)">

      <div class="card-row">
        <strong>Your Idea / Requirements</strong>
      </div>

      <div class="idea">
        <?= nl2br(htmlspecialchars($custom['idea'])) ?>
      </div>

    </div>

    <div class="actions">
      <?php if (
        $custom['status'] === 'approved' &&
        $custom['payment_status'] === 'Requested' &&
        !empty($custom['amount'])
      ): ?>

        <form method="post" action="pay-custom.php">
          <input type="hidden" name="order_id" value="<?= $custom['id'] ?>">
          <button class="btn primary">
            <i class="bi bi-check-circle-fill"></i> Payment Done
          </button>
        </form>

      <?php elseif ($custom['payment_status'] === 'Paid'): ?>

        <span class="muted" style="color:#7dd87d;">
          <i class="bi bi-check-lg"></i> Payment completed. Our team is working on your order.
        </span>

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