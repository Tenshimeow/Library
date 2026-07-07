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
        $baseSql = "SELECT b.*, s.studentname, bk.bookname 
                    FROM borrow b
                    INNER JOIN student s ON b.studentid = s.studentid
                    INNER JOIN book bk ON b.bookid = bk.bookid";

        if (isset($_GET['key'])) {
            $key = "%" . $_GET['key'] . "%";
            $stmt = $conn->prepare("$baseSql WHERE b.studentid LIKE ? OR b.bookid LIKE ? OR s.studentname LIKE ? OR bk.bookname LIKE ? OR b.borrowid LIKE ? ORDER BY b.borrowid DESC");
            $stmt->bind_param("sssss", $key, $key, $key, $key, $key);
            $stmt->execute();
            $result = $stmt->get_result();
            
            logAction($myUsername, $myRole, 'SEARCH', "Tra cứu lịch sử mượn trả với từ khóa: '" . $_GET['key'] . "'");
        } else {
            $result = $conn->query("$baseSql ORDER BY b.borrowid DESC");
            logAction($myUsername, $myRole, 'ACCESS', "Truy cập Form Quản lý Mượn - Trả sách");
        }

        $borrows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $borrows[] = $row;
            }
        }
        echo json_encode($borrows);
        break;

    case 'POST':
        if (empty($data['borrowid']) || empty($data['studentid']) || empty($data['bookid']) || empty($data['date_borrowed'])) {
            echo json_encode(["error" => "Vui lòng nhập đầy đủ Mã lượt mượn, Mã sinh viên, Mã sách và Ngày mượn!"]);
            break;
        }

        if (!is_numeric($data['borrowid']) || !is_numeric($data['studentid'])) {
            echo json_encode(["error" => "Mã lượt mượn và Mã sinh viên phải là định dạng số nguyên hợp lệ!"]);
            break;
        }

        $checkId = $conn->prepare("SELECT borrowid FROM borrow WHERE borrowid = ?");
        $checkId->bind_param("i", $data['borrowid']);
        $checkId->execute();
        if ($checkId->get_result()->num_rows > 0) {
            echo json_encode(["error" => "Mã lượt mượn này đã tồn tại trên hệ thống!"]);
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

        $stmt = $conn->prepare("INSERT INTO borrow (borrowid, studentid, bookid, date_borrowed, date_return, status) VALUES(?, ?, ?, ?, NULL, 'BORROWING')");
        $stmt->bind_param("iiss", $data['borrowid'], $data['studentid'], $data['bookid'], $data['date_borrowed']);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE book SET available = available - 1 WHERE bookid = '" . $conn->real_escape_string($data['bookid']) . "'");

            logAction(
                $myUsername, 
                $myRole, 
                'INSERT', 
                "Tạo phiếu mượn sách thành công. Lượt mượn: " . $data['borrowid'] . " - SV: " . $data['studentid'] . " mượn Mã sách: " . $data['bookid']
            );
            echo json_encode(["message" => "Đăng ký mượn sách thành công!"]);
        } else {
            echo json_encode(["error" => "Không thể tạo phiếu mượn sách. Lỗi xung đột dữ liệu!"]);
        }
        break;

    case 'PUT':
        if (empty($data['borrowid']) || empty($data['date_return'])) {
            echo json_encode(["error" => "Thiếu mã phiếu mượn (borrowid) hoặc Ngày trả thực tế để xử lý"]);
            break;
        }

        if (!is_numeric($data['borrowid'])) {
            echo json_encode(["error" => "Mã lượt mượn không hợp lệ!"]);
            break;
        }

        $getBorrow = $conn->prepare("SELECT bookid, status FROM borrow WHERE borrowid = ?");
        $getBorrow->bind_param("i", $data['borrowid']);
        $getBorrow->execute();
        $borrowData = $getBorrow->get_result()->fetch_assoc();

        if (!$borrowData) {
            echo json_encode(["error" => "Phiếu mượn không tồn tại!"]);
            break;
        }
        if ($borrowData['status'] === 'RETURNED') {
            echo json_encode(["error" => "Sách của phiếu mượn này đã được hoàn trả từ trước!"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE borrow SET status = 'RETURNED', date_return = ? WHERE borrowid = ?");
        $stmt->bind_param("si", $data['date_return'], $data['borrowid']);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE book SET available = available + 1 WHERE bookid = '" . $conn->real_escape_string($borrowData['bookid']) . "'");

            logAction(
                $myUsername, 
                $myRole, 
                'UPDATE', 
                "Xử lý TRẢ SÁCH thành công cho phiếu mượn ID: " . $data['borrowid']
            );
            echo json_encode(["message" => "Xác nhận trả sách và hoàn kho thành công!"]);
        } else {
            echo json_encode(["error" => "Cập nhật trạng thái trả sách thất bại"]);
        }
        break;

    case 'DELETE':
        $borrowid = $_GET['borrowid'] ?? $data['borrowid'] ?? null;
        
        if (!$borrowid || !is_numeric($borrowid)) {
            echo json_encode(["error" => "Thiếu ID phiếu mượn hoặc ID không hợp lệ"]);
            break;
        }

        $getBorrow = $conn->prepare("SELECT bookid, status FROM borrow WHERE borrowid = ?");
        $getBorrow->bind_param("i", $borrowid);
        $getBorrow->execute();
        $borrowData = $getBorrow->get_result()->fetch_assoc();

        if ($borrowData) {
            if ($borrowData['status'] === 'BORROWING') {
                $conn->query("UPDATE book SET available = available + 1 WHERE bookid = '" . $conn->real_escape_string($borrowData['bookid']) . "'");
            }

            $stmt = $conn->prepare("DELETE FROM borrow WHERE borrowid = ?");
            $stmt->bind_param("i", $borrowid);
            
            if ($stmt->execute()) {
                logAction($myUsername, $myRole, 'DELETE', "Xóa vĩnh viễn phiếu mượn/trả có ID: $borrowid khỏi hệ thống");
                echo json_encode(["message" => "Xóa bản ghi mượn trả thành công"]);
            } else {
                echo json_encode(["error" => "Xóa dữ liệu thất bại"]);
            }
        } else {
            echo json_encode(["error" => "Phiếu mượn không tồn tại"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức HTTP không được hỗ trợ"]);
}
?>