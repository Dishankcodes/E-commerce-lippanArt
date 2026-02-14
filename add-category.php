<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['add_category'])){
    $category_name = $_POST['category_name'];

    // Basic validation to prevent empty categories
    if(!empty($category_name)){
        $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        mysqli_query($conn, $query);
        $success = "Category Added Successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category | Auraloom</title>
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

        /* --- CARD STYLING --- */
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
            display: block;
        }

        /* --- INPUTS --- */
        .form-control {
            background-color: var(--bg-input) !important;
            border: 1px solid var(--border-soft) !important;
            color: var(--text-main) !important;
            border-radius: 0 !important;
            padding: 12px 15px !important;
            font-size: 14px !important;
        }

        .form-control:focus {
            box-shadow: none !important;
            border-color: var(--accent) !important;
            background-color: var(--bg-input) !important;
            color: var(--text-main) !important;
        }

        .form-control::placeholder {
            color: #555 !important;
            font-style: italic;
        }

        /* --- BUTTONS --- */
        .btn-brand {
            background-color: var(--accent) !important;
            color: #fff !important;
            border: none !important;
            padding: 12px !important;
            font-weight: 500 !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            border-radius: 0 !important;
            transition: 0.3s !important;
            width: 100%;
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
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-soft);
        }

        /* --- ALERTS --- */
        .alert-custom {
            background: rgba(196, 106, 59, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="col-md-6 mx-auto">
        
        <div class="card p-4 shadow">
            
            <div class="card-header-flex">
                <h4>Add Category</h4>
                <a href="manage_categories.php" class="btn-back">← Back to List</a>
            </div>

            <?php if(isset($success)) echo "<div class='alert-custom'>$success</div>"; ?>

            <form method="POST">
                
                <label>Category Name</label>
                <input type="text" name="category_name" class="form-control mb-4" placeholder="e.g. Wall Decor" required>
                
                <button type="submit" name="add_category" class="btn-brand">ADD CATEGORY</button>
            
            </form>
        </div>

    </div>
</div>

</body>
</html>