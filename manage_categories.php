<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_email'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories | Auraloom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* --- VARIABLES --- */
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
        }

        /* --- GLOBAL OVERRIDES --- */
        body {
            background-color: var(--bg-dark) !important;
            color: var(--text-main) !important;
            font-family: 'Poppins', sans-serif !important;
        }

        /* --- TYPOGRAPHY --- */
        h3 {
            font-family: 'Playfair Display', serif !important;
            color: var(--text-main) !important;
            margin: 0 !important;
        }

        /* --- TABLE STYLING --- */
        .table {
            background-color: var(--bg-soft) !important;
            border-color: var(--border-soft) !important;
            color: var(--text-muted) !important;
            margin-top: 30px !important;
        }

        .table th {
            background-color: rgba(255,255,255,0.05) !important;
            color: var(--accent) !important;
            font-family: 'Playfair Display', serif !important;
            border-bottom: 1px solid var(--border-soft) !important;
            font-weight: normal !important;
            text-transform: uppercase !important;
            font-size: 14px !important;
            letter-spacing: 1px !important;
            padding: 15px !important;
        }

        .table td {
            background-color: transparent !important;
            border-color: var(--border-soft) !important;
            color: var(--text-main) !important;
            padding: 15px !important;
            vertical-align: middle !important;
        }

        .table tr:hover td {
            background-color: rgba(255,255,255,0.02) !important;
        }

        /* --- BUTTONS --- */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

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
            font-family: 'Poppins', sans-serif;
            display: inline-block;
        }

        .btn-back:hover {
            background-color: var(--accent);
            color: #fff;
        }

        /* New Add Button Style */
        .btn-add {
            background-color: var(--accent);
            color: #fff;
            padding: 8px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            display: inline-block;
            border: 1px solid var(--accent);
        }

        .btn-add:hover {
            background-color: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        /* Optional: Status Badge Styling */
        td:last-child {
            text-transform: capitalize;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    
    <div class="header-flex">
        <h3>All Categories</h3>
        
        <div class="header-actions">
            <a href="add-category.php" class="btn-add">+ Add New Category</a>
            
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>
    </div>

    <table class="table table-bordered">
        <tr>
            <th width="10%">ID</th>
            <th width="60%">Name</th>
            <th width="30%">Status</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['category_name']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>