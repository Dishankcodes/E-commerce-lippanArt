<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h3>All Categories</h3>

    <table class="table table-bordered mt-3">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['category_name']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
