<?php
session_start();
include("db.php");

/* ===== ADMIN CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
  header("Location: admin_login.php");
  exit();
}

/* ===== UPDATE STATUS (INLINE) ===== */
if (isset($_GET['update']) && isset($_GET['id'])) {

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
<html>

<head>
  <title>Manage Custom Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container mt-4">

    <h3 class="mb-4">Custom Order Requests</h3>

    <table class="table table-bordered table-hover shadow align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Order Type</th>
          <th>Budget</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
          <tr>
            <td>#<?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['name']); ?><br>
              <small class="text-muted"><?= htmlspecialchars($row['email']); ?></small>
            </td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td><?= $row['order_type']; ?></td>
            <td><?= $row['budget'] ?: '-'; ?></td>
            <td>
              <?php
              $badge = match ($row['status']) {
                'approved' => 'success',
                'contacted' => 'warning',
                'completed' => 'secondary',
                default => 'info'
              };
              ?>
              <span class="badge bg-<?= $badge ?>">
                <?= ucfirst($row['status']); ?>
              </span>
            </td>

            <td>
              <a href="?update=approved&id=<?= $row['id']; ?>" class="btn btn-sm btn-success mb-1">
                Approve
              </a>

              <a href="?update=contacted&id=<?= $row['id']; ?>" class="btn btn-sm btn-warning mb-1">
                Contacted
              </a>

              <a href="?update=completed&id=<?= $row['id']; ?>" class="btn btn-sm btn-secondary mb-1">
                Completed
              </a>

              <a href="https://wa.me/91<?= $row['phone']; ?>" target="_blank" class="btn btn-sm btn-success mb-1">
                WhatsApp
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-dark mt-3">← Back to Dashboard</a>

  </div>

</body>

</html>