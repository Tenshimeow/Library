<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "librarydb";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);


if ($conn->connect_errno) {
    die("Kết nối thất bại: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


?>
