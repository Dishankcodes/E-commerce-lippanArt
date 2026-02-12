<?php
include("db.php");

$products = mysqli_query($conn, "
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections | AURALOOM - Luxury Lippan Art</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        /* --- CORE BRAND VARIABLES --- */
        :root {
            --espresso: #2E1C16;
            --maroon: #5A1E1E;
            --terracotta: #A3472D;
            --gold: #B08D57;
            --ivory: #F4EFE6;
            --light-grey: #e6dfd5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--ivory);
            color: var(--espresso);
        }

        /* TOP BAR */
        .top-bar {
            background: var(--espresso);
            color: var(--ivory);
            text-align: center;
            padding: 8px;
            font-size: 13px;
            letter-spacing: 1px;
        }

        /* Header Core */
        header {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            background: rgba(244, 239, 230, 0.98);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--light-grey);
        }

        /* Logo */
        header .logo a {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--maroon);
            letter-spacing: 3px;
            text-decoration: none;
        }

        /* Navigation */
        header nav {
            display: flex;
            gap: 35px;
        }

        header nav a {
            text-decoration: none;
            color: var(--espresso);
            font-weight: 500;
            position: relative;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        header nav a:hover,
        header nav a.active {
            color: var(--terracotta);
        }

        header nav a::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0%;
            height: 2px;
            background: var(--gold);
            transition: 0.3s ease;
        }

        header nav a:hover::after,
        header nav a.active::after {
            width: 100%;
        }

        /* Dropdown */
        header .dropdown {
            position: relative;
        }

        header .dropdown-content {
            display: none;
            position: absolute;
            top: 35px;
            left: 0;
            background: rgba(244, 239, 230, 0.98);
            border: 1px solid var(--light-grey);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 180px;
            border-radius: 4px;
            flex-direction: column;
            z-index: 1000;
        }

        header .dropdown:hover .dropdown-content {
            display: flex;
            flex-direction: column;
        }

        header .dropdown-content a {
            padding: 10px 15px;
            color: var(--espresso);
            text-decoration: none;
            font-weight: 500;
        }

        header .dropdown-content a:hover {
            background: var(--gold);
            color: var(--espresso);
        }

        /* Header Buttons */
        .header-btn {
            padding: 10px 22px;
            background: var(--terracotta);
            color: white;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            transition: 0.3s ease;
            border-radius: 4px;
        }

        .header-btn:hover {
            background: var(--gold);
            color: var(--espresso);
        }

        /* User Dropdown */
        .user-dropdown>a {
            padding: 10px 22px;
            border: 1px solid var(--terracotta);
            color: var(--terracotta);
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            border-radius: 4px;
            transition: 0.3s ease;
        }

        .user-dropdown>a:hover {
            background: var(--terracotta);
            color: white;
        }

        .user-dropdown .dropdown-content {
            top: 45px;
            right: 0;
            left: auto;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 160px;
        }

        .user-dropdown .dropdown-content a {
            color: var(--espresso);
        }

        .user-dropdown .dropdown-content a:hover {
            background: var(--gold);
            color: var(--espresso);
        }

        /* HEADER ACTIONS FIX */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            /* ensures dropdown positioning works */
        }

        /* USER DROPDOWN FIX */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown .user-btn {
            padding: 10px 22px;
            border: 1px solid var(--terracotta);
            color: var(--terracotta);
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            border-radius: 4px;
            transition: 0.3s ease;
            display: inline-block;
        }

        .user-dropdown .user-btn:hover {
            background: var(--terracotta);
            color: white;
        }

        /* Dropdown content */
        .user-dropdown .dropdown-content {
            display: none;
            /* hide by default */
            position: absolute;
            top: 45px;
            right: 0;
            /* align right of button */
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            border-radius: 4px;
            z-index: 1000;
        }

        /* Keep dropdown open when hovering the container */
        .user-dropdown:hover .dropdown-content {
            display: flex;
            flex-direction: column;
        }

        /* Dropdown links */
        .user-dropdown .dropdown-content a {
            padding: 10px 15px;
            color: var(--espresso);
            text-decoration: none;
            font-weight: 500;
        }

        .user-dropdown .dropdown-content a:hover {
            background: var(--gold);
            color: var(--espresso);
        }

        /* Responsive */
        @media (max-width: 900px) {
            header {
                padding: 15px 20px;
            }

            header nav {
                display: none;
            }
        }

        /* --- COLLECTION HEADER WITH BACKGROUND IMAGE --- */
        .page-header {
            background: linear-gradient(rgba(46, 28, 22, 0.5), rgba(46, 28, 22, 0.5)),
                url('https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--ivory);
            margin-bottom: 0;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .breadcrumb {
            font-size: 14px;
            opacity: 0.9;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .breadcrumb a {
            color: var(--ivory);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }

        .breadcrumb a:hover {
            border-bottom: 1px solid var(--gold);
        }

        .breadcrumb span {
            margin: 0 10px;
            color: var(--gold);
        }

        /* LAYOUT GRID */
        .collection-container {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 60px;
            padding: 80px 60px;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* SIDEBAR */
        .sidebar {
            position: sticky;
            top: 130px;
            height: fit-content;
        }

        .filter-group {
            margin-bottom: 35px;
            border-bottom: 1px solid #e0d8cc;
            padding-bottom: 25px;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--maroon);
            margin-bottom: 18px;
            font-weight: 600;
        }

        .filter-options label {
            display: flex;
            align-items: center;
            margin-bottom: 14px;
            cursor: pointer;
            font-size: 14px;
            color: var(--espresso);
            transition: 0.2s;
        }

        .filter-options label:hover {
            color: var(--terracotta);
        }

        .filter-options input[type="checkbox"] {
            accent-color: var(--terracotta);
            margin-right: 12px;
            width: 16px;
            height: 16px;
        }

        /* TOOLBAR */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 45px;
            border-bottom: 1px solid #e0d8cc;
            padding-bottom: 20px;
        }

        .result-count {
            font-size: 14px;
            opacity: 0.7;
        }

        .sort-select {
            padding: 10px 15px;
            border: 1px solid #d4cbbd;
            background: transparent;
            color: var(--espresso);
            font-family: 'Montserrat', sans-serif;
            outline: none;
            cursor: pointer;
        }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 40px;
        }

        .product-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            border: 1px solid transparent;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(46, 28, 22, 0.1);
            border-color: #f0e6da;
        }

        .badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--terracotta);
            color: white;
            padding: 6px 14px;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            z-index: 2;
        }

        .badge.sold-out {
            background: var(--espresso);
        }

        .img-container {
            height: 340px;
            overflow: hidden;
            background: #f9f9f9;
        }

        /* --- ADDED STYLE FOR LINKS --- */
        /* Makes the image link and title link display correctly */
        .product-link,
        .product-title-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.6s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 25px 20px;
            text-align: center;
        }

        .category {
            font-size: 11px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 8px;
            letter-spacing: 1.5px;
        }

        .product-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 19px;
            margin-bottom: 10px;
            color: var(--maroon);
        }

        .price {
            color: var(--terracotta);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .add-btn {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--terracotta);
            background: transparent;
            color: var(--terracotta);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 500;
        }

        .add-btn:hover {
            background: var(--terracotta);
            color: white;
        }

        /* LOAD MORE */
        .load-more-container {
            text-align: center;
            margin-top: 80px;
        }

        /* FOOTER */
        footer {
            background: var(--espresso);
            color: var(--ivory);
            padding: 60px 40px;
            text-align: center;
            margin-top: 100px;
        }

        footer p {
            margin: 10px 0;
            font-size: 14px;
            opacity: 0.8;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            header {
                padding: 15px 20px;
            }

            nav {
                display: none;
            }

            .collection-container {
                grid-template-columns: 1fr;
                padding: 40px 20px;
            }

            .sidebar {
                position: relative;
                top: 0;
                margin-bottom: 40px;
                border-bottom: 1px solid var(--gold);
            }

            .page-header h1 {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>

    <div class="top-bar">
        ✨ Free Shipping Across India | Custom Orders Available | WhatsApp: +91 98765 43210
    </div>
    <header>
        <div class="logo"><a href="index.html">AURALOOM</a></div>

        <nav>
            <a href="index.html">Home</a>
            <a href="collection.html" class="active">Collections</a>
            <div class="dropdown">
                <a href="#">B2B ▾</a>
                <div class="dropdown-content">
                    <a href="b2b-landing.html">For Businesses</a>
                    <a href="bulk-orders.html">Bulk Orders</a>
                    <a href="case-studies.html">Case Studies</a>
                </div>
            </div>
            <a href="#">About</a>
            <a href="#">Contact</a>
        </nav>

        <div class="header-actions">
            <!-- User Info Dropdown -->
            <div class="user-dropdown dropdown">
                <a href="#" class="user-btn">Hello, John ▾</a>
                <div class="dropdown-content">
                    <a href="profile.html">Profile</a>
                    <a href="orders.html">My Orders</a>
                    <a href="logout.html">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <section class="page-header">
        <div>
            <h1>The Masterpiece Collection</h1>
            <div class="breadcrumb">
                <a href="index.html">Home</a> <span>/</span> All Products
            </div>
        </div>
    </section>

    <div class="collection-container">

        <aside class="sidebar">
            <div class="filter-group">
                <div class="filter-title">Category</div>
                <div class="filter-options">
                    <label><input type="checkbox" checked> All Products</label>
                    <label><input type="checkbox"> Wall Mirrors</label>
                    <label><input type="checkbox"> Jharokha Art</label>
                    <label><input type="checkbox"> Nameplates</label>
                    <label><input type="checkbox"> Mandala Panels</label>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Price Range</div>
                <div class="filter-options">
                    <label><input type="checkbox"> Under ₹2,000</label>
                    <label><input type="checkbox"> ₹2,000 - ₹5,000</label>
                    <label><input type="checkbox"> ₹5,000 - ₹10,000</label>
                    <label><input type="checkbox"> Above ₹10,000</label>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Size</div>
                <div class="filter-options">
                    <label><input type="checkbox"> 12" x 12" (Small)</label>
                    <label><input type="checkbox"> 18" x 18" (Medium)</label>
                    <label><input type="checkbox"> 24" x 24" (Large)</label>
                </div>
            </div>
        </aside>

        <main class="content">
            <div class="toolbar">
                <span class="result-count">Showing 6 of 24 results</span>
                <select class="sort-select">
                    <option>Sort by: Featured</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Newest Arrivals</option>
                </select>
            </div>

            <div class="product-grid">

                <?php while ($product = mysqli_fetch_assoc($products)) { ?>

                    <div class="product-card">

                        <?php if ($product['stock'] <= 0) { ?>
                            <span class="badge sold-out">Sold Out</span>
                        <?php } elseif ($product['featured'] == 'yes') { ?>
                            <span class="badge">Featured</span>
                        <?php } ?>

                        <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link">
                            <div class="img-container">
                                <img src="uploads/<?php echo $product['image']; ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                        </a>

                        <div class="product-info">
                            <div class="category">
                                <?php echo $product['category_name']; ?>
                            </div>

                            <h3>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="product-title-link">
                                    <?php echo $product['name']; ?>
                                </a>
                            </h3>

                            <div class="price">₹
                                <?php echo number_format($product['price']); ?>
                            </div>

                            <?php if ($product['stock'] > 0) { ?>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="add-btn">
                                        Add to Cart
                                    </button>
                                </form>
                            <?php } else { ?>
                                <button class="add-btn" style="opacity:0.5; cursor:not-allowed;">
                                    Out of Stock
                                </button>
                            <?php } ?>

                        </div>
                    </div>

                <?php } ?>

            </div>


            <div class="product-card">
                <span class="badge">New</span>
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1618220179428-22790b461013" alt="Royal Mandala">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Mandala</div>
                    <h3><a href="product.html" class="product-title-link">Royal Mandala Panel</a></h3>
                    <div class="price">₹4,999</div>
                    <button class="add-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace" alt="Terracotta Mirror">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Mirrors</div>
                    <h3><a href="product.html" class="product-title-link">Terracotta Mirror Art</a></h3>
                    <div class="price">₹3,499</div>
                    <button class="add-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1615874959474-d609969a20ed" alt="Heritage Frame">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Wall Decor</div>
                    <h3><a href="product.html" class="product-title-link">Heritage Wall Frame</a></h3>
                    <div class="price">₹2,999</div>
                    <button class="add-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1513519245088-0e12902e35a6?auto=format&fit=crop&w=800&q=80"
                            alt="Mud Work Jharokha">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Jharokha</div>
                    <h3><a href="product.html" class="product-title-link">Kutch Mud Jharokha</a></h3>
                    <div class="price">₹5,499</div>
                    <button class="add-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <span class="badge sold-out">Sold Out</span>
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=800&q=80"
                            alt="Peacock Motif">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Custom</div>
                    <h3><a href="product.html" class="product-title-link">Peacock Motif Panel</a></h3>
                    <div class="price">₹6,999</div>
                    <button class="add-btn" style="opacity:0.5; cursor:not-allowed;">Out of Stock</button>
                </div>
            </div>

            <div class="product-card">
                <a href="product.html" class="product-link">
                    <div class="img-container">
                        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=800&q=80"
                            alt="White Clay Art">
                    </div>
                </a>
                <div class="product-info">
                    <div class="category">Wall Decor</div>
                    <h3><a href="product.html" class="product-title-link">Ivory Clay Square</a></h3>
                    <div class="price">₹1,999</div>
                    <button class="add-btn">Add to Cart</button>
                </div>
            </div>

    </div> 

    <div class="load-more-container">
        <a href="#" class="header-btn" style="background:var(--espresso); color:white;">Load More Products</a>
    </div>
    </main>
    </div>

    <footer>
        <p>AURALOOM</p>
        <p>Luxury handcrafted Lippan Art blending tradition & elegance.</p>
        <p>Instagram | Pinterest | WhatsApp</p>
        <p>© 2026 AURALOOM. Crafted with Tradition & Luxury.</p>
    </footer>

</body>

</html>