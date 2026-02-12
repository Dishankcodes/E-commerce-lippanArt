<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Royal Mandala Masterpiece | AURALOOM</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* --- CORE BRAND VARIABLES --- */
:root {
    --espresso: #2E1C16;
    --maroon: #5A1E1E;
    --terracotta: #A3472D;
    --gold: #B08D57;
    --ivory: #F4EFE6;
    --light-grey: #e6dfd5;
    --border-color: #dcd3c9;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Montserrat', sans-serif;
    background: var(--ivory);
    color: var(--espresso);
    line-height: 1.6;
}

/* TOP BAR & HEADER (Standard) */
.top-bar {
    background: var(--espresso);
    color: var(--ivory);
    text-align: center;
    padding: 8px;
    font-size: 12px;
    letter-spacing: 1px;
}

header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(244,239,230,0.98);
    backdrop-filter: blur(8px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 60px;
    border-bottom: 1px solid var(--border-color);
}

.logo {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    letter-spacing: 3px;
    color: var(--maroon);
    text-decoration: none;
}

nav { display: flex; gap: 35px; }

nav a {
    text-decoration: none;
    color: var(--espresso);
    font-weight: 500;
    font-size: 14px;
    position: relative;
}
nav a::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 0;
    width: 0%;
    height: 2px;
    background: var(--gold);
    transition: 0.3s ease;
}

nav a:hover::after, nav a.active::after { width: 100%; }

.header-btn {
    padding: 10px 22px;
    background: var(--terracotta);
    color: white;
    text-decoration: none;
    font-size: 13px;
    letter-spacing: 1px;
    transition: 0.3s ease;
}
.header-btn:hover { background: var(--gold); color: var(--espresso); }

