<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
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
    <title>Add Product | Auraloom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* --- BRAND VARIABLES (Dark Theme) --- */
        :root {
            --bg-dark: #0f0d0b;      /* Page Background */
            --bg-panel: #171411;     /* Card Background */
            --bg-input: #0b0a09;     /* Input Background */
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
        }

        /* --- GLOBAL --- */
        body {
            background-color: var(--bg-dark) !important;
            color: var(--text-main) !important;
            font-family: 'Poppins', sans-serif !important;
            padding-bottom: 50px;
        }

        /* --- CARD STYLING (Dark Panel) --- */
        .card {
            background-color: var(--bg-panel) !important;
            border: 1px solid var(--border-soft) !important;
            border-radius: 0 !important;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
        }

        /* --- TYPOGRAPHY --- */
        h4 {
            font-family: 'Playfair Display', serif !important;
            color: var(--text-main) !important;
            font-size: 28px !important;
            margin-bottom: 0 !important;
        }

        label {
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            color: var(--text-muted) !important;
            margin-bottom: 8px !important;
            margin-top: 20px !important;
            display: block;
        }

        /* --- DARK INPUTS (Matches Manage Page) --- */
        .form-control, .form-select {
            background-color: var(--bg-input) !important;
            border: 1px solid var(--border-soft) !important;
            color: var(--text-main) !important;
            border-radius: 0 !important;
            padding: 12px 15px !important;
            font-size: 14px !important;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none !important;
            border-color: var(--accent) !important;
            background-color: var(--bg-input) !important;
            color: var(--text-main) !important;
        }

        .form-control::placeholder {
            color: #555 !important;
            font-style: italic;
        }

        /* Customizing File Input */
        input[type="file"]::file-selector-button {
            background-color: #2a2622;
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            margin-right: 15px;
            padding: 5px 10px;
        }

        /* --- BUTTONS --- */
        .btn-brand {
            background-color: var(--accent) !important;
            color: #fff !important;
            border: none !important;
            padding: 14px !important;
            font-weight: 500 !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            margin-top: 30px !important;
            border-radius: 0 !important;
            transition: 0.3s !important;
            width: 100%;
            display: block;
        }

        .btn-brand:hover {
            background-color: var(--accent-hover) !important;
        }

        .btn-back {
            background: transparent;
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            padding: 6px 16px;
            font-size: 13px;
            text-decoration: none;
            border-radius: 30px;
            transition: 0.3s;
        }

        .btn-back:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* --- LAYOUT UTILS --- */
        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-soft);
        }

        .alert {
            background: rgba(196, 106, 59, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            border-radius: 0;
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">
        <div class="col-md-8 mx-auto">
            
            <div class="card p-4">

                <div class="card-header-flex">
                    <h4>Add New Product</h4>
                    <a href="manage_products.php" class="btn-back">← Back to List</a>
                </div>

                <?php
                if (isset($success))
                    echo "<div class='alert'>$success</div>";
                if (isset($error))
                    echo "<div class='alert' style='border-color:#d9534f; color:#d9534f;'>$error</div>";
                ?>

                <form method="POST" enctype="multipart/form-data">

                    <label>Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="" style="background:#171411;">Select Category</option>
                        <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                            <option value="<?php echo $row['id']; ?>" style="background:#171411;">
                                <?php echo $row['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Product Name" required>

                    <label>Short Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief introduction..."></textarea>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Price (₹)</label>
                            <input type="number" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label>Stock Quantity</label>
                            <input type="number" name="stock" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <label>In-Depth Details</label>
                    <textarea name="more_details" class="form-control" rows="4" placeholder="Story behind the art..."></textarea>

                    <label>Specifications</label>
                    <textarea name="specifications" class="form-control" rows="4" placeholder="Size: 18 inch&#10;Material: Mud & Mirror"></textarea>

                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control" required>

                    <button type="submit" name="add_product" class="btn-brand">
                        ADD PRODUCT
                    </button>

                </form>

            </div>
        </div>
    </div>

</body>

</html>