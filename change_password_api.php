<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php"; 


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); 
    echo json_encode(["status" => "error", "message" => "Phương thức HTTP không được hỗ trợ (Yêu cầu PUT)."]);
    exit;
}


if (!isset($_SESSION['username'])) {
    http_response_code(401); 
    echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập hệ thống!"]);
    exit;
}

$myUsername = $_SESSION['username']; 

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$old_password = $data['old_password'] ?? '';
$new_password = $data['new_password'] ?? '';

if (empty($username) || empty($old_password) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Không được để trống thông tin yêu cầu!"]);
    exit;
}

if ($username !== $myUsername) {
    echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ! Bạn không thể đổi mật khẩu của người khác."]);
    exit;
}

$stmt = $conn->prepare("SELECT password, librarianname FROM librarian WHERE username = ? AND status = 'ACTIVE'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $isOldPassValid = false;
    
    if (password_verify($old_password, $row['password']) || $old_password === $row['password']) {
        $isOldPassValid = true;
    }

    if ($isOldPassValid) {
        $updatedPassword = $new_password; 

        $update_stmt = $conn->prepare("UPDATE librarian SET password = ? WHERE username = ?");
        $update_stmt->bind_param("ss", $updatedPassword, $username);

        if ($update_stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Thay đổi mật khẩu tài khoản thành công!"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống, không thể cập nhật cơ sở dữ liệu."]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Mật khẩu hiện tại không chính xác!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Tài khoản không tồn tại hoặc đã bị khóa."]);
}

$stmt->close();
$conn->close(); 
?>