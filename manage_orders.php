<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

$orders = mysqli_query($conn, "
    SELECT orders.*, customers.name 
    FROM orders 
    JOIN customers ON orders.customer_id = customers.id
    ORDER BY orders.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h3 class="mb-4">Manage Orders</h3>

    <table class="table table-bordered table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($orders)) { ?>
            <tr>
                <td>#<?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td>₹<?= $row['total_amount']; ?></td>
                <td>
                    <span class="badge bg-info">
                        <?= ucfirst($row['status']); ?>
                    </span>
                </td>
                <td><?= $row['created_at'] ?? '-'; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>

</div>

</body>
</html>
