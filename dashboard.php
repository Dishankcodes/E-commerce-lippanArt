<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

// Total Customers
$customer_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'];

// Total Products
$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];

// Total Categories
$category_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];

// Total Orders
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];

// Recent Customers
$recent_customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC LIMIT 5");

// Recent Products
$recent_products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h3 class="mb-4">Admin Dashboard</h3>

    <!-- STAT CARDS -->
    <div class="row">

        <div class="col-md-3">
            <a href="manage_customers.php" style="text-decoration:none;">
                <div class="card text-white bg-primary mb-3 shadow">
                    <div class="card-body">
                        <h5>Total Customers</h5>
                        <h3><?php echo $customer_count; ?></h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="manage_products.php" style="text-decoration:none;">
                <div class="card text-white bg-success mb-3 shadow">
                    <div class="card-body">
                        <h5>Total Products</h5>
                        <h3><?php echo $product_count; ?></h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="manage_categories.php" style="text-decoration:none;">
                <div class="card text-white bg-warning mb-3 shadow">
                    <div class="card-body">
                        <h5>Total Categories</h5>
                        <h3><?php echo $category_count; ?></h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="manage_orders.php" style="text-decoration:none;">
                <div class="card text-white bg-danger mb-3 shadow">
                    <div class="card-body">
                        <h5>Total Orders</h5>
                        <h3><?php echo $order_count; ?></h3>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- RECENT CUSTOMERS -->
    <div class="row mt-4">

        <div class="col-md-6">
            <div class="card shadow p-3">
                <h5>Recent Customers</h5>
                <ul class="list-group">
                    <?php while($row = mysqli_fetch_assoc($recent_customers)) { ?>
                        <li class="list-group-item">
                            <?php echo $row['name']; ?> (<?php echo $row['email']; ?>)
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <!-- RECENT PRODUCTS -->
        <div class="col-md-6">
            <div class="card shadow p-3">
                <h5>Recently Added Products</h5>
                <ul class="list-group">
                    <?php while($row = mysqli_fetch_assoc($recent_products)) { ?>
                        <li class="list-group-item">
                            <?php echo $row['name']; ?> - ₹<?php echo $row['price']; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>

    </div>

    <a href="logout.php" class="btn btn-dark mt-4">Logout</a>

</div>

</body>
</html>
