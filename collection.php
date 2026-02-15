<?php
session_start();
include("db.php");

/* =========================
   CALCULATE CART COUNT
========================= */
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}

/* =========================
   FETCH ACTIVE CATEGORIES
========================= */
$cat_q = mysqli_query($conn, "SELECT id, category_name FROM categories WHERE status='active'");
$categories = [];
while ($row = mysqli_fetch_assoc($cat_q)) {
    $categories[] = $row;
}

/* =========================
   FILTER LOGIC
========================= */
$where = "WHERE p.status='active'";

if (!empty($_GET['category'])) {
    $cat_ids = array_map('intval', $_GET['category']);
    $where .= " AND p.category_id IN (" . implode(',', $cat_ids) . ")";
}

if (!empty($_GET['price'])) {
    $price_sql = [];
    foreach ($_GET['price'] as $price) {
        switch ($price) {
            case 'under_2000': $price_sql[] = "p.price < 2000"; break;
            case '2000_5000': $price_sql[] = "p.price BETWEEN 2000 AND 5000"; break;
            case '5000_10000': $price_sql[] = "p.price BETWEEN 5000 AND 10000"; break;
            case 'above_10000': $price_sql[] = "p.price > 10000"; break;
        }
    }
    if ($price_sql) {
        $where .= " AND (" . implode(" OR ", $price_sql) . ")";
    }
}

/* SORTING */
$order = "ORDER BY p.created_at DESC";
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] === 'low_high') {
        $order = "ORDER BY p.price ASC";
    } elseif ($_GET['sort'] === 'high_low') {
        $order = "ORDER BY p.price DESC";
    }
}

$sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id $where $order";
$products = mysqli_query($conn, $sql);
$total = mysqli_num_rows($products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collections | Auraloom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; transition: 0.3s ease; }

        /* ================= TOP BAR ================= */
        .top-bar {
            background: #000;
            color: var(--text-muted);
            text-align: center;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-soft);
        }

        /* ================= HEADER ================= */
        header {
            position: fixed;
            top: 0; width: 100%; height: 80px;
            z-index: 1000;
            background: rgba(15, 13, 11, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-soft);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 0 60px;
        }

        .logo { font-family: 'Playfair Display', serif; font-size: 28px; letter-spacing: 2px; justify-self: start; }
        nav { display: flex; gap: 40px; }
        nav a { font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-muted); position: relative; padding-bottom: 5px; }
        nav a:hover, nav a.active { color: var(--text-main); }
        nav a::after { content: ""; position: absolute; left: 0; bottom: 0; width: 0%; height: 1px; background: var(--accent); transition: 0.4s ease; }
        nav a:hover::after, nav a.active::after { width: 100%; }

        .header-actions { justify-self: end; }
        .cart-btn { font-size: 12px; letter-spacing: 1px; text-transform: uppercase; background: var(--accent); color: #fff; padding: 10px 24px; transition: 0.3s; }
        .cart-btn:hover { background: var(--accent-hover); }

        /* ================= PAGE LAYOUT ================= */
        .page-wrap { padding-top: 120px; }

        .collection-container {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 60px;
            max-width: 1600px;
            margin: 0 auto;
            padding: 60px 60px 100px;
        }

        /* ================= SIDEBAR ================= */
        .sidebar { position: sticky; top: 120px; height: fit-content; }
        .filter-group { border-bottom: 1px solid var(--border-soft); padding-bottom: 30px; margin-bottom: 35px; }
        .filter-title { font-family: 'Playfair Display', serif; font-size: 18px; margin-bottom: 20px; letter-spacing: 0.5px; }
        
        .checkbox-label {
            display: flex; align-items: center; gap: 12px; font-size: 14px; color: var(--text-muted); cursor: pointer; margin-bottom: 14px; transition: 0.2s;
        }
        .checkbox-label:hover { color: var(--text-main); }
        
        input[type="checkbox"] {
            appearance: none; width: 16px; height: 16px; border: 1px solid var(--text-muted); background: transparent; position: relative; cursor: pointer;
        }
        input[type="checkbox"]:checked { background: var(--accent); border-color: var(--accent); }
        input[type="checkbox"]:checked::after { content: "✓"; position: absolute; color: #fff; font-size: 10px; top: 50%; left: 50%; transform: translate(-50%, -50%); }

        .clear-filter-btn { display: inline-block; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); border-bottom: 1px solid var(--border-soft); padding-bottom: 2px; margin-bottom: 25px; }
        .clear-filter-btn:hover { color: var(--accent); border-color: var(--accent); }

        /* ================= TOOLBAR ================= */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid var(--border-soft); }
        .result-count { font-family: 'Playfair Display', serif; font-size: 16px; font-style: italic; color: var(--text-muted); }
        .sort-select { background: transparent; color: var(--text-main); border: none; font-size: 13px; cursor: pointer; outline: none; }

        /* ================= PRODUCT GRID ================= */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 40px; }
        .product-card { background: var(--card-bg); border: 1px solid var(--border-soft); transition: 0.3s; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.2); box-shadow: 0 15px 30px rgba(0,0,0,0.5); }

        .product-image { width: 100%; aspect-ratio: 3/4; overflow: hidden; background: var(--bg-soft); position: relative; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s ease; }
        .product-card:hover .product-image img { transform: scale(1.05); }

        .product-info { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-info small { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 8px; }
        .product-info h3 { font-family: 'Playfair Display', serif; font-size: 19px; margin-bottom: 10px; font-weight: 500; }
        .price { font-size: 16px; color: var(--accent); font-weight: 600; margin-bottom: 20px; }

        .add-btn { width: 100%; padding: 12px; background: var(--accent); color: #fff; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; text-align: center; margin-top: auto; }
        .add-btn:hover { background: var(--accent-hover); }

        .badge-sold { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.8); color: #fff; font-size: 10px; padding: 5px 12px; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255,255,255,0.2); }

        @media (max-width: 900px) {
            header { padding: 0 20px; height: 70px; grid-template-columns: 1fr auto; }
            nav { display: none; }
            .collection-container { grid-template-columns: 1fr; padding: 40px 20px; }
            .sidebar { position: relative; top: 0; border-bottom: 1px solid var(--border-soft); margin-bottom: 40px; }
            .product-grid { grid-template-columns: 1fr 1fr; gap: 15px; }
        }
    </style>
</head>

<body>
    <div class="top-bar">✨ Free Shipping Across India | Custom Orders Available</div>

    <header>
        <div class="logo">AURALOOM</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="collection.php" class="active">Collection</a>
            <a href="custom-order.php">Custom</a>
            <a href="b2b.php">B2B</a>
            <a href="about-us.php">About</a>
        </nav>
        <div class="header-actions">
            <a href="cart.php" class="cart-btn">Cart (<?= $cart_count ?>)</a>
        </div>
    </header>

    <div class="page-wrap">
        <section class="collection-container">
            <aside class="sidebar">
                <form method="GET" id="filterForm">
                    <a href="collection.php" class="clear-filter-btn">Reset Filters</a>

                    <div class="filter-group">
                        <h4 class="filter-title">Category</h4>
                        <?php foreach ($categories as $cat): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="category[]" value="<?= $cat['id'] ?>" 
                                <?= (isset($_GET['category']) && in_array($cat['id'], $_GET['category'])) ? 'checked' : '' ?> 
                                onchange="this.form.submit()">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="filter-group">
                        <h4 class="filter-title">Price Range</h4>
                        <?php 
                        $prices = ['under_2000' => 'Under ₹2,000', '2000_5000' => '₹2,000 – ₹5,000', '5000_10000' => '₹5,000 – ₹10,000', 'above_10000' => 'Above ₹10,000'];
                        foreach ($prices as $val => $label): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="price[]" value="<?= $val ?>" 
                                <?= (isset($_GET['price']) && in_array($val, $_GET['price'])) ? 'checked' : '' ?> 
                                onchange="this.form.submit()">
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            </aside>

            <main>
                <div class="toolbar">
                    <span class="result-count">Showing <?= $total ?> masterpieces</span>
                    <select name="sort" class="sort-select" form="filterForm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Sort by: Featured</option>
                        <option value="low_high" <?= ($_GET['sort'] ?? '') == 'low_high' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="high_low" <?= ($_GET['sort'] ?? '') == 'high_low' ? 'selected' : '' ?>>Price: High to Low</option>
                    </select>
                </div>

                <div class="product-grid">
                    <?php if($total > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($products)): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="product.php?id=<?= $p['id'] ?>">
                                        <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                                    </a>
                                    <?php if ($p['stock'] <= 0): ?>
                                        <span class="badge-sold">Sold Out</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <small><?= htmlspecialchars($p['category_name']) ?></small>
                                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                                    <div class="price">₹<?= number_format($p['price'], 2) ?></div>
                                    <a href="product.php?id=<?= $p['id'] ?>" class="add-btn">View Artwork</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
                            <p style="color: var(--text-muted); font-size: 18px;">No masterpieces found matching your criteria.</p>
                            <a href="collection.php" style="color: var(--accent); margin-top: 20px; display: inline-block; border-bottom: 1px solid var(--accent);">View All Works</a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </section>
    </div>
</body>
</html>