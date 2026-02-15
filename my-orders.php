<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get customer email
$user = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT email FROM customers WHERE id='$user_id'")
);

$email = $user['email'];

// Fetch orders
$orders = mysqli_query($conn, "
  SELECT * FROM orders 
  WHERE customer_email='$email'
  ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Auraloom</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        /* ================= BRAND VARIABLES ================= */
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;         /* Signature Rust */
            --accent-hover: #a85830;
            --border-soft: rgba(255, 255, 255, 0.12);
        }

        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
        }

        a { text-decoration: none; transition: 0.3s ease; }

        /* ================= HEADER (Consistent Center Nav) ================= */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            height: 80px;
            z-index: 1000;
            background: rgba(15, 13, 11, 0.75);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-soft);
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            padding: 0 80px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            letter-spacing: 2px;
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 35px;
        }

        nav a {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            position: relative;
            padding-bottom: 5px;
        }

        nav a:hover { color: var(--text-main); }
        nav a::after {
            content: "";
            position: absolute;
            left: 0; bottom: 0;
            width: 0%; height: 1px;
            background: var(--accent);
            transition: 0.4s ease;
        }
        nav a:hover::after { width: 100%; }

        .header-btn {
            padding: 10px 22px;
            background: var(--accent);
            color: #fff;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-btn:hover { background: var(--accent-hover); }

        /* ================= MAIN CONTENT AREA ================= */
        .page-wrap {
            padding-top: 150px;
            padding-bottom: 100px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 30px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            margin-bottom: 40px;
            font-weight: 500;
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 15px;
        }

        /* ================= ORDER CARD ================= */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            padding: 30px;
            margin-bottom: 25px;
            transition: 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .order-info strong {
            font-size: 18px;
            font-family: 'Playfair Display', serif;
            color: var(--text-main);
            letter-spacing: 0.5px;
        }

        .order-info p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .status-badge {
            color: var(--accent);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        /* ================= SQUARE BUTTON ================= */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--accent);
            color: #fff;
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .empty-msg {
            text-align: center;
            padding: 100px 0;
            color: var(--text-muted);
            font-style: italic;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            header { padding: 0 30px; }
            nav { display: none; }
            .card { flex-direction: column; align-items: flex-start; gap: 20px; }
            .btn { width: 100%; text-align: center; }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">AURALOOM</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="collection.php">Shop</a>
            <a href="custom-order.php">Custom</a>
            <a href="b2b.php">B2B</a>
            <a href="about-us.php">About</a>
        </nav>
        <a href="logout.php" class="header-btn">Logout</a>
    </header>

    <div class="page-wrap">
        <div class="container">
            <h1>My Order History</h1>

            <?php if (mysqli_num_rows($orders) == 0): ?>
                <div class="empty-msg">
                    <p>You haven’t placed any orders yet.</p>
                    <a href="collection.php" style="color: var(--accent); margin-top: 15px; display: block;">Browse Collections →</a>
                </div>
            <?php endif; ?>

            <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                <div class="card">
                    <div class="order-info">
                        <p class="status-badge"><?= strtoupper($o['order_status'] ?? 'Processing') ?></p>
                        <strong>Invoice #AUR-<?= $o['id'] ?></strong>
                        <p>Placed on <?= date("d M Y", strtotime($o['created_at'])) ?></p>
                        <p>Total Paid: <span style="color: var(--accent);">₹<?= number_format($o['final_amount'], 2) ?></span></p>
                    </div>

                    <a href="track-order.php?id=<?= $o['id'] ?>" class="btn">View / Track</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>