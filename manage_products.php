<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

// DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    header("Location: manage_products.php");
    exit();
}

// UPDATE
if(isset($_POST['update_product'])){

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Check if new image uploaded
    if($_FILES['image']['name'] != ""){

        $image_name = $_FILES['image']['name'];
        $temp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($temp_name, "uploads/".$image_name);

        mysqli_query($conn, "UPDATE products 
        SET name='$name', price='$price', stock='$stock', description='$description', image='$image_name'
        WHERE id='$id'");
    } else {

        mysqli_query($conn, "UPDATE products 
        SET name='$name', price='$price', stock='$stock', description='$description'
        WHERE id='$id'");
    }

    header("Location: manage_products.php");
    exit();
}

$result = mysqli_query($conn, "
SELECT products.*, categories.category_name 
FROM products 
LEFT JOIN categories 
ON products.category_id = categories.id
");
?>


<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<h3>All Products</h3>

<table class="table table-bordered mt-3">
<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Category</th>
<th>Price</th>
<th>Stock</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><img src="uploads/<?php echo $row['image']; ?>" width="60"></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['category_name']; ?></td>
<td>₹<?php echo $row['price']; ?></td>
<td><?php echo $row['stock']; ?></td>
<td>

<!-- Edit Button -->
<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
Edit
</button>

<!-- Delete Button -->
<a href="?delete=<?php echo $row['id']; ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('Are you sure you want to delete this product?')">
Delete
</a>

</td>
</tr>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal<?php echo $row['id']; ?>">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5>Edit Product</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST" enctype="multipart/form-data">
<div class="modal-body">

<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<input type="text" name="name" class="form-control mb-2" value="<?php echo $row['name']; ?>" required>

<textarea name="description" class="form-control mb-2"><?php echo $row['description']; ?></textarea>

<input type="number" name="price" class="form-control mb-2" value="<?php echo $row['price']; ?>" required>

<input type="number" name="stock" class="form-control mb-2" value="<?php echo $row['stock']; ?>" required>

<label>Change Image (optional)</label>
<input type="file" name="image" class="form-control">

</div>

<div class="modal-footer">
<button type="submit" name="update_product" class="btn btn-success">Update</button>
</div>
</form>

</div>
</div>
</div>

<?php } ?>

</table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
