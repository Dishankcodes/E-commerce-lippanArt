<?php
session_start();
include("db.php");

// --- Security Check ---
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

// --- Data Fetching ---
$customer_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))['total'];
$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$category_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];

$recent_customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY id DESC LIMIT 5");
$recent_products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 5");

// Total Custom Orders
$custom_order_count = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM custom_orders")
)['total'];

// Recent Custom Orders
$recent_custom_orders = mysqli_query(
    $conn,
    "SELECT * FROM custom_orders ORDER BY id DESC LIMIT 5"
);

$items_sold = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(quantity),0) AS total FROM order_items"
    )
)['total'];


$recent_reviews = mysqli_query(
    $conn,
    "SELECT 
        r.id,
        r.rating,
        r.status,
        r.created_at,
        p.name AS product_name,
        c.name AS customer_name
     FROM product_reviews r
     JOIN products p ON r.product_id = p.id
     JOIN customers c ON r.user_id = c.id
     ORDER BY r.created_at DESC
     LIMIT 5"
);
// Total B2B Enquiries
$b2b_count = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM b2b_enquiries")
)['total'];

// Recent B2B Enquiries
$recent_b2b = mysqli_query(
    $conn,
    "SELECT * FROM b2b_enquiries ORDER BY id DESC LIMIT 5"
);

