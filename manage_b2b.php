<?php
session_start();
include("db.php");

/* ===== ADMIN AUTH ===== */
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

/* ===== UPDATE STATUS ===== */
if (isset($_POST['b2b_id'], $_POST['status'])) {
    $id = (int) $_POST['b2b_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "
        UPDATE b2b_enquiries
        SET status = '$status'
        WHERE id = $id
    ");
}

/* ===== FETCH ENQUIRIES ===== */
$b2b = mysqli_query($conn, "
    SELECT *
    FROM b2b_enquiries
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage B2B Enquiries | Admin</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap"
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
            --success: #7dd87d;
            --warning: #ffb347;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
        }

        th,
        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-soft);
            vertical-align: top;
            font-size: 14px;
        }

        th {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            text-align: left;
        }

        .badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .new {
            background: rgba(255, 179, 71, .15);
            color: var(--warning);
        }

        .contacted {
            background: rgba(196, 106, 59, .2);
            color: var(--accent);
        }

        .quoted {
            background: rgba(125, 216, 125, .15);
            color: var(--success);
        }

        .completed {
            background: rgba(125, 216, 125, .3);
            color: var(--success);
        }

        .btn {
            padding: 6px 14px;
            font-size: 12px;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            background: var(--accent);
            color: #fff;
        }

        .btn.whatsapp {
            background: #25D366;
            margin-top: 6px;
            display: inline-block;
            text-decoration: none;
        }

        select {
            background: #1b1815;
            color: #f3ede7;
            border: 1px solid var(--border-soft);
            padding: 6px;
            font-size: 12px;
        }

        img.ref {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid var(--border-soft);
        }

        .back {
            margin-top: 30px;
            display: inline-block;
            text-decoration: none;
            color: var(--text-muted);
        }

        .back:hover {
            color: var(--accent);
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>B2B Enquiries</h2>

        <?php if (mysqli_num_rows($b2b) == 0): ?>
            <p style="color:var(--text-muted)">No B2B enquiries yet.</p>
        <?php else: ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Business</th>
                    <th>Contact</th>
                    <th>Details</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($b2b)): ?>

                    <tr>
                        <td>#<?= $row['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($row['business_name']) ?></strong><br>
                            <span class="muted"><?= htmlspecialchars($row['business_type']) ?></span>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['email']) ?><br>
                            <?= htmlspecialchars($row['phone']) ?>
                        </td>

                        <td>
                            Qty: <strong><?= $row['quantity'] ?></strong><br>
                            <span class="muted"><?= nl2br(htmlspecialchars($row['message'])) ?></span>
                        </td>

                        <td>
                            <?php if (!empty($row['reference_image'])): ?>
                                <img src="uploads/b2b/<?= $row['reference_image'] ?>" class="ref">
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>

                        <td>
                            <form method="post">
                                <input type="hidden" name="b2b_id" value="<?= $row['id'] ?>">

                                <select name="status">
                                    <option value="new" <?= $row['status'] == 'new' ? 'selected' : '' ?>>New</option>
                                    <option value="contacted" <?= $row['status'] == 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                    <option value="quoted" <?= $row['status'] == 'quoted' ? 'selected' : '' ?>>Quoted</option>
                                    <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>

                                <button class="btn">Save</button>
                            </form>

                            <a class="btn whatsapp"
                                href="https://wa.me/91<?= $row['phone'] ?>?text=Hello, this is Auraloom regarding your B2B enquiry #<?= $row['id'] ?>"
                                target="_blank">
                                WhatsApp
                            </a>
                        </td>
                    </tr>

                <?php endwhile; ?>
            </table>

        <?php endif; ?>

        <a href="dashboard.php" class="back">← Back to Dashboard</a>

    </div>

</body>

</html>