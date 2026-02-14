<?php
session_start();
include("db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $phone = trim($_POST['phone']);
    $pass  = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    if ($pass !== $cpass) {
        $error = "Passwords do not match";
    } elseif (strlen($pass) < 4) {
        $error = "Password must be at least 4 characters";
    } else {

        /* CHECK EMAIL EXISTS */
        $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Email already exists";
        } else {

            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            /* INSERT CUSTOMER */
            $insert = mysqli_prepare($conn, "
                INSERT INTO customers (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, 'customer')
            ");
            mysqli_stmt_bind_param($insert, "ssss", $name, $email, $hashed, $phone);

            if (mysqli_stmt_execute($insert)) {

                /* ✅ STANDARD SESSION KEYS */
                $_SESSION['user_id']   = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'customer';

                header("Location: index.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Account | Auraloom</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg-dark:#0f0d0b;
  --bg-soft:#171411;
  --text-main:#f3ede7;
  --text-muted:#b9afa6;
  --accent:#c46a3b;
  --border-soft:rgba(255,255,255,0.12);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Poppins',sans-serif;
  background:var(--bg-dark);
  color:var(--text-main);
  height:100vh;
  overflow:hidden;
}
a{text-decoration:none;color:inherit}

/* HEADER */
header{
  position:fixed;
  top:0;
  width:100%;
  padding:24px 60px;
  display:flex;
  justify-content:space-between;
}
.logo{
  font-family:'Playfair Display',serif;
  font-size:28px;
}
.back-home-btn{
  padding:10px 22px;
  border:1px solid var(--accent);
  color:#fff;
  font-size:13px;
  letter-spacing:1px;
  transition:.3s ease;
}

.back-home-btn:hover{
  background:var(--accent);
  color:#fff;
}

/* LAYOUT */
.container{
  display:grid;
  grid-template-columns:1fr 1.2fr;
  height:100vh;
}

/* LEFT – LIPPAN WHEEL */
.visual{
  background:
    radial-gradient(circle at center, rgba(196,106,59,0.18), transparent 65%),
    linear-gradient(to bottom,var(--bg-dark),var(--bg-soft));
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
}
.visual img{
  width:360px;
  animation:spin 90s linear infinite;
  filter:drop-shadow(0 45px 80px rgba(0,0,0,.7));
}
@keyframes spin{
  from{transform:rotate(0deg);}
  to{transform:rotate(360deg);}
}
.visual-text{
  position:absolute;
  bottom:70px;
  left:60px;
}
.visual-text h2{
  font-family:'Playfair Display',serif;
  font-size:36px;
}
.visual-text p{
  color:var(--text-muted);
  font-size:14px;
}

/* FORM SIDE */
.form-wrap{
  background:var(--bg-soft);
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
}
.form-wrap::before{
  content:"";
  position:absolute;
  left:0;
  height:60%;
  width:1px;
  background:var(--border-soft);
}
.form-box{
  width:100%;
  max-width:420px;
  padding:0 40px;
}
.form-box h1{
  font-family:'Playfair Display',serif;
  font-size:32px;
  color:var(--accent);
}
.form-box p{
  color:var(--text-muted);
  font-size:14px;
  margin-bottom:30px;
}
.input-group{
  margin-bottom:22px;
}
label{
  font-size:12px;
  color:var(--text-muted);
  letter-spacing:1px;
}
input{
  width:100%;
  background:transparent;
  border:none;
  border-bottom:1px solid var(--border-soft);
  padding:12px 0;
  color:var(--text-main);
}
input:focus{
  outline:none;
  border-bottom-color:var(--accent);
}
button{
  width:100%;
  padding:16px;
  background:var(--accent);
  border:none;
  color:#fff;
  letter-spacing:1.5px;
  margin-top:15px;
  cursor:pointer;
}
button:hover{background:#a85830;}
.error{
  color:#ff8a8a;
  font-size:13px;
  margin-bottom:15px;
}
.footer{
  text-align:center;
  margin-top:25px;
  font-size:13px;
  color:var(--text-muted);
}
.footer a{color:var(--accent);}

/* RESPONSIVE */
@media(max-width:900px){
  .container{grid-template-columns:1fr;}
  .visual{display:none;}
  header{padding:20px 30px;}
  .form-wrap::before{display:none;}
}
</style>
</head>

<body>

<header>
  <div class="logo">Auraloom</div>
  <a href="index.php" class="back-home-btn">← Back to Home</a>
</header>

<div class="container">

  <div class="visual">
    <img src="a.png" alt="Lippan Art Wheel">
    <div class="visual-text">
      <h2>Begin Your<br>Journey</h2>
      <p>Create an account to access exclusive pieces.</p>
    </div>
  </div>

  <div class="form-wrap">
    <div class="form-box">

      <h1>Create Account</h1>
      <p>Join Auraloom today</p>

      <?php if($error): ?>
        <div class="error"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <label>Full Name</label>
          <input type="text" name="name" required>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>

        <div class="input-group">
          <label>Phone</label>
          <input type="text" name="phone" required>
        </div>

        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>

        <div class="input-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" required>
        </div>

        <button type="submit">Create Account</button>
      </form>

      <div class="footer">
        Already have an account?
        <a href="login.php">Sign in</a>
      </div>

    </div>
  </div>

</div>

</body>
</html>
