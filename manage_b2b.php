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
<html lang="en">

<head>
    <title>Manage B2B Enquiries | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
            --st-new: #ffb347;       
            --st-contacted: #6cbcff; 
            --st-quoted: #a56eff;    
            --st-completed: #7dd87d; 
            --whatsapp: #25d366;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
        }

        .container {
            max-width: 1300px;
            margin: 60px auto;
            padding: 0 40px;
        }

        /* --- HEADER FLEX (Old Style) --- */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-soft);
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin: 0;
        }

        .btn-dash {
            padding: 10px 24px;
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .btn-dash:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(196, 106, 59, 0.05);
        }

        /* --- TABLE STYLING --- */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
        }

        th {
            background-color: rgba(255,255,255,0.05);
            color: var(--accent);
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: left;
            padding: 18px 15px;
            border-bottom: 1px solid var(--border-soft);
        }

        td {
            padding: 18px 15px;
            border-bottom: 1px solid var(--border-soft);
            vertical-align: top;
            font-size: 14px;
        }

        tr:hover td { background-color: rgba(255,255,255,0.02); }

        /* --- STATUS BADGES --- */
        .badge {
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            width: 100px;
        }
        .new { color: var(--st-new); background: rgba(255, 179, 71, 0.15); border: 1px solid var(--st-new); }
        .contacted { color: var(--st-contacted); background: rgba(108, 188, 255, 0.15); border: 1px solid var(--st-contacted); }
        .quoted { color: var(--st-quoted); background: rgba(165, 110, 255, 0.15); border: 1px solid var(--st-quoted); }
        .completed { color: var(--st-completed); background: rgba(125, 216, 125, 0.15); border: 1px solid var(--st-completed); }

        /* --- ACTION WRAPPERS --- */
        .status-form { display: flex; gap: 5px; margin-bottom: 10px; }

        select {
            background: var(--bg-dark);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
            padding: 6px;
            font-size: 12px;
            border-radius: 4px;
            outline: none;
            flex-grow: 1;
        }

        .btn-save {
            padding: 6px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            background: var(--accent);
            color: #fff;
            border-radius: 4px;
            transition: 0.3s;
        }
        .btn-save:hover { background: var(--accent-hover); }

        .btn-whatsapp {
            background: transparent;
            color: var(--whatsapp);
            border: 1px solid var(--whatsapp);
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 6px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 500;
            border-radius: 4px;
        }
        .btn-whatsapp:hover { background: var(--whatsapp); color: #000; }

        .muted { color: var(--text-muted); font-size: 12px; display: block; margin-top: 4px; }
        img.ref { width: 60px; height: 60px; object-fit: cover; border: 1px solid var(--border-soft); border-radius: 4px; }
    </style>
</head>

<body>

    <div class="container">
        
        <div class="header-flex">
            <h2>B2B Business Enquiries</h2>
            <a href="dashboard.php" class="btn-dash">Back to Dashboard</a>
        </div>

        <?php if (mysqli_num_rows($b2b) == 0): ?>
            <p style="color:var(--text-muted); text-align: center; padding: 50px; border: 1px solid var(--border-soft);">No B2B enquiries yet.</p>
        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Business</th>
                        <th width="20%">Contact</th>
                        <th width="25%">Details</th>
                        <th width="10%">Reference</th>
                        <th width="10%">Status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($b2b)): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['business_name']) ?></strong>
                                <span class="muted"><?= htmlspecialchars($row['business_type']) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['email']) ?>
                                <span class="muted"><?= htmlspecialchars($row['phone']) ?></span>
                            </td>
                            <td>
                                Qty: <strong style="color:var(--accent)"><?= $row['quantity'] ?></strong>
                                <div class="muted" style="margin-top:8px;">
                                    <?= nl2br(htmlspecialchars($row['message'])) ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($row['reference_image'])): ?>
                                    <a href="uploads/b2b/<?= $row['reference_image'] ?>" target="_blank">
                                        <img src="uploads/b2b/<?= $row['reference_image'] ?>" class="ref">
                                    </a>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= strtolower($row['status']) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" class="status-form">
                                    <input type="hidden" name="b2b_id" value="<?= $row['id'] ?>">
                                    <select name="status">
                                        <option value="new" <?= $row['status'] == 'new' ? 'selected' : '' ?>>New</option>
                                        <option value="contacted" <?= $row['status'] == 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                        <option value="quoted" <?= $row['status'] == 'quoted' ? 'selected' : '' ?>>Quoted</option>
                                        <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                    <button class="btn-save">Save</button>
                                </form>
                                <a class="btn-whatsapp" target="_blank" href="https://wa.me/91<?= $row['phone'] ?>?text=Hello, this is Auraloom regarding your B2B enquiry #<?= $row['id'] ?>">
                                   WhatsApp
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>