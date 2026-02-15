<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

/* ===== HANDLE APPROVE / REJECT ===== */
if (isset($_POST['testimonial_id'], $_POST['action'])) {

    $id = (int) $_POST['testimonial_id'];
    $action = $_POST['action'];

    $approved = ($action === 'approve') ? 1 : 0;

    mysqli_query($conn, "
    UPDATE testimonials
    SET approved = $approved
    WHERE id = $id
  ");
}

/* ===== FETCH TESTIMONIALS ===== */
$testimonials = mysqli_query($conn, "
  SELECT *
  FROM testimonials
  ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Testimonials | Auraloom</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* --- BRAND VARIABLES --- */
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
            
            /* Status Colors */
            --success: #7dd87d;
            --warning: #ffb347;
            --danger: #ff6b6b;
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

        /* --- HEADER --- */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        .btn-back {
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 8px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-back:hover {
            background-color: var(--accent);
            color: #fff;
        }

        /* --- TABLE STYLING --- */
        .table {
            background-color: var(--bg-soft) !important;
            border-color: var(--border-soft) !important;
            color: var(--text-muted) !important;
            margin-top: 10px !important;
            vertical-align: middle;
        }

        .table th {
            background-color: rgba(255,255,255,0.05) !important;
            color: var(--accent) !important;
            font-family: 'Playfair Display', serif !important;
            border-bottom: 1px solid var(--border-soft) !important;
            font-weight: normal !important;
            text-transform: uppercase !important;
            font-size: 13px !important;
            letter-spacing: 1px !important;
            padding: 18px 15px !important;
        }

        .table td {
            background-color: transparent !important;
            border-color: var(--border-soft) !important;
            color: var(--text-main) !important;
            padding: 18px 15px !important;
            vertical-align: top !important;
            font-size: 14px !important;
        }

        .table tr:hover td {
            background-color: rgba(255,255,255,0.02) !important;
        }

        /* --- STATUS BADGES --- */
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            display: inline-block;
        }

        .approved { 
            background: rgba(125, 216, 125, 0.15); 
            color: var(--success); 
            border: 1px solid var(--success);
        }

        .pending { 
            background: rgba(255, 179, 71, 0.15); 
            color: var(--warning); 
            border: 1px solid var(--warning);
        }

        /* --- ACTION BUTTONS --- */
        .actions-form {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: transparent;
            border: 1px solid;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-approve {
            border-color: var(--success);
            color: var(--success);
        }
        .btn-approve:hover {
            background: var(--success);
            color: #000;
        }

        .btn-reject {
            border-color: var(--danger);
            color: var(--danger);
        }
        .btn-reject:hover {
            background: var(--danger);
            color: #fff;
        }

        /* --- TEXT UTILS --- */
        .text-message {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-muted);
        }
        .customer-name {
            font-weight: 600;
            color: var(--text-main);
            font-size: 15px;
        }

    </style>
</head>

<body>

    <div class="container mt-5">

        <div class="header-flex">
            <h3>Homepage Testimonials</h3>
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>

        <?php if (mysqli_num_rows($testimonials) == 0): ?>
            <div class="text-center p-5" style="border: 1px solid var(--border-soft); color: var(--text-muted);">
                No testimonials found.
            </div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="20%">Customer</th>
                            <th width="40%">Message</th>
                            <th width="10%">Status</th>
                            <th width="15%">Date</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($t = mysqli_fetch_assoc($testimonials)): ?>
                            <tr>
                                <td>
                                    <span class="customer-name"><?= htmlspecialchars($t['customer_name']) ?></span>
                                </td>

                                <td>
                                    <div class="text-message">
                                        "<?= nl2br(htmlspecialchars($t['message'])) ?>"
                                    </div>
                                </td>

                                <td>
                                    <span class="status-badge <?= $t['approved'] ? 'approved' : 'pending' ?>">
                                        <?= $t['approved'] ? 'Approved' : 'Pending' ?>
                                    </span>
                                </td>

                                <td>
                                    <span style="font-size:12px; color:var(--text-muted);">
                                        <?= date("M d, Y", strtotime($t['created_at'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <form method="post" class="actions-form">
                                        <input type="hidden" name="testimonial_id" value="<?= $t['id'] ?>">
                                        
                                        <button class="btn-action btn-approve" name="action" value="approve" title="Approve">
                                            Approve
                                        </button>
                                        
                                        <button class="btn-action btn-reject" name="action" value="reject" title="Hide/Reject">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>