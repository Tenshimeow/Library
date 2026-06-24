<?php
session_start(); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";
include "log.php"; 

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
        if (isset($_GET['key'])) {
            $key = "%" . $_GET['key'] . "%";
            $stmt = $conn->prepare("SELECT * FROM borrow WHERE studentid LIKE ? OR bookid LIKE ?");
            $stmt->bind_param("ss", $key, $key);
            $stmt->execute();
            $result = $stmt->get_result();
            
            logAction($myUsername, $myRole, 'SEARCH', "Tra cứu lịch sử mượn trả với từ khóa: '" . $_GET['key'] . "'");
        } else {
            $result = $conn->query("SELECT * FROM borrow ORDER BY borrowid DESC");
            
            logAction($myUsername, $myRole, 'ACCESS', "Truy cập Form Quản lý Mượn - Trả sách");
        }

        $borrows = [];
        while ($row = $result->fetch_assoc()) {
            $borrows[] = $row;
        }
        echo json_encode($borrows);
        break;

    case 'POST':
        if (empty($data['studentid']) || empty($data['bookid']) || empty($data['due_date'])) {
            echo json_encode(["error" => "Vui lòng nhập đầy đủ Mã sinh viên, Mã sách và Hạn trả!"]);
            break;
        }

        $checkBook = $conn->prepare("SELECT available FROM book WHERE bookid = ?");
        $checkBook->bind_param("s", $data['bookid']);
        $checkBook->execute();
        $bookRes = $checkBook->get_result()->fetch_assoc();

        if (!$bookRes) {
            echo json_encode(["error" => "Sách này không tồn tại trong hệ thống!"]);
            break;
        }

        if ($bookRes['available'] <= 0) {
            echo json_encode(["error" => "Đầu sách này đã được mượn hết, không còn sẵn sàng trong kho!"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO borrow (studentid, bookid, borrow_date, due_date, status) VALUES(?, ?, NOW(), ?, 'BORROWED')");
        $stmt->bind_param("sss", $data['studentid'], $data['bookid'], $data['due_date']);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE book SET available = available - 1 WHERE bookid = '" . $data['bookid'] . "'");

            logAction(
                $myUsername, 
                $myRole, 
                'INSERT', 
                "Tạo phiếu mượn sách thành công. Sinh viên: " . $data['studentid'] . " mượn Mã sách: " . $data['bookid'] . " - Hạn trả: " . $data['due_date']
            );
            echo json_encode(["message" => "Đăng ký mượn sách thành công!"]);
        } else {
            echo json_encode(["error" => "Không thể tạo phiếu mượn sách"]);
        }
        break;

    case 'PUT':
        if (empty($data['borrowid'])) {
            echo json_encode(["error" => "Thiếu mã phiếu mượn (borrowid) để xử lý trả sách"]);
            break;
        }

        $getBorrow = $conn->prepare("SELECT bookid, status FROM borrow WHERE borrowid = ?");
        $getBorrow->bind_param("i", $data['borrowid']);
        $getBorrow->execute();
        $borrowData = $getBorrow->get_result()->fetch_assoc();

        if (!$borrowData || $borrowData['status'] === 'RETURNED') {
            echo json_encode(["error" => "Phiếu mượn không tồn tại hoặc sách đã được trả trước đó!"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE borrow SET status = 'RETURNED', return_date = NOW() WHERE borrowid = ?");
        $stmt->bind_param("i", $data['borrowid']);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE book SET available = available + 1 WHERE bookid = '" . $borrowData['bookid'] . "'");


            logAction(
                $myUsername, 
                $myRole, 
                'UPDATE', 
                "Xử lý TRẢ SÁCH thành công cho phiếu mượn ID: " . $data['borrowid'] . " (Đầu sách: " . $borrowData['bookid'] . " đã hoàn trả về kho)"
            );
            echo json_encode(["message" => "Xác nhận trả sách và hoàn kho thành công!"]);
        } else {
            echo json_encode(["error" => "Cập nhật trạng thái trả sách thất bại"]);
        }
        break;

    case 'DELETE':
        $borrowid = $_GET['borrowid'] ?? $data['borrowid'] ?? null;
        
        if (!$borrowid) {
            echo json_encode(["error" => "Thiếu ID phiếu mượn cần xóa"]);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM borrow WHERE borrowid = ?");
        $stmt->bind_param("i", $borrowid);
        
        if ($stmt->execute()) {
            logAction($myUsername, $myRole, 'DELETE', "Xóa vĩnh viễn phiếu mượn/trả có ID: $borrowid khỏi hệ thống");
            echo json_encode(["message" => "Xóa bản ghi mượn trả thành công"]);
        } else {
            echo json_encode(["error" => "Xóa dữ liệu thất bại"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức HTTP không được hỗ trợ"]);
}
?>