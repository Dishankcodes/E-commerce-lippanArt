<?php
$client_id = "811381816438-qm2jsvlqcqb510100fbsn8aoi2gq83ku.apps.googleusercontent.com";
$redirect_uri = "http://localhost/final/google_callback.php";
$scope = "email profile";





$url = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=$scope";

header("Location: $url");
exit;
?>
