<?php

$host = "127.0.0.1";
$user = "admin_library";
$pass = "Library@2026";
$db   = "librarydb";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);


if ($conn->connect_errno) {
    die("Kết nối thất bại: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


?>
