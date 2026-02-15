<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

/* ===== HANDLE APPROVE / REJECT ===== */
if (isset($_POST['review_id'], $_POST['action'])) {
    $review_id = (int) $_POST['review_id'];
    $action = $_POST['action'];

    $status = match ($action) {
        'approve' => 'approved',
        'reject' => 'rejected',
        default => 'pending'
    };

    mysqli_query($conn, "
        UPDATE product_reviews
        SET status = '$status'
        WHERE id = $review_id
    ");
}

/* ===== FETCH PENDING REVIEWS ===== */
$reviews = mysqli_query($conn, "
  SELECT 
    r.id,
    r.rating,
    r.review_text,
    r.created_at,
    p.name AS product_name,
    c.name AS customer_name
  FROM product_reviews r
  JOIN products p ON r.product_id = p.id
  JOIN customers c ON r.user_id = c.id
  WHERE r.status = 'pending'
  ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Reviews | Auraloom Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
            --success: #7dd87d;
            --danger: #ff6b6b;
            --star: #ffb347;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark) !important;
            color: var(--text-main) !important;
            line-height: 1.6;
        }

        /* --- HEADER (Restored Flex Layout) --- */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        h3 {
            font-family: 'Playfair Display', serif !important;
            color: var(--text-main) !important;
            margin: 0 !important;
            font-size: 32px !important;
        }

        .btn-dash {
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 8px 22px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-dash:hover {
            background-color: var(--accent);
            color: #fff;
            text-decoration: none;
        }

        /* --- TABLE STYLING --- */
        .table {
            background-color: var(--bg-soft) !important;
            border-color: var(--border-soft) !important;
            color: var(--text-muted) !important;
            margin-top: 10px !important;
        }

        .table th {
            background-color: rgba(255,255,255,0.05) !important;
            color: var(--accent) !important;
            font-family: 'Playfair Display', serif !important;
            border-bottom: 1px solid var(--border-soft) !important;
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

        /* --- STARS --- */
        .stars {
            color: var(--star);
            letter-spacing: 2px;
            font-size: 16px;
        }

        /* --- ACTION BUTTONS (Refined Borders) --- */
        .actions-form {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: transparent;
            border: 1px solid var(--border-soft);
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .btn-approve {
            color: var(--success);
            border-color: var(--success);
        }
        .btn-approve:hover {
            background: rgba(125, 216, 125, 0.15);
            color: var(--success);
        }

        .btn-reject {
            color: var(--danger);
            border-color: var(--danger);
        }
        .btn-reject:hover {
            background: rgba(255, 107, 107, 0.15);
            color: var(--danger);
        }

        /* --- TEXT UTILS --- */
        .product-title {
            font-weight: 600;
            color: var(--text-main);
            display: block;
            margin-bottom: 4px;
        }
        .review-text {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 13px;
            font-style: italic;
        }
        .date-text {
            font-size: 12px;
            color: var(--text-muted);
            opacity: 0.7;
        }

        .empty {
            color: var(--text-muted);
            font-size: 16px;
            text-align: center;
            padding: 60px;
            border: 1px solid var(--border-soft);
        }

        @media(max-width:900px){
            .table-responsive { border: none; }
            .header-flex { flex-direction: column; gap: 20px; align-items: flex-start; }
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">

        <div class="header-flex">
            <h3>📝 Pending Product Reviews</h3>
            <a href="dashboard.php" class="btn-dash">← Back to Dashboard</a>
        </div>

        <?php if (mysqli_num_rows($reviews) === 0): ?>
            <div class="empty">No pending reviews found. 🎉</div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="20%">Product</th>
                            <th width="15%">Customer</th>
                            <th width="15%">Rating</th>
                            <th width="30%">Review Content</th>
                            <th width="10%">Date</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
                            <tr>
                                <td>
                                    <span class="product-title"><?= htmlspecialchars($r['product_name']) ?></span>
                                    <span class="date-text">ID: #<?= $r['id'] ?></span>
                                </td>
                                
                                <td><?= htmlspecialchars($r['customer_name']) ?></td>

                                <td>
                                    <div class="stars">
                                        <?= str_repeat("★", (int)$r['rating']) ?>
                                    </div>
                                    <span style="font-size:11px; color:var(--text-muted)">(<?= $r['rating'] ?>/5)</span>
                                </td>

                                <td>
                                    <div class="review-text">
                                        "<?= nl2br(htmlspecialchars($r['review_text'])) ?>"
                                    </div>
                                </td>

                                <td>
                                    <span class="date-text">
                                        <?= date("d M Y", strtotime($r['created_at'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <form method="post" class="actions-form">
                                        <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn-action btn-approve" name="action" value="approve">Approve</button>
                                        <button type="submit" class="btn-action btn-reject" name="action" value="reject">Reject</button>
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