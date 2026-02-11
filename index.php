<?php
include("db.php");

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>LippanKart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2 class="text-center mb-4">Our Products</h2>

<div class="row">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="col-md-4 mb-4">
<div class="card shadow-sm">

<img src="uploads/<?php echo $row['image']; ?>" class="card-img-top" style="height:250px; object-fit:cover;">

<div class="card-body text-center">
<h5><?php echo $row['name']; ?></h5>
<p class="text-danger fw-bold">₹<?php echo $row['price']; ?></p>

<a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn btn-dark">
View Details
</a>

</div>
</div>
</div>

<?php } ?>

</div>
</div>

</body>
</html>
