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
    <title>Manage Testimonials | Admin</title>

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

        /* ===== TABLE ===== */
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
        }

        th {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            text-align: left;
        }

        tr:hover {
            background: rgba(255, 255, 255, .02);
        }

        /* ===== STATUS BADGES ===== */
        .status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .approved {
            background: rgba(125, 216, 125, .15);
            color: var(--success);
        }

        .pending {
            background: rgba(255, 179, 71, .15);
            color: var(--warning);
        }

        /* ===== ACTION BUTTONS ===== */
        .actions button {
            padding: 6px 14px;
            font-size: 11px;
            letter-spacing: 1px;
            border: 1px solid var(--border-soft);
            background: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: .3s;
            margin-right: 6px;
        }

        .actions .approve:hover {
            background: rgba(125, 216, 125, .15);
            border-color: var(--success);
            color: var(--success);
        }

        .actions .reject:hover {
            background: rgba(255, 107, 107, .15);
            border-color: var(--danger);
            color: var(--danger);
        }

        /* ===== EMPTY ===== */
        .empty {
            color: var(--text-muted);
            font-size: 14px;
            padding: 30px 0;
        }

        /* ===== BACK BTN ===== */
        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 24px;
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            text-decoration: none;
            transition: .3s;
            font-size: 13px;
            letter-spacing: 1px;
        }

        .btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        /* ===== MOBILE ===== */
        @media(max-width:900px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                padding: 12px 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>💬 Homepage Testimonials</h2>

        <?php if (mysqli_num_rows($testimonials) == 0): ?>
            <div class="empty">No testimonials found.</div>
        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($testimonials)): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['customer_name']) ?></td>

                            <td><?= nl2br(htmlspecialchars($t['message'])) ?></td>

                            <td>
                                <span class="status <?= $t['approved'] ? 'approved' : 'pending' ?>">
                                    <?= $t['approved'] ? 'Approved' : 'Pending' ?>
                                </span>
                            </td>

                            <td><?= date("M d, Y", strtotime($t['created_at'])) ?></td>

                            <td class="actions">
                                <form method="post">
                                    <input type="hidden" name="testimonial_id" value="<?= $t['id'] ?>">
                                    <button class="approve" name="action" value="approve">Approve</button>
                                    <button class="reject" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php endif; ?>

        <a href="dashboard.php" class="btn">← Back to Dashboard</a>

    </div>

</body>

</html>