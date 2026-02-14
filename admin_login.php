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