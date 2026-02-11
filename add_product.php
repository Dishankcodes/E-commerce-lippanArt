<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM categories");

if (isset($_POST['add_product'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = $_POST['category_id'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $more_details = mysqli_real_escape_string($conn, $_POST['more_details']);
    $specifications = mysqli_real_escape_string($conn, $_POST['specifications']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Image Upload
    $image_name = $_FILES['image']['name'];
    $temp_name = $_FILES['image']['tmp_name'];
    $folder = "uploads/" . $image_name;

    move_uploaded_file($temp_name, $folder);

    $query = "INSERT INTO products 
              (category_id, name, description, more_details, specifications, price, stock, image) 
              VALUES 
              ('$category_id','$name','$description','$more_details','$specifications','$price','$stock','$image_name')";

    if (mysqli_query($conn, $query)) {
        $success = "Product Added Successfully!";
    } else {
        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="col-md-8 mx-auto">
            <div class="card shadow p-4">

                <h4 class="mb-4">Add New Product</h4>

                <?php
                if (isset($success))
                    echo "<p class='text-success'>$success</p>";
                if (isset($error))
                    echo "<p class='text-danger'>$error</p>";
                ?>

                <form method="POST" enctype="multipart/form-data">

                    <!-- Category -->
                    <select name="category_id" class="form-control mb-3" required>
                        <option value="">Select Category</option>
                        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <!-- Product Name -->
                    <input type="text" name="name" class="form-control mb-3" placeholder="Product Name" required>

                    <!-- Short Description -->
                    <label>Description</label>
                    <textarea name="description" class="form-control mb-3" rows="3"
                        placeholder="Short introduction about product..."></textarea>

                    <!-- More Details -->
                    <label>More Details</label>
                    <textarea name="more_details" class="form-control mb-3" rows="4"
                        placeholder="Explain uniqueness, inspiration, story behind design..."></textarea>

                    <!-- Specifications -->
                    <label>Specifications</label>
                    <textarea name="specifications" class="form-control mb-3" rows="4" placeholder="Example:
Size: 18 inch
Material: Mud & Mirror
Weight: 2kg
Installation: Wall Mount"></textarea>

                    <!-- Price -->
                    <input type="number" name="price" class="form-control mb-3" placeholder="Price" required>

                    <!-- Stock -->
                    <input type="number" name="stock" class="form-control mb-3" placeholder="Stock Quantity" required>

                    <!-- Image -->
                    <input type="file" name="image" class="form-control mb-3" required>

                    <button type="submit" name="add_product" class="btn btn-dark w-100">
                        Add Product
                    </button>

                </form>

            </div>
        </div>
    </div>

</body>

</html>