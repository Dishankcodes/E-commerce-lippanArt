<?php
session_start();
include("db.php");

if (!isset($_GET['id'])) {
  die("Product not found");
}

$id = intval($_GET['id']);

$query = "SELECT * FROM products WHERE id = $id AND status = 'active'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);
// ================= CART-AWARE STOCK =================
$in_cart_qty = $_SESSION['cart'][$product['id']] ?? 0;
$remaining_stock = max(0, $product['stock'] - $in_cart_qty);


if (!$product) {
  die("Product not found");
}

/* ================= REVIEW PERMISSION LOGIC ================= */
$canReview = false;
$hasReviewed = false;
$oid = null;

if (isset($_SESSION['customer_id'])) {

  $uid = (int) $_SESSION['customer_id'];

  // Get logged-in user's email
  $uRes = mysqli_query($conn, "SELECT email FROM customers WHERE id=$uid LIMIT 1");

  if ($uRes && mysqli_num_rows($uRes) === 1) {
    $uRow = mysqli_fetch_assoc($uRes);
    $userEmail = mysqli_real_escape_string($conn, $uRow['email']);
  } else {
    $userEmail = null;
  }

  // Check delivered order containing this product
  $check = mysqli_query($conn, "
    SELECT oi.order_id
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.product_id = {$product['id']}
      AND o.customer_email = '$userEmail'
      AND o.order_status = 'Delivered'
    LIMIT 1
  ");

  if (mysqli_num_rows($check) > 0) {
    $canReview = true;
    $row = mysqli_fetch_assoc($check);
    $oid = $row['order_id'];

    // Check if already reviewed
    $revCheck = mysqli_query($conn, "
      SELECT id FROM product_reviews
      WHERE product_id = {$product['id']}
        AND user_id = $uid
        AND order_id = $oid
      LIMIT 1
    ");

    if (mysqli_num_rows($revCheck) > 0) {
      $hasReviewed = true;
    }
  }
}

/* ================= RELATED PRODUCTS ================= */

$related_q = mysqli_query(
  $conn,
  "SELECT id, name, price, image
   FROM products
   WHERE category_id = {$product['category_id']}
     AND id != {$product['id']}
     AND status = 'active'
   ORDER BY RAND()
   LIMIT 3"
);

$reviews_q = mysqli_query($conn, "
  SELECT r.rating, r.review_text,
    r.created_at,c.name
  FROM product_reviews r
  JOIN customers c ON r.user_id = c.id
  WHERE r.product_id = {$product['id']}
    AND r.status = 'approved'
  ORDER BY r.created_at DESC");


$rating_q = mysqli_query($conn, "
  SELECT 
    ROUND(AVG(rating), 1) AS avg_rating,
    COUNT(*) AS total_reviews
  FROM product_reviews
  WHERE product_id = {$product['id']}
    AND status = 'approved'
");

$rating = mysqli_fetch_assoc($rating_q);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?= htmlspecialchars($product['name']) ?> | AURALOOM
  </title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* ================= DARK AURALOOM THEME ================= */

    :root {
      --bg-dark: #0f0d0b;
      --bg-soft: #171411;
      --text-main: #f3ede7;
      --text-muted: #b9afa6;
      --accent: #c46a3b;
      --border-soft: rgba(255, 255, 255, 0.12);
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
      line-height: 1.6;
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* ================= TOP BAR ================= */
    .top-bar {
      background: #0b0a08;
      color: var(--text-muted);
      text-align: center;
      padding: 8px;
      font-size: 12px;
      letter-spacing: 1px;
    }

    /* ================= HEADER ================= */
    header {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(15, 13, 11, 0.85);
      backdrop-filter: blur(10px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 60px;
      border-bottom: 1px solid var(--border-soft);
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      letter-spacing: 3px;
      color: var(--text-main);
    }

    nav {
      display: flex;
      gap: 34px;
    }

    nav a {
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      position: relative;
    }

    nav a::after {
      content: "";
      position: absolute;
      bottom: -6px;
      left: 0;
      width: 0;
      height: 1px;
      background: var(--accent);
      transition: 0.4s;
    }

    nav a:hover::after {
      width: 100%;
    }

    .header-btn {
      padding: 10px 22px;
      background: var(--accent);
      color: #fff;
      font-size: 13px;
      letter-spacing: 1px;
      transition: .3s;
    }

    .header-btn:hover {
      background: #a85830;
    }

    /* ================= BREADCRUMBS ================= */
    .breadcrumbs {
      padding: 20px 60px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .breadcrumbs a {
      color: var(--text-muted);
    }

    .breadcrumbs span {
      margin: 0 8px;
    }

    /* ================= PRODUCT CONTAINER ================= */
    .product-container {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 60px;
      padding: 0 60px 80px;
      max-width: 1400px;
      margin: auto;
    }

    /* ================= GALLERY ================= */
    .gallery-wrapper {
      position: sticky;
      top: 110px;
    }

    .main-image {
      width: 100%;
      height: 600px;
      background: #1b1815;
      margin-bottom: 20px;
      overflow: hidden;
    }


    .main-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .6s ease;
      cursor: zoom-in;
    }

    .main-image:hover img {
      transform: scale(1.08);
    }

    .thumbnail-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
    }

    .stock-badge {
      font-size: 12px;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 500;
    }

    .stock-badge.in {
      background: rgba(125, 216, 125, 0.15);
      color: #7dd87d;
    }

    .stock-badge.low {
      background: rgba(255, 179, 71, 0.15);
      color: #ffb347;
    }

    .stock-badge.out {
      background: rgba(255, 107, 107, 0.15);
      color: #ff6b6b;
    }


    .thumb {
      height: 100px;
      opacity: .6;
      cursor: pointer;
      border: 1px solid var(--border-soft);
      transition: .3s;
    }

    .thumb.active,
    .thumb:hover {
      opacity: 1;
      border-color: var(--accent);
    }

    .thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* ================= PRODUCT DETAILS ================= */
    .product-title {
      font-family: 'Playfair Display', serif;
      font-size: 34px;
      margin-bottom: 10px;
    }

    .review-summary {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .stars {
      color: var(--accent);
    }

    .review-count {
      color: var(--text-muted);
      cursor: pointer;
    }

    .price-area {
      font-size: 28px;
      color: var(--accent);
      margin-bottom: 30px;
    }

    .tax-note {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* ================= VARIANTS ================= */
    .variant-group {
      margin-bottom: 26px;
    }

    .variant-label {
      font-size: 13px;
      letter-spacing: 1px;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    .options-grid {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .option-input {
      display: none;
    }

    .option-box {
      padding: 12px 22px;
      background: var(--bg-soft);
      border: 1px solid var(--border-soft);
      cursor: pointer;
      font-size: 14px;
      transition: .3s;
    }

    .option-input:checked+.option-box {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .color-circle {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
      vertical-align: middle;
    }

    /* ================= DELIVERY ================= */
    .delivery-info {
      background: #1b1815;
      padding: 16px;
      margin: 30px 0;
      display: flex;
      gap: 14px;
      font-size: 13px;
    }

    .delivery-icon {
      color: var(--accent);
    }

    /* ================= ACTIONS ================= */
    .action-buttons {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 15px;
      margin-bottom: 40px;
    }

    .qty-selector {
      border: 1px solid var(--border-soft);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 14px;
      height: 50px;
    }

    .qty-btn {
      background: none;
      border: none;
      color: var(--text-main);
      font-size: 18px;
      cursor: pointer;
    }

    .add-cart-btn {
      background: var(--accent);
      border: none;
      color: #fff;
      height: 50px;
      letter-spacing: 1px;
      font-weight: 600;
      cursor: pointer;
    }

    .add-cart-btn:hover {
      background: #a85830;
    }

    /* ================= ACCORDION ================= */
    .accordion {
      border-top: 1px solid var(--border-soft);
    }

    .accordion-item {
      border-bottom: 1px solid var(--border-soft);
    }

    .accordion-header {
      padding: 18px 0;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
    }

    .accordion-content {
      max-height: 0;
      overflow: hidden;
      color: var(--text-muted);
      font-size: 14px;
    }

    /* ================= REVIEWS ================= */
    .reviews-section {
      background: var(--bg-soft);
      padding: 80px 60px;
      border-top: 1px solid var(--border-soft);
    }

    .review-card {
      background: #1b1815;
      padding: 24px;
    }

    /* ================= RELATED PRODUCTS ================= */
    .related-products {
      padding: 80px 60px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }

    .product-card {
      background: #1b1815;
    }

    .product-card img {
      width: 100%;
      height: 260px;
      object-fit: cover;
    }

    .product-info {
      padding: 15px;
      text-align: center;
    }

    .p-title {
      font-family: 'Playfair Display', serif;
    }

    .p-price {
      color: var(--accent);
    }

    .related-products .product-grid {
      justify-content: center;
    }

    .related-products .product-card {
      max-width: 320px;
    }

    /* ================= FOOTER ================= */
    footer {
      background: #0b0a08;
      color: var(--text-muted);
      padding: 60px 40px;
      text-align: center;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 900px) {
      .product-container {
        grid-template-columns: 1fr;
        padding: 0 20px;
      }

      .gallery-wrapper {
        position: relative;
        top: 0;
      }

      .main-image {
        height: 380px;
      }

      header {
        padding: 15px 20px;
      }

      nav {
        display: none;
      }

      .action-buttons {
        grid-template-columns: 1fr;
      }
    }

    .add-cart-btn:disabled {
      background: #555;
      cursor: not-allowed;
      opacity: 0.6;
    }

    /* ===== WRITE REVIEW (PRODUCT PAGE) ===== */

    .review-write-card {
      background: #1b1815;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 16px;
      padding: 28px;
      margin-top: 40px;
    
    }

    .review-write-title {
      font-family: 'Playfair Display', serif;
      font-size: 22px;
      margin-bottom: 6px;
    }

    .review-write-subtitle {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    .review-field {
      margin-bottom: 18px;
    }

    .review-field label {
      display: block;
      font-size: 13px;
      color: #bfb6ae;
      margin-bottom: 6px;
    }

    .review-field select,
    .review-field textarea {
      width: 100%;
      background: #0f0d0b;
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
      padding: 12px 14px;
      font-size: 14px;
      border-radius: 10px;
      outline: none;
      transition: border-color .25s, box-shadow .25s;
    }

    .review-field textarea {
      min-height: 120px;
      resize: vertical;
    }

    .review-field select:focus,
    .review-field textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 2px rgba(196, 106, 59, 0.25);
    }

    .review-submit-btn {
      margin-top: 10px;
      background: var(--accent);
      border: none;
      color: #fff;
      padding: 12px 26px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
      border-radius: 24px;
      cursor: pointer;
      transition: background .25s, transform .2s;
    }

    .review-submit-btn:hover {
      background: #a95a32;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>

  <div class="top-bar">
    ✨ Free Shipping Across India | Custom Orders Available | WhatsApp: +91 98765 43210
  </div>

  <header>
    <a href="index.html" class="logo">AURALOOM</a>
    <nav>
      <a href="index.php">Home</a>
      <a href="collection.php">Collections</a>
      <a href="b2b.php">B2B</a>
      <a href="about-us.php">About</a>
      <a href="custom-order.php">Custom Orders</a>
      <a href="faq.html">FAQ</a>
    </nav>
    <a href="cart.php" class="header-btn">
      Cart (
      <?= array_sum($_SESSION['cart'] ?? []) ?>)
    </a>

  </header>

  <div class="breadcrumbs">
    <a href="index.php">Home</a> <span>/</span>
    <a href="collection.php">Collections</a> <span>/</span>
    <?= htmlspecialchars($product['name']) ?>

  </div>

  <div class="product-container">

    <!-- GALLERY -->
    <div class="gallery-wrapper">
      <div class="main-image">
        <img id="mainImg" src="uploads/<?= htmlspecialchars($product['image']) ?>"
          alt="<?= htmlspecialchars($product['name']) ?>">

      </div>

      <?php if (!empty($product['gallery'])): ?>
        <div class="thumbnail-grid">
          <?php
          $images = explode(',', $product['gallery']);
          foreach ($images as $index => $img):
            ?>
            <div class="thumb <?= $index === 0 ? 'active' : '' ?>"
              onclick="changeImage(this, '<?= htmlspecialchars($img) ?>')">
              <img src="<?= htmlspecialchars($img) ?>" alt="Product image">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- DETAILS -->
    <div class="product-details">

      <h1 class="product-title">
        <?= htmlspecialchars($product['name']) ?>
      </h1>

      <!-- PRICE -->
      <div class="price-area">
        ₹<?= number_format($product['price'], 2) ?>
        <span class="tax-note">inclusive of all taxes</span>
      </div>
      <!-- stock -->
      <div class="review-summary">
        <?php if ($remaining_stock <= 0): ?>
          <span class="stock-badge out">Out of Stock</span>

        <?php elseif ($remaining_stock <= 3): ?>
          <span class="stock-badge low">
            Only <?= $remaining_stock ?> left
          </span>

        <?php else: ?>
          <span class="stock-badge in">In Stock</span>
        <?php endif; ?>

      </div>



      <!-- DESCRIPTION -->
      <p style="margin-bottom:30px;font-size:14px;color:var(--text-muted)">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </p>

      <div style="
  background:#1b1815;
  padding:14px;
  margin-bottom:30px;
  font-size:13px;
  color:var(--text-muted);
  border-left:3px solid var(--accent);
">
        <strong style="color:var(--text-main);">Size & Customization:</strong><br>
        This artwork is available in the size mentioned in the description.
        If you want to change the size, please contact us or explore similar products in our collection.
      </div>



      <form method="post" action="cart.php" onsubmit="return <?= $product['stock'] > 0 ? 'true' : 'false' ?>;">

        <!-- REQUIRED BY CART LOGIC -->
        <input type="hidden" name="add_to_cart" value="1">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="quantity" id="qtyInput" value="1">

        <div class="action-buttons">
          <div class="qty-selector">
            <button type="button" class="qty-btn" onclick="decreaseQty()">-</button>
            <span id="qtyVal">1</span>
            <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
          </div>

          <button type="submit" class="add-cart-btn" <?= $remaining_stock <= 0 ? 'disabled' : '' ?>>
            <?= $remaining_stock <= 0
              ? 'OUT OF STOCK'
              : 'ADD TO CART • ₹' . number_format($product['price'], 2) ?>
          </button>
        </div>

      </form>

      <!-- ACCORDION -->
      <div class="accordion">

        <?php if (!empty($product['more_details'])): ?>
          <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion(this)">
              More Details <i class="fas fa-plus"></i>
            </div>
            <div class="accordion-content">
              <p><?= nl2br(htmlspecialchars($product['more_details'])) ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['specifications'])): ?>
          <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion(this)">
              Specifications <i class="fas fa-plus"></i>
            </div>
            <div class="accordion-content">
              <p><?= nl2br(htmlspecialchars($product['specifications'])) ?></p>
            </div>
          </div>
        <?php endif; ?>

      </div>

    </div>
  </div>

  <section class="reviews-section">
    <h2 class="section-title">Customer Reviews</h2>
    <div style="text-align:center; margin-bottom:20px;">
      <span style="font-size:40px; font-weight:600; font-family:'Playfair Display'">
        <?= $rating['avg_rating'] ?: '0.0' ?>
      </span>/5

      <p style="font-size:13px; color:#888;">
        Based on
        <?= $rating['total_reviews'] ?> reviews
      </p>
    </div>

    <div class="review-grid">
      <?php while ($rev = mysqli_fetch_assoc($reviews_q)): ?>
        <div class="review-card">
          <div class="review-header">
            <span class="reviewer-name">
              <?= htmlspecialchars($rev['name']) ?>
            </span>
            <span class="review-date">
              <?= date('M d, Y', strtotime($rev['created_at'])) ?>
            </span>
          </div>

          <div class="stars" style="font-size:12px; margin-bottom:10px;">
            <?php
            for ($i = 1; $i <= 5; $i++) {
              echo $i <= $rev['rating']
                ? '<i class="fas fa-star"></i>'
                : '<i class="far fa-star"></i>';
            }
            ?>
          </div>

          <p style="font-size:14px; line-height:1.6;">
            "
            <?= htmlspecialchars($rev['review_text']) ?>"
          </p>
        </div>
      <?php endwhile; ?>
    </div>

    <?php if ($canReview && !$hasReviewed): ?>
      <div class="review-write-card">

        <h3 class="review-write-title">Write a Review</h3>
        <p class="review-write-subtitle">
          Share your experience with this product
        </p>

        <form method="post" action="submit-review.php">

          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="hidden" name="order_id" value="<?= $oid ?>">

          <!-- RATING -->
          <div class="review-field">
            <label>Your Rating</label>
            <select name="rating" required>
              <option value="">Select rating</option>
              <option value="5">★★★★★ Excellent</option>
              <option value="4">★★★★☆ Very Good</option>
              <option value="3">★★★☆☆ Good</option>
              <option value="2">★★☆☆☆ Fair</option>
              <option value="1">★☆☆☆☆ Poor</option>
            </select>
          </div>

          <!-- REVIEW TEXT -->
          <div class="review-field">
            <label>Your Feedback</label>
            <textarea name="review_text" required
              placeholder="What did you like or dislike about this product?"></textarea>
          </div>

          <button type="submit" class="review-submit-btn">
            Submit Review
          </button>

        </form>
      </div>
    <?php endif; ?>


  </section>


  <section class="related-products">
    <h2 class="section-title">You May Also Like</h2>

    <div class="product-grid">
      <?php if (mysqli_num_rows($related_q) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($related_q)): ?>
          <div class="product-card">
            <a href="product.php?id=<?= $r['id'] ?>">
              <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
            </a>

            <div class="product-info">
              <h3 class="p-title">
                <?= htmlspecialchars($r['name']) ?>
              </h3>
              <div class="p-price">₹
                <?= number_format($r['price'], 2) ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="color:var(--text-muted);">No related products found.</p>
      <?php endif; ?>
    </div>
  </section>


  <footer>
    <p>AURALOOM</p>
    <p>Luxury handcrafted Lippan Art.</p>
    <p>© 2026 AURALOOM.</p>
  </footer>

  <script>
    // Image Gallery Script
    function changeImage(thumb, src) {
      document.getElementById('mainImg').src = src;
      // Remove active class from all thumbs
      document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
      // Add active class to clicked thumb
      thumb.classList.add('active');
    }



    // Accordion Script
    function toggleAccordion(header) {
      const content = header.nextElementSibling;
      const icon = header.querySelector('i');

      if (content.style.maxHeight) {
        content.style.maxHeight = null;
        icon.classList.remove('fa-minus');
        icon.classList.add('fa-plus');
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        icon.classList.remove('fa-plus');
        icon.classList.add('fa-minus');
      }
    }
  </script>

</body>

</html>
<script>
  const maxStock = <?= (int) $remaining_stock ?>;

  function increaseQty() {
    let qtyVal = document.getElementById('qtyVal');
    let qtyInput = document.getElementById('qtyInput');
    let val = parseInt(qtyVal.innerText);

    if (val >= maxStock) {
      alert("Only " + maxStock + " quantity available.");
      return;
    }

    val++;
    qtyVal.innerText = val;
    qtyInput.value = val;
  }

  function decreaseQty() {
    let qtyVal = document.getElementById('qtyVal');
    let qtyInput = document.getElementById('qtyInput');
    let val = parseInt(qtyVal.innerText);

    if (val > 1) {
      val--;
      qtyVal.innerText = val;
      qtyInput.value = val;
    }
  }
</script>