<?php
session_start();
include("db.php");

/* ===== ADMIN CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit();
}

/* ===== SET AMOUNT & REQUEST PAYMENT ===== */
if (isset($_POST['set_amount'])) {
  $id = (int) $_POST['id'];
  $amount = (float) $_POST['amount'];

  if ($amount > 0) {
    mysqli_query($conn, "
      UPDATE custom_orders
      SET amount='$amount', payment_status='Requested'
      WHERE id='$id'
    ");
  }

  header("Location: manage_custom_orders.php");
  exit();
}

/* ===== UPDATE STATUS ===== */
if (isset($_GET['update'], $_GET['id'])) {
  $id = (int) $_GET['id'];
  $status = $_GET['update'];

  $allowed = ['approved', 'contacted', 'completed'];

  if (in_array($status, $allowed)) {
    $stmt = mysqli_prepare(
      $conn,
      "UPDATE custom_orders SET status=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);
  }

  header("Location: manage_custom_orders.php");
  exit();
}

/* ===== FETCH ORDERS ===== */
$orders = mysqli_query(
  $conn,
  "SELECT * FROM custom_orders ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Manage Custom Orders | Auraloom Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
      
      --st-approved: #7dd87d;  /* Success Green */
      --st-contacted: #ffb347; /* Warning Orange */
      --st-info: #6cbcff;      /* Info Blue */
      --whatsapp: #25d366;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      line-height: 1.6;
    }

    .container {
      max-width: 1400px;
      margin: 60px auto;
      padding: 0 40px;
    }

    /* --- HEADER FLEX --- */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    h3 {
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

    /* --- TABLE STYLING --- */
    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
    }

    th {
      background-color: rgba(255,255,255,0.05);
      color: var(--accent);
      font-family: 'Playfair Display', serif;
      font-size: 13px;
      letter-spacing: 1px;
      text-transform: uppercase;
      text-align: left;
      padding: 18px 15px;
      border-bottom: 1px solid var(--border-soft);
    }

    td {
      padding: 18px 15px;
      border-bottom: 1px solid var(--border-soft);
      vertical-align: middle;
      font-size: 14px;
    }

    tr:hover td {
      background-color: rgba(255,255,255,0.02);
    }

    /* --- STATUS BADGES --- */
    .status {
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
      display: inline-block;
      text-align: center;
      min-width: 90px;
    }

    .approved { background: rgba(125, 216, 125, 0.15); color: var(--st-approved); border: 1px solid var(--st-approved); }
    .contacted { background: rgba(255, 179, 71, 0.15); color: var(--st-contacted); border: 1px solid var(--st-contacted); }
    .completed { background: rgba(108, 188, 255, 0.15); color: var(--st-info); border: 1px solid var(--st-info); }
    .pending { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); border: 1px solid var(--border-soft); }

    /* --- ACTION BUTTONS --- */
    .actions a, .actions button {
      display: inline-block;
      padding: 6px 12px;
      font-size: 11px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      text-decoration: none;
      margin: 3px 3px 3px 0;
      transition: 0.3s;
      background: transparent;
      text-transform: uppercase;
      cursor: pointer;
    }

    .actions a:hover, .actions button:hover {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .whatsapp { border-color: var(--whatsapp) !important; color: var(--whatsapp) !important; }
    .whatsapp:hover { background: var(--whatsapp) !important; color: #000 !important; }

    /* --- PAYMENT FORM --- */
    .payment-form {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-top: 10px;
    }

    .payment-form input {
      background: var(--bg-dark);
      border: 1px solid var(--border-soft);
      color: #fff;
      padding: 5px 8px;
      font-size: 12px;
      outline: none;
      width: 100px;
    }

    .payment-form button {
        background: var(--accent);
        border: none;
        color: #fff;
        padding: 5px 12px;
    }

    small { color: var(--text-muted); font-size: 12px; }
  </style>
</head>

<body>

  <div class="container">

    <div class="header-flex">
        <h3>Custom Order Requests</h3>
        <a href="dashboard.php" class="btn-dash">← Back to Dashboard</a>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Budget</th>
          <th>Status</th>
          <th>Final Amount</th>
          <th>Payment</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
          <tr>
            <td>#<?= $row['id'] ?></td>

            <td>
              <strong><?= htmlspecialchars($row['name']) ?></strong><br>
              <small><?= htmlspecialchars($row['email']) ?></small><br>
              <small style="color:var(--accent)"><?= htmlspecialchars($row['phone']) ?></small>
            </td>

            <td><?= htmlspecialchars($row['order_type']) ?></td>

            <td>
              <?= $row['budget'] ? '₹' . number_format($row['budget']) : '—' ?>
            </td>

            <td>
              <span class="status <?= strtolower($row['status']) ?>">
                <?= ucfirst($row['status']) ?: 'Pending' ?>
              </span>
            </td>

            <td>
              <?php if ($row['amount']): ?>
                <strong style="color:var(--accent)">₹<?= number_format($row['amount'], 2) ?></strong>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($row['payment_status'] === 'Paid'): ?>
                <span class="status approved">Paid</span>
              <?php elseif ($row['payment_status'] === 'Requested'): ?>
                <span class="status contacted">Requested</span>
              <?php else: ?>
                <span class="status pending">Unpaid</span>
              <?php endif; ?>
            </td>

            <td class="actions">
              <?php if ($row['payment_status'] === 'Pending' && in_array($row['status'], ['approved', 'contacted'])): ?>
                <form method="post" class="payment-form">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <input type="number" name="amount" placeholder="₹ Amount" required>
                  <button type="submit" name="set_amount">Request</button>
                </form>
              <?php endif; ?>

              <?php if ($row['status'] === 'pending'): ?>
                <a href="?update=approved&id=<?= $row['id'] ?>">Approve</a>
              <?php endif; ?>

              <?php if ($row['status'] === 'approved'): ?>
                <a href="?update=contacted&id=<?= $row['id'] ?>">Mark Contacted</a>
              <?php endif; ?>

              <?php if ($row['payment_status'] === 'Paid' && $row['status'] !== 'completed'): ?>
                <a href="?update=completed&id=<?= $row['id'] ?>">Mark Completed</a>
              <?php endif; ?>

              <a href="https://wa.me/91<?= $row['phone'] ?>?text=Hello <?= htmlspecialchars($row['name']) ?>, this is Auraloom regarding your custom order #<?= $row['id'] ?>" target="_blank" class="whatsapp">
                <i class="fab fa-whatsapp"></i> WhatsApp
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

  </div>

</body>
</html>