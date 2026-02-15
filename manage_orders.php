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

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --border-soft: rgba(255, 255, 255, .12);
            --green: #7dd87d;
            --yellow: #ffb347;
            --red: #ff6b6b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: Poppins, sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
        }

        a {
            color: inherit;
            text-decoration: none
        }

        header {
            background: rgba(15, 13, 11, .95);
            border-bottom: 1px solid var(--border-soft);
            padding: 22px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: Playfair Display, serif;
            font-size: 26px;
        }

        .back-btn {
            font-size: 13px;
            border: 1px solid var(--border-soft);
            padding: 8px 22px;
            border-radius: 30px;
            color: var(--text-muted);
            transition: .3s;
        }

        .back-btn:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .container {
            max-width: 1300px;
            margin: 60px auto;
            padding: 0 30px;
        }

        h2 {
            font-family: Playfair Display, serif;
            font-size: 36px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        .orders-panel {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
        }

        .order-row {
            display: grid;
            grid-template-columns: 90px 1.3fr 1.3fr 1fr 1fr 1.2fr 1.2fr;
            gap: 16px;
            padding: 18px;
            align-items: center;
            border-bottom: 1px solid var(--border-soft);
        }

        .order-row:hover {
            background: rgba(255, 255, 255, .03);
        }

        .muted {
            color: var(--text-muted);
            font-size: 12px
        }

        .status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .Pending {
            background: #ffb34722;
            color: var(--yellow)
        }

        .Processing {
            background: #c46a3b22;
            color: var(--accent)
        }

        .Shipped {
            background: #4da3ff22;
            color: #4da3ff
        }

        .Delivered {
            background: #7dd87d22;
            color: var(--green)
        }

        .Cancelled {
            background: #ff6b6b22;
            color: var(--red)
        }

        select {
            background: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            padding: 6px;
            font-size: 12px;
        }

        button {
            background: var(--accent);
            border: none;
            color: #fff;
            padding: 6px 14px;
            font-size: 12px;
            cursor: pointer;
        }

        button:hover {
            opacity: .85
        }

        .view-link {
            font-size: 12px;
            color: var(--accent);
        }

        .empty {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
        }

        @media(max-width:1100px) {
            .order-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">Auraloom <span style="font-size:12px;color:var(--accent)">Admin</span></div>
        <a href="dashboard.php" class="back-btn">← Dashboard</a>
    </header>

    <div class="container">

        <h2>Manage Orders</h2>

        <div class="orders-panel">

            <?php if (mysqli_num_rows($orders) == 0): ?>
                <div class="empty">No orders yet</div>
            <?php else: ?>

                <?php while ($row = mysqli_fetch_assoc($orders)): ?>
                    <div class="order-row">

                        <div>
                            <strong>#<?= $row['id'] ?></strong>
                            <div class="muted"><?= date("d M Y", strtotime($row['created_at'])) ?></div>
                        </div>

                        <div>
                            <?= htmlspecialchars($row['customer_name']) ?>
                            <div class="muted"><?= htmlspecialchars($row['customer_email']) ?></div>
                        </div>

                        <div class="muted">
                            📞 <?= htmlspecialchars($row['customer_phone']) ?>
                        </div>

                        <div>
                            ₹<?= number_format($row['final_amount'], 2) ?>
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

                        <form method="post" style="display:flex;gap:8px">
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