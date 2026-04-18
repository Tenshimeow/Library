<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ================= GET: TRUY VẤN NHÂN VIÊN =================
    case 'GET':
        if (isset($_GET['key'])) {
            $key = "%" . $_GET['key'] . "%";
            $stmt = $conn->prepare("SELECT * FROM librarian WHERE librarianid LIKE ? OR librarianname LIKE ? OR email LIKE ? ORDER BY status ASC, librarianid DESC");
            $stmt->bind_param("sss", $key, $key, $key);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query("SELECT * FROM librarian ORDER BY status ASC, librarianid DESC");
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        break;


    // ================= POST: THÊM THỦ THƯ MỚI =================
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        // 1. Kiểm tra dữ liệu trống
        if (empty($data['librarianid']) || empty($data['librarianname']) || empty($data['username'])) {
            echo json_encode(["error" => "Vui lòng nhập đầy đủ các trường bắt buộc"]);
            break;
        }

        // 2. Kiểm tra định dạng Email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["error" => "Định dạng email không hợp lệ"]);
            break;
        }

        // 3. Kiểm tra số điện thoại (Regex cho 9-11 số)
        if (!preg_match("/^[0-9]{9,11}$/", $data['phone'])) {
            echo json_encode(["error" => "Số điện thoại phải chứa từ 9 đến 11 chữ số"]);
            break;
        }

        // 4. Kiểm tra trùng ID
        $id = $data['librarianid'];
        $check = $conn->query("SELECT librarianid FROM librarian WHERE librarianid='$id'");
        if ($check->num_rows > 0) {
            echo json_encode(["error" => "Mã nhân viên này đã tồn tại trên hệ thống"]);
            break;
        }

        // 5. Thực thi Insert
        $stmt = $conn->prepare("INSERT INTO librarian (librarianid, librarianname, email, address, phone, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", 
            $data['librarianid'], $data['librarianname'], $data['email'], 
            $data['address'], $data['phone'], $data['username'], 
            $data['password'], $data['role'], $data['status']
        );

        if ($stmt->execute()) {
            echo json_encode(["message" => "Đã cấp quyền truy cập cho thủ thư mới thành công!"]);
        } else {
            echo json_encode(["error" => "Lỗi hệ thống, không thể tạo tài khoản"]);
        }
        break;


    // ================= PUT: CẬP NHẬT HỒ SƠ =================
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['librarianid'] ?? '';

        if (empty($id)) {
            echo json_encode(["error" => "Thiếu mã nhân viên để cập nhật"]);
            break;
        }

        // Validation tương tự POST
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["error" => "Email không hợp lệ"]);
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
            echo json_encode(["error" => "Cập nhật thất bại"]);
        }
        break;


    // ================= DELETE: VÔ HIỆU HÓA (SOFT DELETE) =================
    case 'DELETE':
        // Lấy ID từ URL parameter ?librarianid=STAFF-01
        $id = $_GET['librarianid'] ?? '';

        if (empty($id)) {
            echo json_encode(["error" => "Cần cung cấp mã nhân viên để thực hiện"]);
            break;
        }

        // Thay vì xóa vĩnh viễn, ta chuyển trạng thái thành INACTIVE giống logic file gốc của bạn
        $stmt = $conn->prepare("UPDATE librarian SET status='INACTIVE' WHERE librarianid=?");
        $stmt->bind_param("s", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Tài khoản nhân viên đã được vô hiệu hóa"]);
        } else {
            echo json_encode(["error" => "Không thể xử lý yêu cầu"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không được hỗ trợ"]);
        break;
}
?>