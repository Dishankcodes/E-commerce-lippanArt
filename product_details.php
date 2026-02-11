<?php
include("db.php");

$id = $_GET['id'];
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id='$id'"));

if($product['stock'] <= 3 && $product['stock'] > 0){
    echo "<p class='text-warning'>Hurry! Only ".$product['stock']." left.</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<div class="row">

<div class="col-md-6">
<img src="uploads/<?php echo $product['image']; ?>" class="img-fluid">
</div>

<div class="col-md-6">

<h3><?php echo $product['name']; ?></h3>
<h4 class="text-danger">₹<?php echo $product['price']; ?></h4>

<p><?php echo $product['description']; ?></p>

<hr>

<h5>More Details</h5>
<p><?php echo nl2br($product['more_details']); ?></p>

<hr>

<h5>Specifications</h5>
<p><?php echo nl2br($product['specifications']); ?></p>

<?php if($product['stock'] > 0) { ?>

<form action="cart.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

    <div class="d-flex mb-3">
        <input type="number" 
               name="quantity" 
               value="1" 
               min="1" 
               max="<?php echo $product['stock']; ?>"
               class="form-control w-25 me-2">

        <button type="submit" name="add_to_cart" class="btn btn-dark">
            Add to Cart
        </button>
    </div>

    <small class="text-success">
        <?php echo $product['stock']; ?> items available
    </small>

</form>

<?php } else { ?>

    <button class="btn btn-danger" disabled>Out of Stock</button>

<?php } ?>

</div>

</div>
</div>

</body>
</html>
