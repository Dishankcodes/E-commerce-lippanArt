<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | Auraloom</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

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

/* HEADER */
header {
  padding: 24px 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
}

.back-link {
  font-size: 13px;
  color: var(--text-muted);
}
.back-link:hover { color: var(--accent); }

/* CONTACT SECTION */
.contact-section {
  max-width: 1100px;
  margin: 80px auto;
  padding: 0 40px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
}

/* LEFT INFO */
.contact-info h1 {
  font-family: 'Playfair Display', serif;
  font-size: 42px;
  margin-bottom: 15px;
  color: var(--accent);
}

.contact-info p {
  font-size: 15px;
  color: var(--text-muted);
  margin-bottom: 25px;
  line-height: 1.7;
}

.info-box {
  border-left: 2px solid var(--accent);
  padding-left: 20px;
  margin-bottom: 25px;
}

.info-box h4 {
  font-size: 14px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 6px;
}

/* WHATSAPP BUTTON */
.whatsapp-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 22px;
  background: #25D366;
  color: #fff;
  font-size: 13px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-top: 20px;
}

.whatsapp-btn:hover {
  opacity: 0.85;
}

/* FORM */
.contact-form {
  background: var(--bg-soft);
  padding: 40px;
}

.form-group {
  margin-bottom: 25px;
}

label {
  display: block;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--text-muted);
  margin-bottom: 8px;
}

input, textarea {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--border-soft);
  padding: 12px 0;
  color: var(--text-main);
  font-family: 'Poppins', sans-serif;
}

input:focus, textarea:focus {
  outline: none;
  border-bottom-color: var(--accent);
}

textarea {
  resize: none;
  height: 120px;
}

.submit-btn {
  width: 100%;
  padding: 16px;
  background: var(--accent);
  border: none;
  color: #fff;
  font-size: 13px;
  letter-spacing: 2px;
  text-transform: uppercase;
  cursor: pointer;
}

.submit-btn:hover {
  background: #a85830;
}

/* RESPONSIVE */
@media(max-width:900px) {
  .contact-section {
    grid-template-columns: 1fr;
    margin-top: 40px;
  }
  header { padding: 20px 30px; }
}
</style>
</head>

<body>

<header>
  <div class="logo">Auraloom</div>
  <a href="index.php" class="back-link">← Back to Home</a>
</header>

<section class="contact-section">

  <!-- LEFT INFO -->
  <div class="contact-info">
    <h1>Contact Us</h1>
    <p>
      For bulk orders, custom Lippan art, collaborations, or if you face any issue,
      feel free to reach out. Our team will get back to you as soon as possible.
    </p>

    <div class="info-box">
      <h4>Bulk Orders & Custom Work</h4>
      <p>Perfect for weddings, gifting & large installations.</p>
    </div>

    <div class="info-box">
      <h4>Support & Issues</h4>
      <p>Report payment, delivery or product-related concerns.</p>
    </div>

    <a href="https://wa.me/919999999999" target="_blank" class="whatsapp-btn">
      Contact on WhatsApp (Fast)
    </a>
  </div>

  <!-- FORM -->
  <div class="contact-form">
    <form method="post" action="#">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" required>
      </div>

      <div class="form-group">
        <label>Phone / WhatsApp Number</label>
        <input type="tel" required>
      </div>

      <div class="form-group">
        <label>Your Message</label>
        <textarea placeholder="Bulk order details, issue description, or enquiry..." required></textarea>
      </div>

      <button class="submit-btn">Send Enquiry</button>
    </form>
  </div>

</section>

</body>
</html>
