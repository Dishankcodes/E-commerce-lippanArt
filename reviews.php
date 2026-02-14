<?php
session_start();
include("../db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: login.php");
  exit;
}

/* ===== HANDLE APPROVE / REJECT ===== */
if (isset($_POST['review_id'], $_POST['action'])) {
  $review_id = intval($_POST['review_id']);
  $action = $_POST['action'];

  if ($action === 'approve') {
    $status = 'approved';
  } elseif ($action === 'reject') {
    $status = 'rejected';
  } else {
    $status = 'pending';
  }

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
<html>

<head>
  <title>Review Approval | Admin</title>
  <style>
    body {
      font-family: Arial;
      background: #111;
      color: #fff;
      padding: 40px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 12px;
      border-bottom: 1px solid #333;
      vertical-align: top;
    }

    th {
      color: #aaa;
      text-align: left;
    }

    .btn {
      padding: 6px 14px;
      border: none;
      cursor: pointer;
      margin-right: 6px;
    }

    .approve {
      background: #4caf50;
      color: #fff;
    }

    .reject {
      background: #f44336;
      color: #fff;
    }

    .stars {
      color: #ffb347;
    }
  </style>
</head>

<body>

  <h2>Pending Product Reviews</h2>

  <?php if (mysqli_num_rows($reviews) == 0): ?>
    <p style="color:#aaa;">No pending reviews 🎉</p>
  <?php else: ?>

    <table>
      <tr>
        <th>Product</th>
        <th>Customer</th>
        <th>Rating</th>
        <th>Review</th>
        <th>Date</th>
        <th>Action</th>
      </tr>

      <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
        <tr>
          <td><?= htmlspecialchars($r['product_name']) ?></td>
          <td><?= htmlspecialchars($r['customer_name']) ?></td>
          <td class="stars"><?= str_repeat("★", $r['rating']) ?></td>
          <td><?= nl2br(htmlspecialchars($r['review_text'])) ?></td>
          <td><?= date("M d, Y", strtotime($r['created_at'])) ?></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
              <button class="btn approve" name="action" value="approve">
                Approve
              </button>
              <button class="btn reject" name="action" value="reject">
                Reject
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>

    </table>
  <?php endif; ?>

</body>

</html>