<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add_category'])){
    $category_name = $_POST['category_name'];

    $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    mysqli_query($conn, $query);

    $success = "Category Added Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="col-md-6 mx-auto">
        <div class="card p-4 shadow">
            <h4>Add Category</h4>

            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>

            <form method="POST">
                <input type="text" name="category_name" class="form-control mb-3" placeholder="Enter Category Name" required>
                <button type="submit" name="add_category" class="btn btn-dark">Add Category</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
