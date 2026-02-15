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

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        /* --- BRAND VARIABLES --- */
        :root {
            --bg-dark: #0f0d0b;
            --bg-soft: #171411;
            --card-bg: #1b1815;
            --text-main: #f3ede7;
            --text-muted: #b9afa6;
            --accent: #c46a3b;
            --border-soft: rgba(255, 255, 255, 0.12);
        }

        /* --- RESET & TYPOGRAPHY --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            /* Standard Text */
            background: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }

        /* --- HEADER --- */
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
        }

        .logo {
            font-family: 'Playfair Display', serif;
            /* Brand Font */
            font-size: 28px;
            letter-spacing: 1px;
            color: var(--text-main);
        }

        .admin-badge {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            background: var(--accent);
            color: #fff;
            padding: 3px 8px;
            border-radius: 2px;
            margin-left: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .logout-btn {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            border: 1px solid var(--border-soft);
            padding: 8px 24px;
            color: var(--text-muted);
            border-radius: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .logout-btn:hover {
            border-color: var(--accent);
            color: var(--text-main);
            background: var(--accent);
        }

        /* --- LAYOUT --- */
        .container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 30px;
        }

        h2.page-title {
            font-family: 'Playfair Display', serif;
            /* Heading Font */
            font-size: 38px;
            margin-bottom: 40px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 20px;
        }

        /* --- STAT CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            padding: 30px;
            transition: transform 0.3s ease, border-color 0.3s ease;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }

        .stat-card h5 {
            font-family: 'Poppins', sans-serif;
            /* Label Font */
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .stat-card h3 {
            font-family: 'Playfair Display', serif;
            /* Number Font */
            font-size: 48px;
            color: var(--accent);
            font-weight: 400;
        }

        /* --- DATA PANELS --- */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .data-panel {
            background: var(--bg-soft);
            border: 1px solid var(--border-soft);
            padding: 35px;
        }

        .data-panel h4 {
            font-family: 'Playfair Display', serif;
            /* Heading Font */
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-soft);
            padding-bottom: 15px;
        }

        .data-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
            transition: 0.3s;
            border-left: 2px solid transparent;
        }

        .data-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-left: 2px solid var(--accent);
        }

        .data-primary {
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            color: var(--text-main);
            font-weight: 400;
            display: block;
        }

        .data-sub {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            display: block;
        }

        .price-tag {
            font-family: 'Playfair Display', serif;
            /* Price Font (Classic look) */
            color: var(--accent);
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 15px 20px;
            }

            .stat-card h3 {
                font-size: 36px;
            }
        }

        /* ================= ADMIN REDESIGN ENHANCEMENTS ================= */

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            margin: 50px 0 25px;
            color: var(--text-main);
            letter-spacing: 0.5px;
        }

        /* Make Revenue & Orders pop */
        .stat-card h5 {
            opacity: .75;
        }

        .stat-card:nth-child(4),
        .stat-card:nth-child(6) {
            background:
                linear-gradient(135deg,
                    rgba(196, 106, 59, .18),
                    rgba(27, 24, 21, .95));
            border-color: rgba(196, 106, 59, .5);
        }

        /* KPI emphasis */
        .stat-card:nth-child(4) h3,
        .stat-card:nth-child(6) h3 {
            font-size: 52px;
        }

        /* Content grid balance */
        .content-grid {
            align-items: start;
        }

        /* Panel polish */
        .data-panel {
            border-radius: 14px;
            background:
                linear-gradient(180deg,
                    rgba(255, 255, 255, .02),
                    rgba(0, 0, 0, .05));
        }

        /* Panel headers stick out */
        .data-panel h4 {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Item separation */
        .data-item {
            border-radius: 8px;
        }

        /* Highlight Top Selling */
        .data-panel:nth-child(3) .data-item {
            border-left: 3px solid var(--accent);
        }

        /* B2B status emphasis */
        .data-panel:nth-last-child(2) .data-sub {
            font-weight: 500;
        }

        /* Testimonials feel softer */
        .data-panel:last-child {
            background: rgba(255, 255, 255, .015);
        }

        /* Mobile admin comfort */
        @media(max-width:900px) {
            .section-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            Auraloom <span class="admin-badge">Admin</span>
        </div>
        <a href="admin_login.php" class="logout-btn">Logout</a>
    </header>

    <div class="container">
        <h2 class="page-title">Dashboard Overview</h2>


        <h3 class="section-title">Key Metrics</h3>
        <div class="stats-grid">
            <a href="manage_customers.php" class="stat-card">
                <div>
                    <h5>Customers</h5>
                    <h3><?php echo $customer_count; ?></h3>
                </div>
            </a>

            <a href="manage_products.php" class="stat-card">
                <div>
                    <h5>Products</h5>
                    <h3><?php echo $product_count; ?></h3>
                </div>
            </a>

            <a href="manage_categories.php" class="stat-card">
                <div>
                    <h5>Categories</h5>
                    <h3><?php echo $category_count; ?></h3>
                </div>
            </a>

            <a href="manage_orders.php" class="stat-card">
                <div>
                    <h5>Orders</h5>
                    <h3><?php echo $order_count; ?></h3>
                </div>
            </a>

            <a href="manage_custom_orders.php" class="stat-card">
                <div>
                    <h5>Custom Orders</h5>
                    <h3>
                        <?php echo $custom_order_count; ?>
                    </h3>
                </div>
            </a>
            <a href="manage_orders.php" class="stat-card">
                <div>
                    <h5>Revenue</h5>
                    <h3>₹
                        <?= number_format($revenue) ?>
                    </h3>
                </div>
            </a>


            <a href="manage_carts.php" class="stat-card">
                <div>
                    <h5>Items Sold</h5>
                    <h3><?php echo $items_sold; ?></h3>

                </div>
            </a>

            <a href="manage_b2b.php" class="stat-card">
                <div>
                    <h5>B2B Enquiries</h5>
                    <h3>
                        <?php echo $b2b_count; ?>
                    </h3>
                </div>
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
                    <?php if (mysqli_num_rows($top_selling) == 0): ?>
                        <p style="color:var(--text-muted);font-size:13px;">
                            No sales yet
                        </p>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($top_selling)): ?>
                            <div class="data-item">
                                <div>
                                    <span class="data-primary">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </span>
                                    <span class="data-sub">
                                        Sold:
                                        <?= $row['sold_qty'] ?> units
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="data-panel">
                <h4>Recent Custom Orders</h4>

                <div class="data-list">
                    <?php while ($row = mysqli_fetch_assoc($recent_custom_orders)) { ?>
                        <a href="manage_custom_orders.php#order-<?php echo $row['id']; ?>" class="data-item">

                            <div>
                                <span class="data-primary">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </span>
                                <span class="data-sub">
                                    <?php echo htmlspecialchars($row['order_type']); ?> •
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </div>

                            <span class="price-tag">
                                <?php echo $row['budget'] ? '₹' . $row['budget'] : '—'; ?>
                            </span>

                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="data-panel">
                <h4>
                    Recent Reviews
                    <a href="manage-reviews.php" style="float:right;font-size:12px;color:var(--accent)">
                        Manage →
                    </a>
                </h4>


                <div class="data-list">
                    <?php while ($row = mysqli_fetch_assoc($recent_reviews)) { ?>
                        <div class="data-item">

                            <div>
                                <span class="data-primary">
                                    <?php echo htmlspecialchars($row['customer_name']); ?>
                                    •
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </span>

                                <span class="data-sub">
                                    <?php echo $row['rating']; ?>★ •
                                    <span style="
                                font-size:11px;
                         padding:2px 6px;
                     border-radius:4px;
                             background: <?= $row['status'] == 'pending' ? '#ffb34722' : '#7dd87d22' ?>;
                    color: <?= $row['status'] == 'pending' ? '#ffb347' : '#7dd87d' ?>;
                                                        ">
                                        <?= ucfirst($row['status']); ?>
                                    </span>


                                    <?php echo date("M d", strtotime($row['created_at'])); ?>
                                </span>
                            </div>

                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="data-panel">
                <h4>Recent B2B Enquiries</h4>

                <div class="data-list">
                    <?php while ($row = mysqli_fetch_assoc($recent_b2b)) {

                        $waText = urlencode(
                            "Hello " . $row['business_name'] .
                            ", this is AURALOOM regarding your B2B enquiry."
                        );
                        ?>
                        <div class="data-item">

                            <div>
                                <span class="data-primary">
                                    <?php echo htmlspecialchars($row['business_name']); ?>
                                </span>

                                <span class="data-sub">
                                    <?php echo htmlspecialchars($row['business_type']); ?> •
                                    Qty:
                                    <?php echo $row['quantity']; ?> •
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </div>

                            <a href="https://wa.me/91<?php echo $row['phone']; ?>?text=<?php echo $waText; ?>"
                                target="_blank" class="price-tag" style="font-size:14px">
                                WhatsApp
                            </a>

                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="data-panel">
                <h4>
                    Recent Testimonials
                    <a href="manage-testimonials.php" style="float:right;font-size:12px;color:var(--accent)">
                        Manage →
                    </a>
                </h4>

                <div class="data-list">
                    <?php if (mysqli_num_rows($recent_testimonials) == 0): ?>
                        <p style="color:var(--text-muted);font-size:13px;">
                            No testimonials yet
                        </p>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($recent_testimonials)): ?>
                            <div class="data-item">

                                <div>
                                    <span class="data-primary">
                                        <?= htmlspecialchars($row['customer_name']) ?>
                                    </span>

                                    <span class="data-sub">
                                        <?= substr(htmlspecialchars($row['message']), 0, 70) ?>…
                                    </span>
                                </div>

                                <!-- STATUS BADGE -->
                                <span style="
                        font-size:11px;
                        padding:3px 8px;
                        border-radius:4px;
                        background: <?= $row['approved'] ? '#7dd87d22' : '#ffb34722' ?>;
                        color: <?= $row['approved'] ? '#7dd87d' : '#ffb347' ?>;
                    ">
                                    <?= $row['approved'] ? 'Approved' : 'Pending' ?>
                                </span>

                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>

</html>