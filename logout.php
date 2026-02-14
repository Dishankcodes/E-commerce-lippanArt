<?php
session_start();
include("db.php");

if (isset($_SESSION['user_id'])) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE customers SET remember_token=NULL WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
}

setcookie("remember_token", "", time() - 3600, "/");
session_destroy();

header("Location: login.php");
exit;
