<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h3 class="mb-4">Manage Customers</h3>

    <table class="table table-bordered table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered On</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($customers)) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= $row['created_at'] ?? '-'; ?></td>
                <td>
                    <a href="delete_customer.php?id=<?= $row['id']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this customer?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>

</div>

</body>
</html>
