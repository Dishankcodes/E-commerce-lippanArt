<?php
session_start();
include("db.php");


$topSellingQuery = mysqli_query($conn, "
  SELECT 
    p.id,
    p.name,
    p.price,
    p.image,
    SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE p.status = 'active'
  GROUP BY oi.product_id
  ORDER BY total_sold DESC
  LIMIT 8
");

$feedbackQuery = mysqli_query($conn, "
  SELECT customer_name, message 
  FROM testimonials
  WHERE approved = 1
  ORDER BY created_at DESC
  LIMIT 6
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {

  /* 🔐 LOGIN CHECK */
  if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?redirect=collection.php");
    exit;
  }


  $product_id = (int) $_POST['product_id'];
  $quantity = (int) $_POST['quantity'];

  if ($product_id <= 0 || $quantity <= 0) {
    header("Location: collection.php");
    exit;
  }

  // Fetch product
  $q = mysqli_query(
    $conn,
    "SELECT id, name, price, image 
     FROM products 
     WHERE id=$product_id AND status='active' 
     LIMIT 1"
  );

  if (mysqli_num_rows($q) == 0) {
    header("Location: collection.php");
    exit;
  }

  $product = mysqli_fetch_assoc($q);

  // Init cart
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  // Add / update cart (quantity-only cart)
  if (!isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] = 0;
  }

  $_SESSION['cart'][$product_id] += $quantity;

  header("Location: product.php?id=" . $product_id);
  exit;
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auraloom | Handcrafted Lippan Art</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, 0.12);
    }

    /* RESET */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* ================= HEADER ================= */
    header {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.75);
      backdrop-filter: blur(10px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 22px 80px;
      border-bottom: 1px solid var(--border-soft);
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      letter-spacing: 1px;
    }

    nav a {
      position: relative;
      margin-left: 34px;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding-bottom: 6px;
    }

    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0%;
      height: 1px;
      background: var(--accent);
      transition: .4s ease;
    }

    nav a:hover::after {
      width: 100%;
    }

    .header-actions {
      display: flex;
      gap: 14px;
    }

    /* HEADER BUTTONS (LOCKED) */
    .header-login {
      padding: 10px 22px;
      background: transparent;
      border: 1px solid var(--accent);
      color: var(--accent);
      font-size: 13px;
      letter-spacing: 1px;
      transition: .3s ease;
    }

    .header-login:hover {
      background: var(--accent);
      color: #fff;
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1px;
      transition: .3s ease;
    }

    .header-btn:hover {
      background: #a85830;
    }

    /* ================= HERO ================= */
    .hero {
      min-height: 100vh;
      padding-top: 120px;
      display: flex;
      align-items: center;
      background:
        radial-gradient(circle at right, rgba(196, 106, 59, 0.18), transparent 55%),
        linear-gradient(to bottom, var(--bg-dark), var(--bg-soft));
      overflow: hidden;
    }

    .hero-inner {
      display: grid;
      grid-template-columns: 1fr 1fr;
      width: 100%;
      padding-left: 80px;
      align-items: center;
    }

    .hero-text h1 {
      font-family: 'Playfair Display', serif;
      font-size: 52px;
      line-height: 1.2;
      margin-bottom: 24px;
    }

    .hero-text p {
      color: var(--text-muted);
      font-size: 17px;
      max-width: 520px;
      margin-bottom: 40px;
    }

    .hero-buttons a {
      display: inline-block;
      padding: 14px 36px;
      border-radius: 30px;
      font-size: 13px;
      letter-spacing: 1px;
      margin-right: 16px;
      transition: .4s ease;
    }

    /* HERO BUTTONS (LOCKED) */
    .btn-primary {
      background: var(--accent);
      color: #fff;
    }

    .btn-primary:hover {
      background: #a85830;
    }

    .btn-outline {
      border: 1px solid var(--accent);
      color: var(--accent);
    }

    .btn-outline:hover {
      background: var(--accent);
      color: #fff;
    }

    /* SEMICIRCLE WHEEL */
    .hero-art-mask {
      position: relative;
      height: 100vh;
      overflow: hidden;
    }

    .lippan-wheel {
      position: absolute;
      right: -48%;
      top: 50%;
      transform: translateY(-50%);
      width: 750px;
      animation: slowRotate 90s linear infinite;
      filter: drop-shadow(0 55px 80px rgba(0, 0, 0, 0.7));
    }

    .lippan-wheel:hover {
      animation-play-state: paused;
    }

    @keyframes slowRotate {
      from {
        transform: translateY(-50%) rotate(0deg);
      }

      to {
        transform: translateY(-50%) rotate(360deg);
      }
    }

    /* ================= WHY ================= */
    .why {
      padding: 120px 80px;
      background: linear-gradient(to bottom, var(--bg-soft), var(--bg-dark));
    }

    .why h2 {
      font-family: 'Playfair Display', serif;
      font-size: 40px;
      margin-bottom: 20px;
    }

    .why p {
      color: var(--text-muted);
      max-width: 600px;
      margin-bottom: 60px;
    }

    .why-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 40px;
    }

    .why-card {
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
    }

    .why-card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      margin-bottom: 12px;
    }

    .why-card p {
      font-size: 14px;
      color: var(--text-muted);
    }

    /* ================= FOOTER ================= */
    footer {
      background: #0b0a08;
      padding: 100px 80px 60px;
      border-top: 1px solid var(--border-soft);
    }

    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 50px;
      margin-bottom: 60px;
    }

    footer h4 {
      font-size: 14px;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    footer a,
    footer p {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 12px;
      display: block;
    }

    .footer-bottom {
      text-align: center;
      font-size: 13px;
      color: var(--text-muted);
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
    }

    /* ================= RESPONSIVE ================= */
    @media(max-width:900px) {
      header {
        padding: 20px 30px;
      }

      .hero-inner {
        grid-template-columns: 1fr;
        padding: 140px 30px 60px;
        text-align: center;
      }

      .hero-art-mask {
        height: 420px;
        margin-top: 40px;
      }

      .lippan-wheel {
        position: relative;
        right: auto;
        top: auto;
        transform: none;
        width: 320px;
        animation: none;
      }

      .why,
      footer {
        padding: 80px 30px;
      }
    }

    /* ================= TOP SELLING ================= */
    .top-selling {
      padding: 120px 80px;
      background: var(--bg-dark);
    }

    .top-selling h2 {
      font-family: 'Playfair Display', serif;
      font-size: 40px;
      margin-bottom: 10px;
    }

    .top-selling p {
      color: var(--text-muted);
      margin-bottom: 50px;
    }

    .product-scroll {
      display: flex;
      gap: 30px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      padding-bottom: 10px;
    }

    .product-scroll::-webkit-scrollbar {
      display: none;
    }

    .product-card {
      min-width: 260px;
      background: #1b1815;
      scroll-snap-align: start;
    }

    .product-card img {
      width: 100%;
      height: 260px;
      object-fit: cover;
    }

    .product-card h4 {
      font-family: 'Playfair Display', serif;
      font-size: 18px;
      padding: 15px 15px 5px;
    }

    .product-card span {
      color: var(--accent);
      font-size: 14px;
      padding: 0 15px 15px;
      display: block;
    }

    /* ================= FEEDBACK ================= */
    .feedback {
      padding: 120px 80px;
      background: linear-gradient(to bottom, var(--bg-soft), var(--bg-dark));
      overflow: hidden;
    }

    .feedback h2 {
      font-family: 'Playfair Display', serif;
      font-size: 40px;
      margin-bottom: 60px;
    }

    .feedback-track {
      display: flex;
      gap: 40px;
      transition: transform 0.8s ease;
    }

    .feedback-card {
      min-width: 420px;
      border-top: 1px solid var(--border-soft);
      padding-top: 30px;
    }

    .feedback-card p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.7;
      margin-bottom: 15px;
    }

    .feedback-card span {
      font-size: 14px;
      color: var(--accent);
    }

    /* =======================
   RESPONSIVE MASTER FIX
======================= */

    /* Large Tablets */
    @media (max-width: 1200px) {
      .hero-inner {
        padding-left: 50px;
      }

      .hero-text h1 {
        font-size: 46px;
      }

      .lippan-wheel {
        width: 620px;
        right: -55%;
      }
    }

    /* Tablets */
    @media (max-width: 1024px) {
      header {
        padding: 18px 40px;
      }

      nav a {
        margin-left: 24px;
      }

      .hero-text p {
        font-size: 16px;
      }
    }

    /* ================= MOBILE ================= */
    @media (max-width: 768px) {

      /* HEADER */
      header {
        flex-direction: row;
        justify-content: space-between;
        padding: 18px 26px;
      }

      nav {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        background: rgba(15, 13, 11, 0.96);
        backdrop-filter: blur(10px);
        flex-direction: column;
        text-align: center;
        padding: 30px 0;
        transform: translateY(-120%);
        transition: .5s ease;
      }

      nav.active {
        transform: translateY(0);
      }

      nav a {
        margin: 14px 0;
        font-size: 14px;
      }

      .header-actions {
        display: none;
      }

      /* HERO */
      .hero-inner {
        grid-template-columns: 1fr;
        padding: 120px 26px 40px;
        text-align: center;
      }

      .hero-text h1 {
        font-size: 34px;
      }

      .hero-text p {
        font-size: 15px;
        margin: 22px auto 30px;
      }

      .hero-buttons a {
        display: inline-block;
        margin: 8px;
        padding: 12px 26px;
      }

      .hero-art-mask {
        height: 320px;
        margin-top: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .lippan-wheel {
        position: relative;
        right: auto;
        top: auto;
        transform: none;
        width: 280px;
        animation: none;
      }

      /* WHY */
      .why {
        padding: 80px 26px;
      }

      .why h2 {
        font-size: 32px;
      }

      /* TOP SELLING */
      .top-selling {
        padding: 80px 26px;
      }

      .product-card {
        min-width: 220px;
      }

      /* FEEDBACK */
      .feedback {
        padding: 80px 26px;
      }

      .feedback h2 {
        font-size: 30px;
      }

      .feedback-card {
        min-width: 260px;
      }

      /* FOOTER */
      footer {
        padding: 70px 26px 40px;
      }
    }

    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: all 1s ease;
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    /* ================= MOBILE NAVBAR (PREMIUM) ================= */

    /* ================= CLEAN MOBILE NAVBAR ================= */

    /* Hamburger button */
    .menu-toggle {
      width: 34px;
      height: 22px;
      display: none;
      flex-direction: column;
      justify-content: space-between;
      cursor: pointer;
    }

    .menu-toggle span {
      height: 2px;
      width: 100%;
      background: var(--text-main);
      transition: 0.4s ease;
    }

    /* X animation */
    .menu-toggle.active span:nth-child(1) {
      transform: translateY(10px) rotate(45deg);
    }

    .menu-toggle.active span:nth-child(2) {
      transform: translateY(-10px) rotate(-45deg);
    }

    /* ================= MOBILE ================= */
    @media (max-width: 768px) {

      header {
        height: 64px;
        padding: 0 20px;
      }

      .logo {
        font-size: 22px;
        line-height: 64px;
      }

      .menu-toggle {
        display: flex;
        height: 64px;
        justify-content: center;
        z-index: 1001;
      }

      .header-actions {
        display: none;
      }

      nav {
        position: fixed;
        top: 64px;
        left: 0;
        width: 100%;
        height: calc(100vh - 64px);
        background: linear-gradient(to bottom,
            rgba(15, 13, 11, 0.97),
            rgba(23, 20, 17, 0.98));
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transform: translateY(-110%);
        transition: transform 0.6s cubic-bezier(.77, 0, .18, 1);
        z-index: 1000;
      }

      nav.active {
        transform: translateY(0);
      }

      nav a {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        margin: 14px 0;
        letter-spacing: 1px;
      }

      nav a::after {
        display: none;
      }
    }



    /* ================= ADD TO CART ================= */

    .add-cart-btn {
      width: 100%;
      padding: 14px 18px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      letter-spacing: 2px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .add-cart-btn:hover {
      background: #a85830;
      transform: translateY(-2px);
    }

    .add-cart-btn:active {
      transform: scale(0.98);
    }

    .add-cart-btn:disabled {
      background: #555;
      cursor: not-allowed;
    }
  </style>
</head>

<body>

  <header>
    <div class="logo">Auraloom</div>
    <div class="menu-toggle" id="menuToggle">
      <span></span>
      <span></span>
    </div>


    <nav>
      <a href="collection.php">Collection</a>
      <a href="custom-order.php">Custom</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About Us</a>
      <a href="order-history.php">Order History</a>
    </nav>
    <div class="header-actions">
      <a href="login.php" class="header-login">Login</a>

      <?php
      $username = "Guest";
      if (isset($_SESSION['customer_id'])) {
        $cid = (int) $_SESSION['customer_id'];
        $q = mysqli_query($conn, "SELECT name FROM customers WHERE id=$cid LIMIT 1");
        if ($row = mysqli_fetch_assoc($q)) {
          $username = explode(' ', $row['name'])[0];
        }
      }
      ?>

      <span class="header-btn">
        Hello
        <?= htmlspecialchars($username) ?>
      </span>
    </div>
  </header>

  <section class="hero">
    <div class="hero-inner">
      <div class="hero-text">
        <h1>Handcrafted Lippan Art<br>for Timeless Spaces</h1>
        <p>
          Rooted in Kutch tradition, shaped by hand, and crafted for
          modern homes and soulful commercial spaces.
        </p>
        <div class="hero-buttons">
          <a href="collection.php" class="btn-primary">Explore Collection</a>
          <a href="enquire.php" class="btn-outline">Bulk & B2B Orders</a>
        </div>
      </div>

      <div class="hero-art-mask">
        <img src="a.png" alt="Lippan Art" class="lippan-wheel">
      </div>
    </div>
  </section>

  <section class="why reveal">
    <h2>Why Choose Auraloom</h2>
    <p>
      Every Auraloom piece is created with intention — honoring heritage,
      craftsmanship, and the spaces they live in.
    </p>

    <div class="why-grid">
      <div class="why-card">
        <h3>Rooted in Tradition</h3>
        <p>Inspired by the timeless Lippan art of Kutch.</p>
      </div>
      <div class="why-card">
        <h3>Truly Handcrafted</h3>
        <p>Each piece shaped by hand using natural clay.</p>
      </div>
      <div class="why-card">
        <h3>Custom & B2B Ready</h3>
        <p>Tailored designs for homes and businesses.</p>
      </div>
      <div class="why-card">
        <h3>Modern Aesthetic</h3>
        <p>Traditional art curated for contemporary spaces.</p>
      </div>
    </div>
  </section>
  <!-- ================= TOP SELLING ================= -->
  <section class="top-selling reveal">
    <h2>Top Selling Pieces</h2>
    <p>Our most loved handcrafted artworks, chosen by homes and businesses.</p>

    <div class="product-scroll">
      <?php while ($product = mysqli_fetch_assoc($topSellingQuery)) { ?>
        <div class="product-card">

          <!-- CLICKABLE IMAGE + TITLE -->
          <a href="product.php?id=<?php echo $product['id']; ?>" class="product-link">
            <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            <h4><?php echo $product['name']; ?></h4>
          </a>

          <span class="price">₹<?php echo number_format($product['price'], 2); ?></span>

          <!-- ADD TO CART -->
          <form method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="add-cart-btn">Add to Cart</button>
          </form>

        </div>
      <?php } ?>
    </div>
  </section>



  <!-- ================= FEEDBACK ================= -->
  <section class="feedback reveal">
    <h2>What Our Clients Say</h2>

    <div class="feedback-track" id="feedbackTrack">
      <?php while ($review = mysqli_fetch_assoc($feedbackQuery)) { ?>
        <div class="feedback-card">
          <p>“<?php echo htmlspecialchars($review['message']); ?>”</p>
          <span>— <?php echo htmlspecialchars($review['customer_name']); ?></span>
        </div>
      <?php } ?>
    </div>
  </section>



  <footer class="reveal">
    <div class="footer-grid">
      <div>
        <h4>Auraloom</h4>
        <p>Handcrafted Lippan Art<br>Rooted in Kutch</p>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="collection.php">Shop</a>
        <a href="custom-order.php">Custom Art</a>
        <a href="about-us.php">About Us</a>
        <a href="logout.php"> Switch Acoount</a>
      </div>
      <div>
        <h4>Business</h4>
        <a href="#">B2B Orders</a>
        <a href="#">Collaborations</a>
        <a href="#">Care Guide</a>
      </div>
      <div>
        <h4>Contact</h4>
        <p>Email: hello@auraloom.in</p>
        <p>WhatsApp: +91 XXXXX XXXXX</p>
      </div>
    </div>

    <div class="footer-bottom">
      © 2026 Auraloom · Handcrafted in India
    </div>
  </footer>
  <script src='https://www.noupe.com/embed/019c52c8cb7a7420bc671dfed07b11d3bc14.js'></script>
  <script>
    const track = document.getElementById("feedbackTrack");
    let index = 0;

    function getCardWidth() {
      const card = track.querySelector(".feedback-card");
      return card.offsetWidth + 40; // card width + gap
    }

    setInterval(() => {
      index++;
      if (index >= track.children.length) index = 0;
      track.style.transform = `translateX(-${index * getCardWidth()}px)`;
    }, 4000);
  </script>
  <script>
    const reveals = document.querySelectorAll(".reveal");

    function revealOnScroll() {
      reveals.forEach(el => {
        const top = el.getBoundingClientRect().top;
        const height = window.innerHeight;
        if (top < height - 100) {
          el.classList.add("active");
        }
      });
    }

    window.addEventListener("scroll", revealOnScroll);
    revealOnScroll();


  </script>

  <script>
    const toggle = document.getElementById("menuToggle");
    const nav = document.querySelector("nav");

    toggle.addEventListener("click", () => {
      toggle.classList.toggle("active");
      nav.classList.toggle("active");
    });

    // close on link click
    document.querySelectorAll("nav a").forEach(link => {
      link.addEventListener("click", () => {
        toggle.classList.remove("active");
        nav.classList.remove("active");
      });
    });
  </script>
</body>

</html>