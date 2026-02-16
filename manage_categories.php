<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

/* =============================
   HANDLE CATEGORY ACTIONS
============================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Toggle Status */
    if (isset($_POST['toggle_status'])) {
        $id = (int) $_POST['id'];
        $newStatus = $_POST['new_status'] === 'active' ? 'active' : 'inactive';

        mysqli_query($conn, "
            UPDATE categories 
            SET status = '$newStatus'
            WHERE id = $id
        ");
    }

    /* Edit Category Name */
    if (isset($_POST['edit_category'])) {
        $id = (int) $_POST['id'];
        $name = mysqli_real_escape_string($conn, $_POST['category_name']);

        mysqli_query($conn, "
            UPDATE categories
            SET category_name = '$name'
            WHERE id = $id
        ");
    }
}

/* Reload updated list */
$result = mysqli_query($conn, "SELECT * FROM categories");

$result = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Categories | Auraloom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">

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
           
        }

        .table th {
            background-color: rgba(255, 255, 255, 0.05) !important;
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
            background-color: rgba(255, 255, 255, 0.02) !important;
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

        /* Theme Inputs */
        .theme-input {
            background-color: #111 !important;
            color: #f3ede7 !important;
            border: 1px solid var(--border-soft) !important;
        }

        /* Buttons */
        .btn-theme-edit {
            border: 1px solid var(--accent);
            color: var(--accent);
            background: transparent;
        }

        .btn-theme-edit:hover {
            background: var(--accent);
            color: #fff;
        }

        .btn-theme-save {
            background: var(--accent);
            border: 1px solid var(--accent);
            color: #fff;
        }

        .btn-theme-save:hover {
            background: var(--accent-hover);
        }

        .btn-theme-cancel {
            border: 1px solid #666;
            color: #aaa;
            background: transparent;
        }

        .btn-theme-cancel:hover {
            background: #333;
        }

        /* THEME STATUS BADGES */
        .badge-theme-active {
            background: rgba(196, 106, 59, 0.15);
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .badge-theme-disabled {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        #categorySearch {
            margin-bottom: 20px;
        }

        .search-soft {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #f3ede7 !important;
        }

        .search-soft::placeholder {
            color: #b9afa6 !important;
        }

        .search-soft:focus {
            background-color: rgba(255, 255, 255, 0.12) !important;
            border-color: var(--accent) !important;
            box-shadow: none !important;
        }

        .search-icon {
            position: absolute;
            left: 5px;
            top: 30%;
            transform: translateY(-50%);
            color: #b9afa6;
            font-size: 12px;
            pointer-events: none;
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
        <div class="d-flex justify-content-end mb-3">
            <div class="position-relative">
                <input type="text" id="categorySearch" class="form-control form-control-sm search-soft ps-4"
                    placeholder="Search categories…" onkeyup="filterCategories()" style="width: 300px;">
                <span class="search-icon">🔍</span>
            </div>
        </div>


        <table class="table table-bordered">
            <tr>
                <th width="10%">ID</th>
                <th width="60%">Name</th>
                <th width="20%">Status</th>
                <th width="20%">Actions</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="category-row" data-name="<?php echo strtolower($row['category_name']); ?>">

                    <td><?php echo $row['id']; ?></td>


                    <!-- NAME -->
                    <td>
                        <!-- View mode -->
                        <span class="cat-text" id="text-<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </span>

                        <!-- Edit mode -->
                        <form method="post" class="cat-edit d-none" id="edit-<?php echo $row['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                            <div class="d-flex gap-2">
                                <input type="text" name="category_name"
                                    value="<?php echo htmlspecialchars($row['category_name']); ?>"
                                    class="form-control form-control-sm theme-input" required>

                                <button type="submit" name="edit_category" class="btn btn-sm btn-theme-save">
                                    Save
                                </button>

                                <button type="button" class="btn btn-sm btn-theme-cancel"
                                    onclick="cancelEdit(<?php echo $row['id']; ?>)">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </td>

                    <!-- STATUS -->
                    <td>
                        <?php if ($row['status'] === 'active') { ?>
                            <span class="badge badge-theme-active">Active</span>
                        <?php } else { ?>
                            <span class="badge badge-theme-disabled">Disabled</span>
                        <?php } ?>

                    </td>

                    <!-- ACTIONS -->
                    <td class="d-flex gap-2">

                        <!-- Edit button -->
                        <button class="btn btn-sm btn-theme-edit" onclick="enableEdit(<?php echo $row['id']; ?>)">
                            Edit
                        </button>

                        <!-- Toggle status -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="new_status"
                                value="<?php echo $row['status'] === 'active' ? 'inactive' : 'active'; ?>">

                            <button type="submit" name="toggle_status" class="btn btn-sm
                    <?php echo $row['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                <?php echo $row['status'] === 'active' ? 'Disable' : 'Enable'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>

        </table>
    </div>
    <script>
        function enableEdit(id) {
            document.getElementById('text-' + id).classList.add('d-none');
            document.getElementById('edit-' + id).classList.remove('d-none');
        }

        function cancelEdit(id) {
            document.getElementById('edit-' + id).classList.add('d-none');
            document.getElementById('text-' + id).classList.remove('d-none');
        }
    </script>
    <script>
        function filterCategories() {
            const query = document
                .getElementById('categorySearch')
                .value
                .toLowerCase();

            const rows = document.querySelectorAll('.category-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                row.style.display = name.includes(query) ? '' : 'none';
            });
        }
    </script>




</body>

</html>