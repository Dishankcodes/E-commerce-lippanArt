<?php
session_start();
include("db.php");

$error = "";

/* ===== AUTO LOGIN VIA REMEMBER ME ===== */
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

  $token = $_COOKIE['remember_token'];

  $stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, role FROM customers WHERE remember_token=? LIMIT 1"
  );
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);
  $user = mysqli_stmt_get_result($stmt)->fetch_assoc();

  if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    header("Location: collection.php");
    exit;
  }
}

/* ===== NORMAL CUSTOMER LOGIN ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $email = strtolower(trim($_POST['email']));
  $password = $_POST['password'];
  $remember = isset($_POST['remember']);

  $stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, password, role FROM customers WHERE email=? LIMIT 1"
  );
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $user = mysqli_stmt_get_result($stmt)->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    /* REMEMBER ME */
    if ($remember) {
      $token = bin2hex(random_bytes(32));

      setcookie(
        "remember_token",
        $token,
        time() + (86400 * 30), // 30 days
        "/",
        "",
        false,
        true
      );

      $u = mysqli_prepare(
        $conn,
        "UPDATE customers SET remember_token=? WHERE id=?"
      );
      mysqli_stmt_bind_param($u, "si", $token, $user['id']);
      mysqli_stmt_execute($u);
    }

    header("Location: collection.php");
    exit;
  }

  $error = "Invalid email or password";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In | Auraloom</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500&display=swap"
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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-dark);
      color: var(--text-main);
      height: 100vh;
      overflow: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* HEADER */
    header {
      position: fixed;
      top: 0;
      width: 100%;
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
      letter-spacing: 1px;
    }

    .back-link:hover {
      color: var(--accent);
    }


    /* LAYOUT */
    .login-container {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      height: 100vh;
    }

    /* LIPPAN WHEEL SIDE */
    .login-visual {
      background:
        radial-gradient(circle at center, rgba(196, 106, 59, 0.18), transparent 65%),
        linear-gradient(to bottom, var(--bg-dark), var(--bg-soft));
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .lippan-wheel {
      width: 360px;
      animation: spin 90s linear infinite;
      filter: drop-shadow(0 45px 80px rgba(0, 0, 0, .7));
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    .visual-text {
      position: absolute;
      bottom: 70px;
      left: 60px;
      max-width: 420px;
    }

    .visual-text h2 {
      font-family: 'Playfair Display', serif;
      font-size: 36px;
    }

    .visual-text p {
      color: var(--text-muted);
      font-size: 14px;
    }

    /* FORM SIDE */
    .login-form-wrapper {
      background: var(--bg-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .login-form-wrapper::before {
      content: "";
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 1px;
      height: 60%;
      background: var(--border-soft);
    }

    .form-box {
      width: 100%;
      max-width: 420px;
      padding: 0 40px;
    }

    .form-header {
      margin-bottom: 40px;
    }

    .form-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      color: var(--accent);
    }

    .form-header p {
      font-size: 14px;
      color: var(--text-muted);
    }

    .input-group {
      margin-bottom: 25px;
    }

    .input-label {
      font-size: 12px;
      text-transform: uppercase;
      color: var(--text-muted);
      display: block;
      margin-bottom: 8px;
    }

    .form-input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-soft);
      padding: 12px 0;
      color: var(--text-main);
    }

    .form-input:focus {
      outline: none;
      border-bottom-color: var(--accent);
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      margin-bottom: 35px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .remember-check {
      display: flex;
      gap: 8px;
    }

    .remember-check input {
      accent-color: var(--accent);
    }

    .submit-btn {
      width: 100%;
      padding: 16px;
      background: var(--accent);
      border: none;
      color: #fff;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
    }

    .submit-btn:hover {
      background: #a85830;
    }

    .error {
      color: #ff8a8a;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .form-footer {
      text-align: center;
      margin-top: 30px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .create-link {
      color: var(--accent);
    }

    .create-link:hover {
      text-decoration: underline;
    }

    /* RESPONSIVE */
    @media(max-width:900px) {
      .login-container {
        grid-template-columns: 1fr;
      }

      .login-visual {
        display: none;
      }

      header {
        padding: 20px 30px;
      }

      .login-form-wrapper::before {
        display: none;
      }
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 90px;
      /* 👈 important */
      padding: 24px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: transparent;
      z-index: 1000;
    }

    .login-container {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      height: 100vh;
      padding-top: 90px;
      /* 👈 same as header height */
    }

    header::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 92%;
      height: 1px;
      background: var(--border-soft);
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">Auraloom</div>
    <a href="index.php" class="back-link">← Back to Home</a>
  </header>


  <div class="login-container">

    <!-- LIPPAN WHEEL -->
    <div class="login-visual">
      <img src="a.png" alt="Lippan Art Wheel" class="lippan-wheel">
      <div class="visual-text">
        <h2>Curated for the<br>Connoisseur</h2>
        <p>Sign in to manage your collection and exclusive orders.</p>
      </div>
    </div>

    <!-- FORM -->
    <div class="login-form-wrapper">
      <div class="form-box">

        <div class="form-header">
          <h1>Welcome Back</h1>
          <p>Please enter your details to sign in.</p>
        </div>

        <?php if ($error): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="input-group">
            <label class="input-label">Email Address</label>
            <input type="email" name="email" class="form-input" required>
          </div>

          <div class="input-group">
            <label class="input-label">Password</label>
            <input type="password" name="password" class="form-input" required>
          </div>

          <div class="form-actions">
            <label class="remember-check">
              <input type="checkbox" name="remember"> remember me
            </label>
            <a href="#">Forgot Password?</a>
          </div>

          <button type="submit" class="submit-btn">Sign In</button>
        </form>

        <div class="form-footer">
          New to Auraloom?
          <a href="register.php" class="create-link">Create Account</a>
        </div>

      </div>
    </div>

  </div>

</body>

</html>