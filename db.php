<?php
$host = "localhost";     // MySQL host
$user = "root";          // default user in XAMPP
$pass = "";              // default password is empty
$db   = "lippanart";  // your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>