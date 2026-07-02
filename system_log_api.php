<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

if(!isset($_SESSION['username'])){
    http_response_code(401);
    echo json_encode(["error" => "Yêu cầu quyền truy cập tài khoản hợp lệ từ hệ thống!"]);
    exit;
}

$myUsername = $_SESSION['username'];
$myRole = $_SESSION['role'];
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        $notes_res = $conn->query("SELECT * FROM admin_notes ORDER BY created_at DESC LIMIT 10");
        $notes_list = [];
        while ($nRow = $notes_res->fetch_assoc()) { $notes_list[] = $nRow; }

        
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $search_param = "%$search%";
            $logs_query = "SELECT l.*, lb.role as user_role 
                           FROM system_log l 
                           LEFT JOIN librarian lb ON l.username = lb.username 
                           WHERE l.username LIKE ? OR l.action_type LIKE ? OR l.action_detail LIKE ?
                           ORDER BY l.action_time DESC LIMIT 100";
            $stmt = $conn->prepare($logs_query);
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        } else {
            $logs_query = "SELECT l.*, lb.role as user_role 
                           FROM system_log l 
                           LEFT JOIN librarian lb ON l.username = lb.username 
                           ORDER BY l.action_time DESC LIMIT 100";
            $stmt = $conn->prepare($logs_query);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $logs_list = [];
        while ($lRow = $result->fetch_assoc()) { $logs_list[] = $lRow; }

        
        echo json_encode([
            "notes" => $notes_list,
            "logs" => $logs_list
        ]);
        break;

    case 'POST':
        if ($myRole !== 'ADMIN') {
            echo json_encode(["error" => "Lỗi: Tài khoản của bạn không có thẩm quyền thực hiện tác vụ này!"]);
            break;
        }

        if (empty($data['note_content'])) {
            echo json_encode(["error" => "Nội dung dòng thông báo không được bỏ trống!"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO admin_notes (username, note_content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $myUsername, $data['note_content']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Đã gửi thông báo nội bộ thành công!"]);
        } else {
            echo json_encode(["error" => "Lỗi CSDL không thể thực thi."]);
        }
        break;

    case 'DELETE':
        if ($myRole !== 'ADMIN') {
            echo json_encode(["error" => "Lỗi phân quyền: Tác vụ yêu cầu tài khoản quản trị viên!"]);
            break;
        }

        $target = $data['target'] ?? ''; 
        $id = intval($data['id'] ?? 0);

        if ($id <= 0 || empty($target)) {
            echo json_encode(["error" => "Thông tin tham số định danh yêu cầu không hợp lệ!"]);
            break;
        }

        if ($target === 'note') {
            $stmt = $conn->prepare("DELETE FROM admin_notes WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "Đã gỡ bỏ dòng thông báo thành công!"]);
            } else {
                echo json_encode(["error" => "Lỗi CSDL khi xóa tin thông báo."]);
            }
        } elseif ($target === 'log') {
            $stmt = $conn->prepare("DELETE FROM system_log WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "Đã xóa bản ghi nhật ký hoạt động thành công!"]);
            } else {
                echo json_encode(["error" => "Lỗi CSDL khi gỡ bản ghi nhật ký hoạt động."]);
            }
        } else {
            echo json_encode(["error" => "Mục tiêu xử lý dữ liệu không hợp lệ."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức HTTP hiện tại không được hỗ trợ!"]);
}
?>