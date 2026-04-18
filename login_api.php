<?php
header("Content-Type: application/json");
include "librarydb.php";

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$password = $data['password'];

$sql = "SELECT librarianname, role 
        FROM librarian 
        WHERE username='$username' 
        AND password='$password' 
        AND status='ACTIVE'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    echo json_encode([
        "status" => true,
        "message" => "Login success",
        "user" => $user
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Sai tài khoản hoặc mật khẩu"
    ]);
}
?>