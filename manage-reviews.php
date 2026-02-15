<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit;
}

/* ===== HANDLE APPROVE / REJECT ===== */
if (isset($_POST['review_id'], $_POST['action'])) {
  $review_id = (int) $_POST['review_id'];
  $action = $_POST['action'];

  $status = match ($action) {
    'approve' => 'approved',
    'reject' => 'rejected',
    default => 'pending'
  };

  mysqli_query($conn, "
    UPDATE product_reviews
    SET status = '$status'
    WHERE id = $review_id
  ");
}

/* ===== FETCH PENDING REVIEWS ===== */
$reviews = mysqli_query($conn, "
  SELECT 
    r.id,
    r.rating,
    r.review_text,
    r.created_at,
    p.name AS product_name,
    c.name AS customer_name
  FROM product_reviews r
  JOIN products p ON r.product_id = p.id
  JOIN customers c ON r.user_id = c.id
  WHERE r.status = 'pending'
  ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Pending Reviews | Admin</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg-dark:#0f0d0b;
      --bg-soft:#171411;
      --card-bg:#1b1815;
      --text-main:#f3ede7;
      --text-muted:#b9afa6;
      --accent:#c46a3b;
      --border-soft:rgba(255,255,255,.12);
      --success:#7dd87d;
      --danger:#ff6b6b;
      --star:#ffb347;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family:'Poppins',sans-serif;
      background:var(--bg-dark);
      color:var(--text-main);
    }

    .container {
      max-width:1200px;
      margin:60px auto;
      padding:0 30px;
    }

    h2 {
      font-family:'Playfair Display',serif;
      font-size:34px;
      margin-bottom:30px;
    }

    /* ===== TABLE ===== */
    table {
      width:100%;
      border-collapse:collapse;
      background:var(--bg-soft);
      border:1px solid var(--border-soft);
    }

    th, td {
      padding:14px;
      border-bottom:1px solid var(--border-soft);
      vertical-align:top;
    }

    th {
      font-size:12px;
      text-transform:uppercase;
      letter-spacing:1px;
      color:var(--text-muted);
      text-align:left;
    }

    tr:hover {
      background:rgba(255,255,255,.02);
    }

    /* ===== STARS ===== */
    .stars {
      color:var(--star);
      font-size:14px;
      letter-spacing:2px;
    }

    /* ===== ACTION BUTTONS ===== */
    .actions button {
      padding:6px 14px;
      font-size:11px;
      letter-spacing:1px;
      border:1px solid var(--border-soft);
      background:none;
      color:var(--text-muted);
      cursor:pointer;
      transition:.3s;
      margin-right:6px;
    }

    .actions .approve:hover {
      background:rgba(125,216,125,.15);
      border-color:var(--success);
      color:var(--success);
    }

    .actions .reject:hover {
      background:rgba(255,107,107,.15);
      border-color:var(--danger);
      color:var(--danger);
    }

    /* ===== EMPTY STATE ===== */
    .empty {
      color:var(--text-muted);
      font-size:14px;
      padding:30px 0;
    }

    /* ===== BACK BUTTON ===== */
    .btn {
      display:inline-block;
      margin-top:30px;
      padding:10px 24px;
      border:1px solid var(--border-soft);
      color:var(--text-muted);
      text-decoration:none;
      transition:.3s;
      font-size:13px;
      letter-spacing:1px;
    }

    .btn:hover {
      background:var(--accent);
      border-color:var(--accent);
      color:#fff;
    }

    /* ===== MOBILE ===== */
    @media(max-width:900px){
      table,thead,tbody,th,td,tr { display:block; }
      th { display:none; }
      td { padding:12px 0; }
    }
  </style>
</head>

<body>

<div class="container">

  <h2>📝 Pending Product Reviews</h2>

  <?php if (mysqli_num_rows($reviews) === 0): ?>
    <div class="empty">No pending reviews 🎉</div>
  <?php else: ?>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Customer</th>
          <th>Rating</th>
          <th>Review</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
          <tr>
            <td><?= htmlspecialchars($r['product_name']) ?></td>
            <td><?= htmlspecialchars($r['customer_name']) ?></td>

            <td class="stars">
              <?= str_repeat("★", (int)$r['rating']) ?>
            </td>

            <td><?= nl2br(htmlspecialchars($r['review_text'])) ?></td>

            <td><?= date("M d, Y", strtotime($r['created_at'])) ?></td>

            <td class="actions">
              <form method="post">
                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                <button class="approve" name="action" value="approve">Approve</button>
                <button class="reject" name="action" value="reject">Reject</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <a href="dashboard.php" class="btn">← Back to Dashboard</a>

</div>

</body>
</html>
