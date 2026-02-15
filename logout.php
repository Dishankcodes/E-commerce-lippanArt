<?php
session_start();
include("db.php");

/* 🔐 CLEAR REMEMBER TOKEN FROM DB */
if (isset($_SESSION['customer_id'])) {

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE customers SET remember_token = NULL WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['customer_id']);
    mysqli_stmt_execute($stmt);
}

/* 🍪 DELETE REMEMBER COOKIE */
setcookie(
    "remember_token",
    "",
    [
        "expires" => time() - 3600,
        "path" => "/",
        "secure" => isset($_SERVER['HTTPS']),
        "httponly" => true,
        "samesite" => "Lax"
    ]
);

/* 🧹 DESTROY SESSION */
session_unset();
session_destroy();

/* 🔁 REDIRECT */
header("Location: login.php");
exit;
