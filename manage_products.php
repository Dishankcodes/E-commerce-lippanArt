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

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
    /* VARIABLES */
    :root {
        --bg-dark: #0f0d0b;
        --bg-soft: #171411;
        --text-main: #f3ede7;
        --text-muted: #b9afa6;
        --accent: #c46a3b;
        --accent-hover: #a85830;
        --border-soft: rgba(255, 255, 255, 0.12);
    }

    /* GLOBAL */
    body {
        background-color: var(--bg-dark) !important;
        color: var(--text-main) !important;
        font-family: 'Poppins', sans-serif !important;
    }

    h3, h5 {
        font-family: 'Playfair Display', serif !important;
        color: var(--text-main) !important;
    }

    /* TABLE */
    .table {
        background-color: var(--bg-soft) !important;
        border-color: var(--border-soft) !important;
        color: var(--text-muted) !important;
    }
    
    .table th {
        background-color: rgba(255,255,255,0.05) !important;
        color: var(--accent) !important;
        font-family: 'Playfair Display', serif !important;
        border-bottom: 1px solid var(--border-soft) !important;
        font-weight: normal !important;
        text-transform: uppercase !important;
        font-size: 14px !important;
    }

    .table td {
        background-color: transparent !important;
        border-color: var(--border-soft) !important;
        color: var(--text-main) !important;
        vertical-align: middle !important;
    }

    .table img {
        border: 1px solid var(--border-soft) !important;
        border-radius: 4px !important;
    }

    /* MODAL */
    .modal-content {
        background-color: var(--bg-soft) !important;
        border: 1px solid var(--border-soft) !important;
        color: var(--text-main) !important;
    }

    .modal-header, .modal-footer {
        border-color: var(--border-soft) !important;
    }

    .btn-close {
        filter: invert(1) !important;
    }

    /* INPUTS & LABELS */
    label {
        font-size: 13px !important;
        color: var(--text-muted) !important;
        margin-bottom: 5px !important;
        margin-top: 10px !important;
        display: block !important;
    }

    .form-control {
        background-color: var(--bg-dark) !important;
        border: 1px solid var(--border-soft) !important;
        color: var(--text-main) !important;
        padding: 10px !important;
    }
    
    .form-control:focus {
        background-color: var(--bg-dark) !important;
        border-color: var(--accent) !important;
        color: var(--text-main) !important;
        box-shadow: none !important;
    }

    /* BUTTONS */
    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-back {
        border: 1px solid var(--accent);
        color: var(--accent);
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: 0.3s;
        display: inline-block;
    }
    .btn-back:hover {
        background-color: var(--accent);
        color: #fff;
    }

    /* New "Add Product" Button Style */
    .btn-add {
        background-color: var(--accent);
        color: #fff;
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: 0.3s;
        display: inline-block;
        border: 1px solid var(--accent);
    }
    .btn-add:hover {
        background-color: var(--accent-hover);
        border-color: var(--accent-hover);
        color: #fff;
    }

    .btn-primary {
        background-color: transparent !important;
        border: 1px solid var(--accent) !important;
        color: var(--accent) !important;
    }
    .btn-primary:hover {
        background-color: var(--accent) !important;
        color: #fff !important;
    }
    .btn-danger {
        background-color: transparent !important;
        border: 1px solid #d9534f !important;
        color: #d9534f !important;
    }
    .btn-danger:hover {
        background-color: #d9534f !important;
        color: #fff !important;
    }
    .btn-success {
        background-color: var(--accent) !important;
        border: none !important;
        padding: 8px 25px !important;
    }
    .btn-success:hover {
        background-color: #a85830 !important;
    }

    /* Header Flex Container */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
</style>

</head>
<body>

<div class="container mt-5">

    <div class="page-header">
        <h3>All Products</h3>
        
        <div class="header-actions">
            <a href="add_product.php" class="btn-add">+ Add New Product</a>
            
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>
    </div>

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

    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
    Edit
    </button>

    <a href="?delete=<?php echo $row['id']; ?>" 
    class="btn btn-sm btn-danger"
    onclick="return confirm('Are you sure you want to delete this product?')">
    Delete
    </a>

    </td>
    </tr>

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

    <label>Product Name</label>
    <input type="text" name="name" class="form-control mb-2" value="<?php echo $row['name']; ?>" required>

    <label>Description</label>
    <textarea name="description" class="form-control mb-2"><?php echo $row['description']; ?></textarea>

    <label>Price (₹)</label>
    <input type="number" name="price" class="form-control mb-2" value="<?php echo $row['price']; ?>" required>

    <label>Stock Quantity</label>
    <input type="number" name="stock" class="form-control mb-2" value="<?php echo $row['stock']; ?>" required>

    <label>Change Image (optional)</label>
    <input type="file" name="image" class="form-control">

    </div>

    <div class="modal-footer">
    <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
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