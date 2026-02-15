<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

/* ===== UPDATE ORDER STATUS ===== */
if (isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['order_status']);

    mysqli_query($conn, "
        UPDATE orders
        SET order_status = '$status'
        WHERE id = $order_id
    ");
}

/* ===== FETCH ORDERS ===== */
$orders = mysqli_query($conn, "
    SELECT *
    FROM orders
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Auraloom Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, .12);
            
            /* Status Colors from Old Page */
            --st-pending: #ffb347;
            --st-processing: #6cbcff;
            --st-shipped: #a56eff;
            --st-delivered: #7dd87d;
            --st-cancelled: #ff6b6b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; transition: 0.3s; }

        /* ================= HEADER (Consistent Glass-morphism) ================= */
        header {
            background: rgba(15, 13, 11, .85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-soft);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            letter-spacing: 1px;
        }

        .back-btn {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 8px 20px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: var(--accent);
            color: #fff;
        }

        /* ================= PAGE CONTENT ================= */
        .container {
            max-width: 1400px;
            margin: 60px auto;
            padding: 0 40px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        .orders-panel {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            overflow: hidden;
        }

        /* ================= GRID SYSTEM (From Old Page) ================= */
        .order-row {
            display: grid;
            grid-template-columns: 100px 1.5fr 1fr 1fr 1fr 1.5fr;
            gap: 20px;
            padding: 20px;
            align-items: center;
            border-bottom: 1px solid var(--border-soft);
            transition: 0.2s;
        }

        .order-row:hover {
            background: rgba(255, 255, 255, .02);
        }

        .order-row strong {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            display: block;
        }

        .muted {
            color: var(--text-muted);
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        /* ================= STATUS BADGES ================= */
        .status {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-align: center;
            min-width: 100px;
        }

        .Pending { color: var(--st-pending); background: rgba(255, 179, 71, 0.1); border: 1px solid var(--st-pending); }
        .Processing { color: var(--st-processing); background: rgba(108, 188, 255, 0.1); border: 1px solid var(--st-processing); }
        .Shipped { color: var(--st-shipped); background: rgba(165, 110, 255, 0.1); border: 1px solid var(--st-shipped); }
        .Delivered { color: var(--st-delivered); background: rgba(125, 216, 125, 0.1); border: 1px solid var(--st-delivered); }
        .Cancelled { color: var(--st-cancelled); background: rgba(255, 107, 107, 0.1); border: 1px solid var(--st-cancelled); }

        /* ================= FORM ELEMENTS ================= */
        select {
            background: var(--bg-dark);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            padding: 8px;
            font-size: 12px;
            border-radius: 4px;
            outline: none;
            flex-grow: 1;
        }

        button {
            background: var(--accent);
            border: none;
            color: #fff;
            padding: 8px 16px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            border-radius: 4px;
            transition: 0.3s;
        }

        button:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .view-link {
            font-size: 12px;
            color: var(--accent);
            text-decoration: underline;
            margin-top: 5px;
            display: inline-block;
        }

        .empty {
            padding: 60px;
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
        }

        /* RESPONSIVE */
        @media(max-width:1100px) {
            .order-row {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            header { padding: 20px; }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">Auraloom <span style="font-size:12px; color:var(--accent); text-transform: uppercase; letter-spacing: 2px; margin-left: 10px;">Admin Portal</span></div>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </header>

    <div class="container">

        <h2>Order Management</h2>

        <div class="orders-panel">

            <?php if (mysqli_num_rows($orders) == 0): ?>
                <div class="empty">No orders have been placed yet.</div>
            <?php else: ?>

                <?php while ($row = mysqli_fetch_assoc($orders)): ?>
                    <div class="order-row">

                        <div>
                            <strong>#<?= $row['id'] ?></strong>
                            <div class="muted"><?= date("d M Y", strtotime($row['created_at'])) ?></div>
                        </div>

                        <div>
                            <span style="font-weight: 500;"><?= htmlspecialchars($row['customer_name']) ?></span>
                            <div class="muted"><?= htmlspecialchars($row['customer_email']) ?></div>
                        </div>

                        <div class="muted">
                            <i class="fas fa-phone-alt" style="margin-right: 5px;"></i> <?= htmlspecialchars($row['customer_phone']) ?>
                        </div>

                        <div>
                            <span style="color: var(--accent); font-weight: 600;">₹<?= number_format($row['final_amount'], 2) ?></span>
                            <div>
                                <a href="manage-order-details.php?id=<?= $row['id'] ?>" class="view-link">
                                    View Items →
                                </a>
                            </div>
                        </div>

                        <div>
                            <span class="status <?= $row['order_status'] ?>">
                                <?= $row['order_status'] ?>
                            </span>
                        </div>

                        <form method="post" style="display:flex; gap:8px; align-items: center;">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                            <select name="order_status">
                                <?php
                                $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                foreach ($statuses as $s) {
                                    echo "<option value='$s' " . ($row['order_status'] == $s ? 'selected' : '') . ">$s</option>";
                                }
                                ?>
                            </select>

                            <button name="update_status">Save</button>
                        </form>

                    </div>
                <?php endwhile; ?>

            <?php endif; ?>

        </div>

    </div>

</body>
</html>