/* --- BREADCRUMBS --- */
.breadcrumbs {
    padding: 20px 60px;
    font-size: 13px;
    color: #888;
}
.breadcrumbs a { color: #888; text-decoration: none; transition: 0.3s; }
.breadcrumbs a:hover { color: var(--terracotta); }
.breadcrumbs span { margin: 0 8px; }

/* --- PRODUCT CONTAINER --- */
.product-container {
    display: grid;
    grid-template-columns: 1.2fr 1fr; /* Gallery wider than details */
    gap: 60px;
    padding: 0 60px 80px;
    max-width: 1400px;
    margin: 0 auto;
}

/* IMAGE GALLERY */
.gallery-wrapper {
    position: sticky;
    top: 100px; /* Sticks while scrolling details */
    align-self: start;
}

.main-image {
    width: 100%;
    height: 600px;
    background: #f0f0f0;
    margin-bottom: 20px;
    overflow: hidden;
    border-radius: 4px;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: zoom-in;
}

.main-image:hover img { transform: scale(1.1); }

.thumbnail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.thumb {
    height: 100px;
    cursor: pointer;
    opacity: 0.6;
    transition: 0.3s;
    border: 1px solid transparent;
}

.thumb:hover, .thumb.active { opacity: 1; border-color: var(--terracotta); }
.thumb img { width: 100%; height: 100%; object-fit: cover; }

/* PRODUCT DETAILS SIDE */
.product-details { padding-top: 10px; }

.product-title {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: var(--maroon);
    margin-bottom: 10px;
    line-height: 1.2;
}

.review-summary {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}
.stars { color: var(--gold); }
.review-count { text-decoration: underline; cursor: pointer; color: #666; }

.price-area {
    font-size: 28px;
    color: var(--terracotta);
    font-weight: 500;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.tax-note { font-size: 12px; color: #888; font-weight: 300; }

/* VARIANT SELECTORS */
.variant-group { margin-bottom: 25px; }

.variant-label {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
}

.size-guide-link { color: var(--gold); text-decoration: underline; cursor: pointer; font-size: 11px; }

.options-grid {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Custom Radio Buttons Styling */
.option-input { display: none; }

.option-box {
    padding: 12px 20px;
    border: 1px solid var(--border-color);
    background: white;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
    min-width: 80px;
    text-align: center;
}

.option-input:checked + .option-box {
    border-color: var(--maroon);
    background: var(--maroon);
    color: white;
}

.color-circle {
    width: 20px; 
    height: 20px; 
    border-radius: 50%; 
    display: inline-block;
    vertical-align: middle;
    margin-right: 8px;
    border: 1px solid #ddd;
}

/* ACTIONS */
.action-buttons {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 15px;
    margin: 35px 0;
}

.qty-selector {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid var(--espresso);
    padding: 0 15px;
    height: 50px;
}
.qty-btn { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--espresso); }

.add-cart-btn {
    background: var(--espresso);
    color: white;
    border: none;
    height: 50px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: 0.3s;
}
.add-cart-btn:hover { background: var(--gold); color: var(--espresso); }

/* DELIVERY INFO */
.delivery-info {
    background: #eaddcf;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 13px;
}
.delivery-icon { font-size: 18px; color: var(--maroon); }

/* ACCORDIONS */
.accordion { border-top: 1px solid var(--border-color); }

.accordion-item { border-bottom: 1px solid var(--border-color); }

.accordion-header {
    padding: 20px 0;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    font-weight: 500;
    color: var(--espresso);
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    font-size: 14px;
    color: #555;
    line-height: 1.7;
}

.accordion-content p { padding-bottom: 20px; }

/* REVIEWS SECTION */
.reviews-section {
    padding: 80px 60px;
    background: white;
    border-top: 1px solid var(--border-color);
}
.section-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--maroon); margin-bottom: 30px; text-align: center; }

.review-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.review-card {
    background: var(--ivory);
    padding: 25px;
    border-radius: 4px;
}

.review-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
.reviewer-name { font-weight: 600; font-size: 14px; }
.review-date { font-size: 12px; color: #888; }

/* RELATED PRODUCTS (Simplified from Collection Page) */
.related-products { padding: 80px 60px; background: var(--ivory); }

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}
.product-card { background: white; border-radius: 4px; overflow: hidden; transition: 0.3s; }
.product-card img { width: 100%; height: 280px; object-fit: cover; }
.product-info { padding: 15px; text-align: center; }
.p-title { font-family: 'Playfair Display'; margin-bottom: 5px; font-size: 16px; }
.p-price { color: var(--terracotta); font-weight: 600; font-size: 14px; }

/* FOOTER */
footer {
    background: var(--espresso);
    color: var(--ivory);
    padding: 60px 40px;
    text-align: center;
}
footer p { margin: 10px 0; font-size: 14px; opacity: 0.8; }

/* RESPONSIVE */
@media (max-width: 900px) {
    .product-container { grid-template-columns: 1fr; padding: 0 20px; gap: 40px; }
    .gallery-wrapper { position: relative; top: 0; }
    .main-image { height: 400px; }
    header { padding: 15px 20px; }
    nav { display: none; }
    .action-buttons { grid-template-columns: 1fr; }
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
        <a href="index.html">Home</a>
        <a href="collection.html">Collections</a>
        <a href="#">B2B</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="faq.html">FAQ</a>
    </nav>
    <a href="#" class="header-btn">Cart (0)</a>
</header>

<div class="breadcrumbs">
    <a href="index.html">Home</a> <span>/</span> 
    <a href="collection.html">Collections</a> <span>/</span> 
    Royal Mandala Masterpiece
</div>

<div class="product-container">
    
    <div class="gallery-wrapper">
        <div class="main-image">
            <img id="mainImg" src="https://images.unsplash.com/photo-1618220179428-22790b461013" alt="Lippan Art Main">
        </div>
        <div class="thumbnail-grid">
            <div class="thumb active" onclick="changeImage(this, 'https://images.unsplash.com/photo-1618220179428-22790b461013')">
                <img src="https://images.unsplash.com/photo-1618220179428-22790b461013" alt="Front View">
            </div>
            <div class="thumb" onclick="changeImage(this, 'https://images.unsplash.com/photo-1513519245088-0e12902e35a6?auto=format&fit=crop&w=800')">
                <img src="https://images.unsplash.com/photo-1513519245088-0e12902e35a6?auto=format&fit=crop&w=800" alt="Detail View">
            </div>
            <div class="thumb" onclick="changeImage(this, 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=800')">
                <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=800" alt="Side View">
            </div>
            <div class="thumb" onclick="changeImage(this, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800')">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800" alt="Room View">
            </div>
        </div>
    </div>

    <div class="product-details">
        <h1 class="product-title">Royal Mandala Masterpiece</h1>
        
        <div class="review-summary">
            <div class="stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <span class="review-count">(12 Reviews)</span>
        </div>

        <div class="price-area">
            ₹4,999 <span class="tax-note">inclusive of all taxes</span>
        </div>

        <p style="margin-bottom: 30px; font-size: 14px; color: #555;">
            Handcrafted from Kutch clay and adorned with real mirrors, this Mandala panel brings positive energy and royal elegance to your living space.
        </p>

        <div class="variant-group">
            <div class="variant-label">
                Select Size
            </div>
            <div class="options-grid">
                <input type="radio" name="size" id="s12" class="option-input">
                <label for="s12" class="option-box">12" x 12"</label>

                <input type="radio" name="size" id="s18" class="option-input" checked>
                <label for="s18" class="option-box">18" x 18"</label>

                <input type="radio" name="size" id="s24" class="option-input">
                <label for="s24" class="option-box">24" x 24"</label>
            </div>
        </div>

        <div class="variant-group">
            <div class="variant-label">Frame Finish</div>
            <div class="options-grid">
                <input type="radio" name="frame" id="f-none" class="option-input">
                <label for="f-none" class="option-box">No Frame</label>

                <input type="radio" name="frame" id="f-teak" class="option-input" checked>
                <label for="f-teak" class="option-box">
                    <span class="color-circle" style="background:#8b5a2b"></span>Teak
                </label>

                <input type="radio" name="frame" id="f-rose" class="option-input">
                <label for="f-rose" class="option-box">
                    <span class="color-circle" style="background:#3d1c1c"></span>Rosewood
                </label>
            </div>
        </div>

        <div class="delivery-info">
            <i class="fas fa-shipping-fast delivery-icon"></i>
            <div>
                <strong>Estimated Delivery:</strong><br>
                Order now to receive by <strong>Feb 20 - Feb 22</strong>
            </div>
        </div>

        <div class="action-buttons">
            <div class="qty-selector">
                <button class="qty-btn" onclick="decreaseQty()">-</button>
                <span id="qtyVal">1</span>
                <button class="qty-btn" onclick="increaseQty()">+</button>
            </div>
            <button class="add-cart-btn">ADD TO CART • ₹4,999</button>
        </div>

        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    Description <i class="fas fa-plus"></i>
                </div>
                <div class="accordion-content">
                    <p>Experience the authentic Mud Art of Gujarat. This piece is created using a dough of clay and camel dung (sanitized), known as Lippan Kaam. Small mirrors (Aabhla) are embedded into the geometric patterns, reflecting light and creating a shimmering effect.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    Shipping & Returns <i class="fas fa-plus"></i>
                </div>
                <div class="accordion-content">
                    <p>We offer free shipping across India. Since this is a handcrafted item, please allow 5-7 days for dispatch. Returns are accepted within 7 days of delivery in case of damage.</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    Care Instructions <i class="fas fa-plus"></i>
                </div>
                <div class="accordion-content">
                    <p>Do not wash with water. Clean with a dry, soft cloth or a soft-bristle brush to remove dust from the relief work. Keep away from direct moisture.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<section class="reviews-section">
    <h2 class="section-title">Customer Reviews</h2>
    <div style="text-align:center; margin-bottom:20px;">
        <span style="font-size:40px; font-weight:600; font-family:'Playfair Display'">4.9</span>/5
        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
        <p style="font-size:13px; color:#888;">Based on 12 reviews</p>
    </div>

    <div class="review-grid">
        <div class="review-card">
            <div class="review-header">
                <span class="reviewer-name">Ananya S.</span>
                <span class="review-date">Oct 12, 2025</span>
            </div>
            <div class="stars" style="font-size:12px; margin-bottom:10px;"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
            <p style="font-size:14px; line-height:1.6;">"Absolutely stunning work. The detailing of the mirrors is precise and it looks very premium on my living room wall. Packaging was very secure."</p>
        </div>

        <div class="review-card">
            <div class="review-header">
                <span class="reviewer-name">Rahul M.</span>
                <span class="review-date">Sep 05, 2025</span>
            </div>
            <div class="stars" style="font-size:12px; margin-bottom:10px;"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
            <p style="font-size:14px; line-height:1.6;">"Beautiful art piece. The terracotta color matches my decor perfectly. Took a few extra days to deliver, but worth the wait."</p>
        </div>
    </div>
</section>

<section class="related-products">
    <h2 class="section-title">You May Also Like</h2>
    <div class="product-grid">
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace" alt="Product">
            <div class="product-info">
                <h3 class="p-title">Terracotta Mirror</h3>
                <div class="p-price">₹3,499</div>
            </div>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1615874959474-d609969a20ed" alt="Product">
            <div class="product-info">
                <h3 class="p-title">Heritage Frame</h3>
                <div class="p-price">₹2,999</div>
            </div>
        </div>
        <div class="product-card">
            <img src="https://images.unsplash.com/photo-1513519245088-0e12902e35a6" alt="Product">
            <div class="product-info">
                <h3 class="p-title">Kutch Jharokha</h3>
                <div class="p-price">₹5,499</div>
            </div>
        </div>
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

    // Quantity Script
    function increaseQty() {
        let val = parseInt(document.getElementById('qtyVal').innerText);
        document.getElementById('qtyVal').innerText = val + 1;
    }

    function decreaseQty() {
        let val = parseInt(document.getElementById('qtyVal').innerText);
        if(val > 1) document.getElementById('qtyVal').innerText = val - 1;
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