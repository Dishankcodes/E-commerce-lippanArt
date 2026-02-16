<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit;
}

/* ===== HANDLE STATUS UPDATE ===== */
if (isset($_POST['enquiry_id'], $_POST['action'])) {
  $id = (int) $_POST['enquiry_id'];

  $status = match ($_POST['action']) {
    'progress' => 'In Progress',
    'resolve' => 'Resolved',
    default => 'New'
  };

  mysqli_query($conn, "
        UPDATE contact_enquiries
        SET status = '$status'
        WHERE id = $id
    ");
}

/* ===== FETCH ENQUIRIES ===== */
$enquiries = mysqli_query($conn, "
    SELECT *
    FROM contact_enquiries
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Contact Issues | Auraloom Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* 🔥 SAME VARIABLES + THEME YOU SENT */
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
    }

    body {
      font-family: Poppins, sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
    }

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
    }

    .btn-dash {
      border: 1px solid var(--accent);
      color: var(--accent);
      padding: 8px 22px;
      text-decoration: none;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 14px;
    }

    .btn-dash:hover {
      background: var(--accent);
      color: #fff
    }

    .table {
      background: var(--bg-soft);
      color: var(--text-main);
    }

    .table th {
      color: var(--accent);
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 1px;
      border-bottom: 1px solid var(--border-soft);
    }

    .table td {
      border-color: var(--border-soft);
      font-size: 14px;
    }

    .status {
      font-size: 12px;
      padding: 4px 10px;
      border-radius: 20px;
    }

    .New {
      background: #444
    }

    .In\ Progress {
      background: var(--warning)
    }

    .Resolved {
      background: var(--success)
    }

    .actions-form {
      display: flex;
      gap: 8px;
    }

    .btn-action {
      background: transparent;
      border: 1px solid var(--border-soft);
      padding: 6px 14px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      cursor: pointer;
    }

    .btn-progress {
      color: var(--warning);
      border-color: var(--warning)
    }

    .btn-resolve {
      color: var(--success);
      border-color: var(--success)
    }

    .empty {
      text-align: center;
      padding: 60px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
    }

    /* ===== FORCE DARK TABLE THEME ===== */
    .table,
    .table thead,
    .table tbody,
    .table tr,
    .table th,
    .table td {
      background-color: var(--bg-soft) !important;
      color: var(--text-main) !important;
      border-color: var(--border-soft) !important;
    }

    /* Header row */
    .table thead th {
      background-color: rgba(255, 255, 255, 0.04) !important;
      color: var(--accent) !important;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 12px;
    }

    /* Row hover */
    .table tbody tr:hover td {
      background-color: rgba(255, 255, 255, 0.03) !important;
    }

    /* Message text style */
    .table td.message-cell {
      color: var(--text-muted) !important;
      font-style: italic;
    }

    /* Status pills */
    .status {
      font-size: 12px;
      padding: 5px 12px;
      border-radius: 20px;
      display: inline-block;
    }

    .status.New {
      background: #3a3a3a;
    }

    .status.In\ Progress {
      background: rgba(255, 179, 71, 0.2);
      color: #ffb347;
    }

    .status.Resolved {
      background: rgba(125, 216, 125, 0.2);
      color: #7dd87d;
    }
  </style>
</head>

<body>

  <div class="container mt-5 mb-5">

    <div class="header-flex">
      <h3>📩 Contact & Support Enquiries</h3>
      <a href="dashboard.php" class="btn-dash">← Back to Dashboard</a>
    </div>

    <?php if (mysqli_num_rows($enquiries) === 0): ?>
      <div class="empty">No contact enquiries found 🎉</div>
    <?php else: ?>

      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Name</th>
              <th>Phone</th>
              <th>Purpose</th>
              <th>Message</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php while ($e = mysqli_fetch_assoc($enquiries)): ?>
              <tr>
                <td><?= date("d M Y", strtotime($e['created_at'])) ?></td>
                <td><?= htmlspecialchars($e['name']) ?></td>
                <td><?= htmlspecialchars($e['phone']) ?></td>
                <td><?= htmlspecialchars($e['purpose']) ?></td>
                <td class="message-cell">
                  "<?= nl2br(htmlspecialchars($e['message'])) ?>"
                </td>

                <td>
                  <span class="status <?= $e['status'] ?>">
                    <?= $e['status'] ?>
                  </span>
                </td>
                <td>
                  <form method="post" class="actions-form">
                    <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                    <button name="action" value="progress" class="btn-action btn-progress">In Progress</button>
                    <button name="action" value="resolve" class="btn-action btn-resolve">Resolve</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>

  </div>
</body>

</html>