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
  <title>Custom Orders | Admin</title>

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
      --info: #6cbcff;
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
      font-size: 34px;
      margin-bottom: 30px;
    }

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
      color: var(--text-muted);
      text-transform: uppercase;
      text-align: left;
    }

    tr:hover {
      background: rgba(255, 255, 255, .02);
    }

    .status {
      font-size: 11px;
      padding: 4px 10px;
      border-radius: 4px;
      letter-spacing: 1px;
      text-transform: uppercase;
      display: inline-block;
    }

    .approved {
      background: rgba(125, 216, 125, .15);
      color: var(--success);
    }

    .contacted {
      background: rgba(255, 179, 71, .15);
      color: var(--warning);
    }

    .completed {
      background: rgba(108, 188, 255, .15);
      color: var(--info);
    }

    .actions a {
      display: inline-block;
      padding: 6px 12px;
      font-size: 11px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      text-decoration: none;
      margin: 3px 3px 3px 0;
      transition: .3s;
    }

    .actions a:hover {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .whatsapp {
      border-color: #25d366;
      color: #25d366;
    }

    .whatsapp:hover {
      background: #25d366;
      border-color: #25d366;
      color: #fff;
    }

    .btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 24px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      text-decoration: none;
      transition: .3s;
      font-size: 13px;
      letter-spacing: 1px;
    }

    .btn:hover {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

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

    .actions form {
      margin-top: 6px;
    }

    .actions input {
      background: #1b1815;
      border: 1px solid var(--border-soft);
      color: #fff;
    }

    .actions button {
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 11px;
      padding: 6px 10px;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <div class="container">

    <h3> Custom Order Requests</h3>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Budget</th>
          <th>Status</th>
          <th>Amount</th>
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
              <small style="color:#b9afa6"><?= htmlspecialchars($row['email']) ?></small>
            </td>

            <td><?= htmlspecialchars($row['order_type']) ?></td>

            <td>
              <?= $row['budget'] ? '₹' . number_format($row['budget']) : '—' ?>
            </td>

            <td>
              <span class="status <?= $row['status'] ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>

            <td>
              <?php if ($row['amount']): ?>
                ₹<?= number_format($row['amount'], 2) ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>

            <td>
              <?php if ($row['payment_status'] === 'Paid'): ?>
                <span class="status approved">Paid</span>
              <?php elseif ($row['payment_status'] === 'Requested'): ?>
                <span class="status contacted">Requested</span>
              <?php else: ?>
                <span class="status">Pending</span>
              <?php endif; ?>
            </td>

            <td class="actions">

              <?php if (
                $row['payment_status'] === 'Pending'
                && in_array($row['status'], ['approved', 'contacted'])
              ): ?>
                <form method="post" style="display:flex;gap:6px;align-items:center;">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <input type="number" name="amount" placeholder="₹ Amount" required style="width:90px;padding:4px">
                  <button name="set_amount">Request</button>
                </form>
              <?php endif; ?>

              <?php if ($row['payment_status'] === 'Paid' && $row['status'] !== 'completed'): ?>
                <a href="?update=completed&id=<?= $row['id'] ?>">
                  Mark Completed
                </a>
              <?php endif; ?>

              <!-- STATUS CONTROLS -->
              <?php if ($row['status'] === 'pending'): ?>
                <a href="?update=approved&id=<?= $row['id'] ?>">Approve</a>
              <?php endif; ?>

              <?php if ($row['status'] === 'approved'): ?>
                <a href="?update=contacted&id=<?= $row['id'] ?>">Mark Contacted</a>
              <?php endif; ?>

              <?php if (
                $row['status'] === 'contacted'
                && $row['payment_status'] === 'Paid'
              ): ?>
                <a href="?update=completed&id=<?= $row['id'] ?>">Mark Completed</a>
              <?php endif; ?>

              <a href="https://wa.me/91<?= $row['phone'] ?>" target="_blank" class="whatsapp">
                WhatsApp
              </a>

            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn">← Back to Dashboard</a>

  </div>

</body>

</html>