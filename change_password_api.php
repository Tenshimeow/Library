<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username    = trim($data['username'] ?? '');
$oldPass     = trim($data['old_password'] ?? '');
$newPass     = trim($data['new_password'] ?? '');
$confirmPass = trim($data['confirm_password'] ?? '');

// 1. Validation
if (empty($username) || empty($oldPass) || empty($newPass) || empty($confirmPass)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Vui lòng điền đầy đủ các trường!"]);
    exit;
}

if ($newPass !== $confirmPass) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Mật khẩu xác nhận không khớp!"]);
    exit;
}

// 2. Kiểm tra tài khoản
$stmt = $conn->prepare("SELECT password FROM librarian WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(["status" => "error", "message" => "Tài khoản '$username' không tồn tại!"]);
    exit;
}

$row = $result->fetch_assoc();

// 3. Kiểm tra mật khẩu cũ 
// Em để so sánh thuần để anh không bị khóa tài khoản nếu login cũ chưa dùng hash
if ($oldPass === $row['password']) {
    
    // Cập nhật mật khẩu (Giữ nguyên dạng thuần nếu login cũ dùng vậy)
    $update = $conn->prepare("UPDATE librarian SET password = ? WHERE username = ?");
    $update->bind_param("ss", $newPass, $username); 
    
    if ($update->execute()) {
        http_response_code(200); // OK
        echo json_encode([
            "status" => "success",
            "message" => "Cập nhật mật khẩu thành công cho $username!"
        ]);
    } else {
        http_response_code(500); // Server Error
        echo json_encode(["status" => "error", "message" => "Lỗi database không thể cập nhật."]);
    }
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["status" => "error", "message" => "Mật khẩu hiện tại không đúng!"]);
}

$stmt->close();
$conn->close();
?>