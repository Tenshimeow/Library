<?php

$host = "localhost";
$user = "root";
$pass =  "";
$db   = "librarydb";
$port = 3307;

// Tạo kết nối
$conn = new mysqli($host, $user, $pass, $db, $port);

// Kiểm tra kết nối
if ($conn->connect_errno) {
    die("Kết nối thất bại: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

// Thiết lập UTF-8
$conn->set_charset("utf8mb4");

// Nếu muốn kiểm tra kết nối thì bỏ comment dòng dưới
// echo "Kết nối thành công!";

?>
