<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    // ================= 1. POST: ĐĂNG THÔNG BÁO =================
    case 'POST':
        if (empty($data['username']) || empty($data['note_content'])) {
            echo json_encode(["error" => "Thiếu username hoặc note_content"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO admin_notes (username, note_content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $data['username'], $data['note_content']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Đăng thông báo vào bảng admin_notes thành công!"]);
        } else {
            echo json_encode(["error" => $stmt->error]);
        }
        break;

    // ================= 2. DELETE: XÓA THÔNG BÁO (admin_notes) =================
    case 'DELETE':
        if (empty($data['id']) || empty($data['username'])) {
            echo json_encode(["error" => "Nhập thiếu ID hoặc Username để xóa"]);
            break;
        }

        // Em đổi tên bảng trực tiếp thành admin_notes ở đây cho anh luôn
        $stmt = $conn->prepare("DELETE FROM admin_notes WHERE id = ? AND username = ?");
        $stmt->bind_param("is", $data['id'], $data['username']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Đã xóa thông báo khỏi bảng admin_notes!"]);
        } else {
            echo json_encode(["error" => "Không tìm thấy thông báo để xóa (Kiểm tra lại ID và Username)"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không hỗ trợ"]);
        break;
}
?>