<?php
include("db.php");

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

/* CATEGORY FILTER */
if (!empty($_GET['category'])) {
  $cat_ids = array_map('intval', $_GET['category']);
  $where .= " AND p.category_id IN (" . implode(',', $cat_ids) . ")";
}

/* PRICE FILTER */
if (!empty($_GET['price'])) {
  $price_sql = [];

  foreach ($_GET['price'] as $price) {
    switch ($price) {
      case 'under_2000':
        $price_sql[] = "p.price < 2000";
        break;
      case '2000_5000':
        $price_sql[] = "p.price BETWEEN 2000 AND 5000";
        break;
      case '5000_10000':
        $price_sql[] = "p.price BETWEEN 5000 AND 10000";
        break;
      case 'above_10000':
        $price_sql[] = "p.price > 10000";
        break;
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

/* =========================
   FINAL PRODUCTS QUERY
========================= */
$sql = "
SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
$where
$order
";

$products = mysqli_query($conn, $sql);
$total = mysqli_num_rows($products);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Collections | Auraloom</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, .12);
    }

    /* RESET */
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

    a {
      text-decoration: none;
      color: inherit
    }

    /* ================= TOP BAR ================= */
    .top-bar {
      background: #0b0a08;
      text-align: center;
      padding: 8px;
      font-size: 13px;
      color: var(--text-muted);
    }

    /* ================= HEADER (CENTERED NAV) ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      height: 72px;
      z-index: 1000;
      background: rgba(15, 13, 11, .85);
      backdrop-filter: blur(10px);
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
      gap: 34px;
    }

    nav a {
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-muted);
      position: relative;
      padding-bottom: 6px;
    }

    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 1px;
      background: var(--accent);
      transition: .35s;
    }

    nav a:hover::after,
    nav a.active::after {
      width: 100%
    }

    nav a:hover,
    nav a.active {
      color: var(--text-main)
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
    }

    /* ================= PAGE OFFSET FIX ================= */
    .page-wrap {
      padding-top: 140px;
      /* header + top-bar */
    }

    /* ================= COLLECTION LAYOUT ================= */
    .collection-container {
      display: grid;
      grid-template-columns: 260px 1fr;
      gap: 60px;
      padding: 80px;
    }

    /* ================= SIDEBAR ================= */
    .sidebar {
      position: sticky;
      top: 160px;
    }

    .filter-group {
      border-bottom: 1px solid var(--border-soft);
      padding-bottom: 24px;
      margin-bottom: 30px;
    }

    .filter-title {
      font-family: 'Playfair Display', serif;
      font-size: 18px;
      margin-bottom: 16px;
    }

    .filter-options label {
      display: flex;
      align-items: flex-start;
      /* ← key fix */
      gap: 10px;
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 12px;
      line-height: 1.5;
    }

    .filter-options input[type="checkbox"] {
      appearance: auto;
      /* restore native checkbox */
      -webkit-appearance: auto;
      width: 16px;
      height: 16px;
      margin-top: 3px;
      cursor: pointer;
    }

    .filter-options input {
      margin-top: 3px;
      /* aligns checkbox with first line */
      accent-color: var(--accent);
    }

    /* ================= TOOLBAR ================= */
    .toolbar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 40px;
      font-size: 14px;
      color: var(--text-muted);
    }

    .sort-select {
      background: #151311;
      color: var(--text-main);
      border: 1px solid var(--border-soft);
      padding: 10px 34px 10px 14px;
      appearance: none;
      cursor: pointer;
    }

    /* ================= PRODUCTS ================= */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 40px;
    }

    .product-card {
      background: #1b1815;
      border: 1px solid var(--border-soft);
      transition: .4s;
    }

    .product-card:hover {
      transform: translateY(-6px)
    }

    .product-card img {
      width: 100%;
      height: 300px;
      object-fit: cover;
    }

    .product-info {
      padding: 20px;
      text-align: center;
    }

    .product-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 18px;
    }

    .price {
      color: var(--accent);
      margin: 10px 0 18px;
    }

    .add-btn {
      display: block;
      padding: 12px;
      border: 1px solid var(--accent);
      color: var(--accent);
    }

    .add-btn:hover {
      background: var(--accent);
      color: #fff;
    }

    /* ================= MOBILE ================= */
    @media(max-width:900px) {
      nav {
        display: none
      }

      .collection-container {
        grid-template-columns: 1fr;
        padding: 40px 24px;
      }

      .sidebar {
        position: relative;
        top: 0;
      }
    }

    .checkbox {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 14px;
      color: var(--text-muted);
      cursor: pointer;
      line-height: 1.5;
      margin-bottom: 12px;
    }

    .checkbox input {
      display: none;
    }

    /* box */
    .checkbox span {
      width: 16px;
      height: 16px;
      border: 1.5px solid var(--text-muted);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-top: 3px;
      transition: .25s;
    }

    /* tick */
    .checkbox span::after {
      content: "✓";
      font-size: 12px;
      color: #fff;
      display: none;
    }

    /* checked state */
    .checkbox input:checked+span {
      background: var(--accent);
      border-color: var(--accent);
    }

    .checkbox input:checked+span::after {
      display: block;
    }

    /* hover */
    .checkbox:hover {
      color: var(--text-main);
    }

    .clear-filter-btn {
      display: block;
      text-align: center;
      padding: 12px;
      border: 1px solid var(--border-soft);
      color: var(--text-muted);
      font-size: 13px;
      letter-spacing: 1px;
      transition: .3s;
    }

    .clear-filter-btn:hover {
      border-color: var(--accent);
      color: var(--accent);
    }
  </style>
