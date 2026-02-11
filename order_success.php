<?php
$order_id = $_GET['order_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 text-center">
    <h2 class="text-success">Order Placed Successfully!</h2>
    <p>Your Order ID: <strong>#<?php echo $order_id; ?></strong></p>

    <a href="index.php" class="btn btn-dark mt-3">
        Continue Shopping
    </a>
</div>

</body>
</html>
