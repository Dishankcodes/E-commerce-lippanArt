<?php
// enquiry.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>B2B Enquiry | Auraloom</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg-dark:#0f0d0b;
  --bg-soft:#171411;
  --text-main:#f3ede7;
  --text-muted:#b9afa6;
  --accent:#c46a3b;
  --border-soft:rgba(255,255,255,.12);
}

/* RESET */
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background:var(--bg-dark);
  color:var(--text-main);
}

/* ================= HEADER ================= */
header{
  position:fixed;
  top:0;
  width:100%;
  height:72px;
  z-index:1000;
  background:rgba(15,13,11,.85);
  backdrop-filter:blur(10px);
  border-bottom:1px solid var(--border-soft);
  display:grid;
  grid-template-columns:auto 1fr auto;
  align-items:center;
  padding:0 80px;
}

.logo{
  font-family:'Playfair Display',serif;
  font-size:28px;
  letter-spacing:2px;
}

nav{
  display:flex;
  justify-content:center;
  gap:34px;
}

nav a{
  font-size:13px;
  letter-spacing:1.5px;
  text-transform:uppercase;
  color:var(--text-muted);
  position:relative;
  padding-bottom:6px;
}

nav a::after{
  content:"";
  position:absolute;
  left:0;
  bottom:0;
  width:0;
  height:1px;
  background:var(--accent);
  transition:.35s;
}

nav a:hover::after,
nav a.active::after{width:100%}
nav a:hover,
nav a.active{color:var(--text-main)}

.header-btn{
  padding:10px 22px;
  background:var(--accent);
  color:#fff;
  font-size:13px;
}

/* ================= PAGE ================= */
.page{
  padding-top:140px;
  padding-bottom:120px;
  max-width:1100px;
  margin:auto;
  padding-left:40px;
  padding-right:40px;
}

.page h1{
  font-family:'Playfair Display',serif;
  font-size:42px;
  margin-bottom:12px;
}

.page p{
  color:var(--text-muted);
  max-width:600px;
  margin-bottom:50px;
}

/* ================= FORM ================= */
.form-wrap{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:40px;
}

.form-group{
  display:flex;
  flex-direction:column;
}

label{
  font-size:12px;
  letter-spacing:1px;
  text-transform:uppercase;
  color:var(--text-muted);
  margin-bottom:8px;
}

input, select, textarea{
  background:transparent;
  border:none;
  border-bottom:1px solid var(--border-soft);
  padding:12px 0;
  color:var(--text-main);
  font-family:'Poppins',sans-serif;
}

input:focus,
select:focus,
textarea:focus{
  outline:none;
  border-bottom-color:var(--accent);
}

textarea{
  resize:none;
  height:120px;
}

.full{
  grid-column:1 / -1;
}

.submit-btn{
  grid-column:1 / -1;
  margin-top:20px;
  padding:16px;
  background:var(--accent);
  border:none;
  color:#fff;
  font-size:13px;
  letter-spacing:2px;
  text-transform:uppercase;
  cursor:pointer;
}

.submit-btn:hover{
  background:#a85830;
}

/* ================= RESPONSIVE ================= */
@media(max-width:900px){
  header{padding:0 30px}
  nav{display:none}
  .page{padding:120px 24px}
  .form-wrap{grid-template-columns:1fr}
}
</style>
</head>

<body>

<header>
  <div class="logo">AURALOOM</div>

  <nav>
    <a href="index.php">Home</a>
    <a href="collection.php">Collections</a>
    <a href="custom-order.php">Custom</a>
    <a href="b2b.php" class="active">B2B</a>
    <a href="contact.php">Contact</a>
  </nav>

</header>

<section class="page">
  <h1>B2B Enquiry</h1>
  <p>
    Tell us about your business and requirements.  
    Our team will get in touch with pricing, timelines & design options.
  </p>

  <form method="post" action="submit-enquiry.php">
    <div class="form-wrap">

      <div class="form-group">
        <label>Business Name *</label>
        <input type="text" name="business_name" required>
      </div>

      <div class="form-group">
        <label>Business Type *</label>
        <select name="business_type" required>
          <option value="">Select</option>
          <option>Café / Restaurant</option>
          <option>Hotel / Resort</option>
          <option>Office / Corporate</option>
          <option>Interior Designer</option>
          <option>Builder / Architect</option>
          <option>Retail Store</option>
          <option>Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Estimated Quantity *</label>
        <input type="number" name="quantity" placeholder="e.g. 10, 50, 100" required>
      </div>

      <div class="form-group">
        <label>Phone Number *</label>
        <input type="tel" name="phone" required>
      </div>

      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group full">
        <label>Project Details / Requirements</label>
        <textarea name="message" placeholder="Space type, size, theme, deadline, city, etc."></textarea>
      </div>

      <button class="submit-btn">Submit Enquiry</button>

    </div>
  </form>
</section>

</body>
</html>
