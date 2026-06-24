<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        $key = $_GET['key'] ?? null;
        if ($key) {
            $search = "%$key%";
            $stmt = $conn->prepare("SELECT * FROM librarian WHERE librarianid LIKE ? OR librarianname LIKE ? OR email LIKE ? ORDER BY status ASC, librarianid DESC");
            $stmt->bind_param("sss", $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT * FROM librarian ORDER BY status ASC, librarianid DESC");
        }

        $list = [];
        while ($row = $result->fetch_assoc()) { $list[] = $row; }
        echo json_encode($list);
        break;

    case 'POST':
        if (empty($data['librarianid']) || empty($data['librarianname']) || empty($data['username'])) {
            echo json_encode(["error" => "Vui lòng nhập đầy đủ Mã thủ thư, Họ tên và Tên đăng nhập"]);
            break;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["error" => "Định dạng email liên hệ không hợp lệ"]);
            break;
        }
        if (!isset($data['phone']) || !preg_match("/^[0-9]{9,11}$/", $data['phone'])) {
            echo json_encode(["error" => "Số điện thoại phải từ 9 đến 11 chữ số"]);
            break;
        }

        $stmt = $conn->prepare("SELECT librarianid FROM librarian WHERE librarianid = ?");
        $stmt->bind_param("s", $data['librarianid']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(["error" => "ID nhân viên này đã tồn tại trên hệ thống!"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO librarian (librarianid, librarianname, email, address, phone, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", 
            $data['librarianid'], $data['librarianname'], $data['email'], 
            $data['address'], $data['phone'], $data['username'], 
            $data['password'], $data['role'], $data['status']
        );

        if ($stmt->execute()) {
            echo json_encode(["message" => "Đã đăng ký thủ thư mới thành công!"]);
        } else {
            echo json_encode(["error" => "Lỗi hệ thống không thể xử lý: " . $stmt->error]);
        }
        break;

    case 'PUT':
        $id = $data['librarianid'] ?? '';
        if (empty($id)) {
            echo json_encode(["error" => "Thiếu thông tin mã nhân viên để thực hiện cập nhật"]);
            break;
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["error" => "Định dạng email không hợp lệ"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE librarian SET librarianname=?, email=?, address=?, phone=?, username=?, password=?, role=?, status=? WHERE librarianid=?");
        $stmt->bind_param("sssssssss", 
            $data['librarianname'], $data['email'], $data['address'], 
            $data['phone'], $data['username'], $data['password'], 
            $data['role'], $data['status'], $id
        );

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cập nhật hồ sơ nhân sự thành công!"]);
        } else {
            echo json_encode(["error" => "Cập nhật dữ liệu thất bại: " . $stmt->error]);
        }
        break;

    case 'DELETE':
        $id = $_GET['librarianid'] ?? $data['librarianid'] ?? '';

        if (empty($id)) {
            echo json_encode(["error" => "Cần cung cấp mã nhân viên để thực hiện khóa"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE librarian SET status='INACTIVE' WHERE librarianid=?");
        $stmt->bind_param("s", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Đã tạm khóa tài khoản nhân sự thành công!"]);
        } else {
            echo json_encode(["error" => "Không thể xử lý vô hiệu hóa tài khoản"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức HTTP không được hỗ trợ"]);
}
?>