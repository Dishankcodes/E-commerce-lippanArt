<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH CHECK ===== */
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

$customers = mysqli_query(
    $conn,
    "SELECT id, name, email, created_at 
     FROM customers 
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Customers | Auraloom Admin</title>

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
            --danger: #ff6b6b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
        }

        a {
            text-decoration: none;
            color: inherit;
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
            font-family: 'Playfair Display', serif;
            font-size: 26px;
        }

        .back-btn {
            font-size: 13px;
            border: 1px solid var(--border-soft);
            padding: 8px 20px;
            border-radius: 30px;
            color: var(--text-muted);
            transition: .3s;
        }

        .back-btn:hover {
            border-color: var(--accent);
            color: var(--text-main);
            background: var(--accent);
        }

        .container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 30px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        .data-panel {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            padding: 30px;
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: rgba(255, 255, 255, .02);
            border-left: 2px solid transparent;
            transition: .3s;
        }

        .data-item:hover {
            background: rgba(255, 255, 255, .04);
            border-left-color: var(--accent);
        }

        .data-primary {
            font-size: 15px;
            display: block;
        }

        .data-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            display: block;
        }

        .action-btn {
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255, 107, 107, .4);
            color: var(--danger);
            transition: .3s;
        }

        .action-btn:hover {
            background: var(--danger);
            color: #fff;
            border-color: var(--danger);
        }

        .empty {
            color: var(--text-muted);
            font-size: 14px;
            text-align: center;
            padding: 40px 0;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">Auraloom <span style="font-size:12px;color:var(--accent)">Admin</span></div>
        <a href="dashboard.php" class="back-btn">← Dashboard</a>
    </header>

    <div class="container">

        <h2>Manage Customers</h2>

        <div class="data-panel">

            <?php if (mysqli_num_rows($customers) == 0): ?>
                <div class="empty">No customers found</div>
            <?php else: ?>

                <?php while ($row = mysqli_fetch_assoc($customers)): ?>
                    <div class="data-item">

                        <div>
                            <span class="data-primary">
                                <?= htmlspecialchars($row['name']) ?>
                            </span>
                            <span class="data-sub">
                                <?= htmlspecialchars($row['email']) ?> •
                                Joined <?= $row['created_at'] ? date("M d, Y", strtotime($row['created_at'])) : '—' ?>
                            </span>
                        </div>

                        <a href="delete_customer.php?id=<?= $row['id'] ?>" class="action-btn"
                            onclick="return confirm('Delete this customer permanently?');">
                            Delete
                        </a>

                    </div>
                <?php endwhile; ?>

            <?php endif; ?>

        </div>

    </div>

</body>

</html>