</head>

<body>

  <div class="top-bar">
    ✨ Free Shipping Across India | Custom Orders Available
  </div>

  <header>
    <div class="logo">AURALOOM</div>

    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php" class="active">Collections</a>
      <a href="custom-order.php">Custom</a>
      <a href="#">B2B</a>
      <a href="about-us.php">About Us</a>
      <a href="contact_us.php">Contact</a>

    </nav>

    <a href="cart.php" class="header-btn">Cart</a>
  </header>

  <div class="page-wrap">
    <section class="collection-container">

      <!-- SIDEBAR -->
      <aside class="sidebar">
        <form method="GET" id="filterForm">

          <div class="filter-group" style="border-bottom:none; margin-top:20px;">
            <a href="collection.php" class="clear-filter-btn">
              Clear Filters
            </a>
          </div>

          <div class="filter-group">
            <div class="filter-title">Category</div>
            <div class="filter-options">
              <?php foreach ($categories as $cat): ?>
                <label>
                  <input type="checkbox" name="category[]" value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && in_array($cat['id'], $_GET['category'])) ? 'checked' : '' ?> onchange="this.form.submit()">
                  <?= htmlspecialchars($cat['category_name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-group">
            <div class="filter-title">Price Range</div>
            <div class="filter-options">

              <label>
                <input type="checkbox" name="price[]" value="under_2000" <?= (isset($_GET['price']) && in_array('under_2000', $_GET['price'])) ? 'checked' : '' ?> onchange="this.form.submit()">
                Under ₹2,000
              </label>

              <label>
                <input type="checkbox" name="price[]" value="2000_5000" <?= (isset($_GET['price']) && in_array('2000_5000', $_GET['price'])) ? 'checked' : '' ?> onchange="this.form.submit()">
                ₹2,000 – ₹5,000
              </label>

              <label>
                <input type="checkbox" name="price[]" value="5000_10000" <?= (isset($_GET['price']) && in_array('5000_10000', $_GET['price'])) ? 'checked' : '' ?> onchange="this.form.submit()">
                ₹5,000 – ₹10,000
              </label>

              <label>
                <input type="checkbox" name="price[]" value="above_10000" <?= (isset($_GET['price']) && in_array('above_10000', $_GET['price'])) ? 'checked' : '' ?> onchange="this.form.submit()">
                Above ₹10,000
              </label>

            </div>
          </div>


        </form>
      </aside>

      <!-- PRODUCTS -->
      <main>

        <div class="toolbar">
          <span>Showing <?= $total ?> results</span>

          <select name="sort" class="sort-select" form="filterForm"
            onchange="document.getElementById('filterForm').submit()">
            <option value="">Sort by: Featured</option>
            <option value="low_high" <?= ($_GET['sort'] ?? '') == 'low_high' ? 'selected' : '' ?>>Price: Low to High
            </option>
            <option value="high_low" <?= ($_GET['sort'] ?? '') == 'high_low' ? 'selected' : '' ?>>Price: High to Low
            </option>
          </select>
        </div>

        <div class="product-grid">
          <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <div class="product-card">

              <a href="product.php?id=<?= $p['id'] ?>">
                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                  loading="lazy">
              </a>

              <div class="product-info">
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <small><?= htmlspecialchars($p['category_name']) ?></small>

                <div class="price">
                  ₹<?= number_format($p['price'], 2) ?>
                </div>

                <a href="product.php?id=<?= $p['id'] ?>" class="add-btn">
                  View Artwork
                </a>
              </div>

            </div>
          <?php endwhile; ?>
        </div>


      </main>

    </section>


  </div>

</body>

</html>