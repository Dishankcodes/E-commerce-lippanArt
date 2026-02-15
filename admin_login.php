<?php
session_start();
include("db.php");

if(isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1) {
        $_SESSION['admin_email'] = $email;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #1a1612 0%, #0f0d0b 100%);
        }

        /* Styling the Bootstrap Card to match .login-card */
        .card {
            width: 100%;
            max-width: 400px;
            background: var(--bg-soft) !important;
            padding: 40px !important;
            border: 1px solid var(--border-soft) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4) !important;
            border-radius: 0; /* Removing Bootstrap radius for sharp look */
        }

        /* Styling the H4 to match .brand-logo / .login-title */
        h4 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            text-align: center;
            letter-spacing: 3px;
            margin-bottom: 35px !important;
            color: var(--text-main);
            text-transform: uppercase;
        }

        /* Styling Bootstrap Inputs to match .form-control */
        .form-control {
            background: transparent !important;
            border: none !important;
            border-bottom: 1px solid var(--border-soft) !important;
            padding: 10px 0 !important;
            color: var(--text-main) !important;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            border-radius: 0 !important;
            margin-bottom: 25px !important;
        }

        .form-control:focus {
            outline: none !important;
            box-shadow: none !important;
            border-bottom-color: var(--accent) !important;
        }

        .form-control::placeholder {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Styling Error Message */
        .text-danger {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b !important;
            padding: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #ff6b6b;
        }

        /* Styling Button to match .login-btn */
        .btn-dark {
            width: 100%;
            padding: 14px;
            background: var(--accent) !important;
            color: white !important;
            border: none !important;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
            border-radius: 0 !important;
        }

        .btn-dark:hover {
            background: #a85830 !important;
            transform: translateY(-2px);
        }

        /* Centering Override for Bootstrap Container */
        .container {
            margin-top: 0 !important; 
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="col-md-4 mx-auto">
        <div class="card shadow p-4">
            <h4 class="text-center mb-3">Admin Login</h4>

            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>