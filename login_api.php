<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Phương thức không được hỗ trợ."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$csrf_token = $data['csrf_token'] ?? '';

if (empty($csrf_token) || $csrf_token !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(["status" => false, "message" => "Lỗi xác thực bảo mật hệ thống (CSRF)"]);
    exit;
}

if (empty($username) || empty($password)) {
    echo json_encode(["status" => false, "message" => "Vui lòng nhập đầy đủ tài khoản và mật khẩu"]);
    exit;
}

$stmt = $conn->prepare("SELECT librarianname, password, role FROM librarian WHERE username = ? AND status = 'ACTIVE'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password']) || $password === $row['password']) {
        
        session_regenerate_id(true);
        $_SESSION['username'] = $username; 
        $_SESSION['display_name'] = $row['librarianname']; 
        $_SESSION['role'] = $row['role'];

        $log_detail = "Thủ thư " . $row['librarianname'] . " ($username) đã đăng nhập hệ thống.";
        $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'LOGIN', ?, NOW())");
        $log_stmt->bind_param("ss", $username, $log_detail);
        $log_stmt->execute();

        echo json_encode([
            "status" => true,
            "message" => "Đăng nhập thành công!",
            "user" => [
                "librarianname" => $row['librarianname'],
                "role" => $row['role']
            ]
        ]);
    } else {
        $log_fail = "Cảnh báo: Thử đăng nhập sai mật khẩu cho tài khoản: $username";
        $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'LOGIN_FAIL', ?, NOW())");
        $log_stmt->bind_param("ss", $username, $log_fail);
        $log_stmt->execute();

        echo json_encode(["status" => false, "message" => "Mật khẩu không chính xác!"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Tài khoản không tồn tại hoặc đã bị khóa!"]);
}

$stmt->close();
$conn->close();
?>