$recent_testimonials = mysqli_query(
    $conn,
    "SELECT 
        id,
        customer_name,
        message,
        approved,
        created_at
     FROM testimonials
     ORDER BY created_at DESC
     LIMIT 5"
);
// Total Revenue (exclude cancelled)
$revenue = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(final_amount),0) AS total 
         FROM orders 
         WHERE order_status != 'Cancelled'"
    )
)['total'];
// Top Selling Products
$top_selling = mysqli_query(
    $conn,
    "SELECT 
        p.id,
        p.name,
        SUM(oi.quantity) AS sold_qty
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     GROUP BY oi.product_id
     ORDER BY sold_qty DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Auraloom</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --border-soft: rgba(255, 255, 255, 0.12);
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            display: flex;
        }

        a { text-decoration: none; color: inherit; transition: 0.3s; }

        /* --- SIDEBAR NAVIGATION (Restored) --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-soft);
            border-right: 1px solid var(--border-soft);
            position: fixed;
            left: 0; top: 0;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 0 30px 40px;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            letter-spacing: 2px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-soft);
            margin-bottom: 20px;
        }

        .sidebar-menu {
            list-style: none;
            overflow-y: auto;
            flex-grow: 1;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 14px 30px;
            font-size: 14px;
            color: var(--text-muted);
            transition: 0.3s;
        }

        .sidebar-menu li a i {
            width: 25px;
            font-size: 16px;
            margin-right: 15px;
            color: var(--accent);
        }

        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: rgba(196, 106, 59, 0.1);
            color: var(--text-main);
            border-left: 4px solid var(--accent);
        }

        .sidebar-footer {
            padding: 20px 30px;
            border-top: 1px solid var(--border-soft);
        }

        /* --- MAIN CONTENT AREA --- */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
        }

        header {
            background: rgba(15, 13, 11, 0.95);
            border-bottom: 1px solid var(--border-soft);
            padding: 22px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .admin-badge {
            font-size: 11px;
            background: var(--accent);
            color: #fff;
            padding: 3px 8px;
            border-radius: 2px;
            margin-left: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .logout-btn {
            font-size: 13px;
            border: 1px solid var(--accent);
            padding: 8px 24px;
            color: var(--text-muted);
            border-radius: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
           
        }

        .logout-btn:hover { background: #ebd6c0; color: #fff; border-color: var(--accent); }

        /* --- CONTAINER & STAT CARDS --- */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 40px;
        }

        h2.page-title {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            margin-bottom: 40px;
            color: var(--text-main);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            margin: 50px 0 25px;
            color: var(--text-main);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            padding: 25px;
            transition: 0.3s;
        }

        .stat-card:hover { border-color: var(--accent); transform: translateY(-5px); }

        .stat-card h5 { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 10px; opacity: 0.75; }

        .stat-card h3 { font-family: 'Playfair Display', serif; font-size: 36px; color: var(--accent); }

        /* KPI Emphasis for Revenue & Orders */
        .stat-card:nth-child(4), .stat-card:nth-child(6) {
            background: linear-gradient(135deg, rgba(196, 106, 59, .15), rgba(27, 24, 21, .95));
            border-color: rgba(196, 106, 59, .5);
        }

        /* --- DATA PANELS --- */
        .content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

        .data-panel { 
            background: var(--bg-soft); 
            border: 1px solid var(--border-soft); 
            padding: 30px; 
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .02), rgba(0, 0, 0, .05));
        }

        .data-panel h4 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px; }

        .data-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; background: rgba(255,255,255,0.02); margin-bottom: 10px; border-radius: 8px; border-left: 2px solid transparent; }

        .data-item:hover { background: rgba(255, 255, 255, 0.04); border-left-color: var(--accent); }

        .data-primary { font-size: 14px; color: var(--text-main); display: block; }

        .data-sub { font-size: 12px; color: var(--text-muted); }

        .price-tag { color: var(--accent); font-family: 'Playfair Display', serif; font-size: 16px; }

        /* Highlights */
        .data-panel:nth-child(3) .data-item { border-left-width: 3px; border-left-color: var(--accent); }

        @media (max-width: 1024px) {
            .content-grid { grid-template-columns: 1fr; }
            .sidebar { width: 70px; }
            .sidebar-brand, .sidebar-menu li a span { display: none; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
            .sidebar-menu li a i { margin-right: 0; width: 100%; text-align: center; }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-brand">AURALOOM</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_orders.php"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a></li>
            <li><a href="manage_custom_orders.php"><i class="fas fa-palette"></i> <span>Custom Orders</span></a></li>
            <li><a href="manage_b2b.php"><i class="fas fa-building"></i> <span>B2B Enquiries</span></a></li>
            <li><a href="manage_products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="manage_categories.php"><i class="fas fa-tags"></i> <span>Categories</span></a></li>
            <li><a href="manage_customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="manage-reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
            <li><a href="manage-testimonials.php"><i class="fas fa-comment-alt"></i> <span>Testimonials</span></a></li>
            <li><a href="manage_carts.php"><i class="fas fa-shopping-cart"></i> <span>Active Carts</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn" style="border:none; color:#ff4d4d;"> <i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </div>
    </aside>

    <main class="main-content">
        <header>
            <div class="logo">
                Auraloom <span class="admin-badge">Admin</span>
            </div>
            <div style="font-size: 13px; color: var(--text-muted);">
                <span style="color: var(--accent);"><?php echo date('D, M d Y'); ?></span>
            </div>
        </header>

        <div class="container">
            <h2 class="page-title">Dashboard Overview</h2>

            <h3 class="section-title">Key Metrics</h3>
            <div class="stats-grid">
                <a href="manage_customers.php" class="stat-card">
                    <h5>Customers</h5>
                    <h3><?php echo $customer_count; ?></h3>
                </a>
                <a href="manage_products.php" class="stat-card">
                    <h5>Products</h5>
                    <h3><?php echo $product_count; ?></h3>
                </a>
                <a href="manage_categories.php" class="stat-card">
                    <h5>Categories</h5>
                    <h3><?php echo $category_count; ?></h3>
                </a>
                <a href="manage_orders.php" class="stat-card">
                    <h5>Orders</h5>
                    <h3><?php echo $order_count; ?></h3>
                </a>
                <a href="manage_custom_orders.php" class="stat-card">
                    <h5>Custom Orders</h5>
                    <h3><?php echo $custom_order_count; ?></h3>
                </a>
                <a href="manage_orders.php" class="stat-card">
                    <h5>Revenue</h5>
                    <h3>₹<?= number_format($revenue) ?></h3>
                </a>
                <a href="manage_carts.php" class="stat-card">
                    <h5>Items Sold</h5>
                    <h3><?php echo $items_sold; ?></h3>
                </a>
                <a href="manage_b2b.php" class="stat-card">
                    <h5>B2B Enquiries</h5>
                    <h3><?php echo $b2b_count; ?></h3>
                </a>
            </div>

            <h3 class="section-title">Operations & Activity</h3>
            <div class="content-grid">
                <div class="data-panel">
                    <h4>Recent Customers</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($recent_customers)) { ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="data-sub"><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="data-panel">
                    <h4>New Products</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($recent_products)) { ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="data-sub">ID: #<?php echo $row['id']; ?></span>
                                </div>
                                <span class="price-tag">₹<?php echo $row['price']; ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="data-panel">
                    <h4>Top Selling Products</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($top_selling)) { ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary"><?= htmlspecialchars($row['name']) ?></span>
                                    <span class="data-sub">Sold: <?= $row['sold_qty'] ?> units</span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="data-panel">
                    <h4>Recent Custom Orders</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($recent_custom_orders)) { ?>
                            <a href="manage_custom_orders.php#order-<?php echo $row['id']; ?>" class="data-item">
                                <div>
                                    <span class="data-primary"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span class="data-sub"><?php echo htmlspecialchars($row['order_type']); ?> • <?= ucfirst($row['status']) ?></span>
                                </div>
                                <span class="price-tag"><?= $row['budget'] ? '₹' . $row['budget'] : '—' ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="data-panel">
                    <h4>Recent Reviews</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($recent_reviews)) { ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary"><?= htmlspecialchars($row['customer_name']) ?> • <?= htmlspecialchars($row['product_name']) ?></span>
                                    <span class="data-sub"><?= $row['rating'] ?>★ • <span style="color: <?= $row['status'] == 'pending' ? '#ffb347' : '#7dd87d' ?>"><?= ucfirst($row['status']) ?></span></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="data-panel">
                    <h4>Recent B2B Enquiries</h4>
                    <div class="data-list">
                        <?php while ($row = mysqli_fetch_assoc($recent_b2b)) { ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary"><?= htmlspecialchars($row['business_name']) ?></span>
                                    <span class="data-sub"><?= htmlspecialchars($row['business_type']) ?> • Qty: <?= $row['quantity'] ?></span>
                                </div>
                                <a href="https://wa.me/91<?= $row['phone'] ?>" target="_blank" style="color:#25D366"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>