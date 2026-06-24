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
            $stmt = $conn->prepare("SELECT * FROM book WHERE bookid LIKE ? OR bookname LIKE ? OR author LIKE ?");
            $stmt->bind_param("sss", $key, $key, $key);
            $stmt->execute();
            $result = $stmt->get_result();
            
            logAction($myUsername, $myRole, 'SEARCH', "Tìm kiếm sách trong kho với từ khóa: '" . $_GET['key'] . "'");
        } else {
            $result = $conn->query("SELECT * FROM book ORDER BY bookid DESC");
            
            logAction($myUsername, $myRole, 'ACCESS', "Truy cập Form Quản lý Sách");
        }

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        echo json_encode($books);
        break;

    case 'POST':
        if (empty($data['bookid']) || empty($data['bookname'])) {
            echo json_encode(["error" => "Vui lòng nhập đầy đủ Mã sách và Tên sách!"]);
            break;
        }

        $check = $conn->prepare("SELECT bookid FROM book WHERE bookid=?");
        $check->bind_param("s", $data['bookid']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(["error" => "Mã sách này đã tồn tại trong kho!"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO book (bookid, bookname, author, publisher, category, quantity, available) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssii", 
                $data['bookid'], $data['bookname'], $data['author'], 
                $data['publisher'], $data['category'], $data['quantity'], $data['quantity']
            );
            
            if ($stmt->execute()) {
                logAction(
                    $myUsername, 
                    $myRole, 
                    'INSERT', 
                    "Thêm mới đầu sách vào kho. Mã sách: " . $data['bookid'] . " - Tên sách: " . $data['bookname'] . " - Số lượng: " . $data['quantity']
                );
                echo json_encode(["message" => "Thêm sách vào kho thành công"]);
            } else {
                echo json_encode(["error" => "Không thể thêm sách vào hệ thống"]);
            }
        }
        break;

    case 'PUT':
        if (empty($data['bookid'])) {
            echo json_encode(["error" => "Thiếu mã định danh dữ liệu đầu sách (bookid)"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE book SET bookname=?, author=?, publisher=?, category=?, quantity=?, available=? WHERE bookid=?");
        $stmt->bind_param("ssssiis", 
            $data['bookname'], $data['author'], $data['publisher'], 
            $data['category'], $data['quantity'], $data['available'], $data['bookid']
        );
        
        if ($stmt->execute()) {
            logAction(
                $myUsername, 
                $myRole, 
                'UPDATE', 
                "Cập nhật thông tin đầu sách. Mã sách: " . $data['bookid'] . " thành: " . $data['bookname'] . " [Tổng kho: " . $data['quantity'] . " - Sẵn sàng: " . $data['available'] . "]"
            );
            echo json_encode(["message" => "Cập nhật thông tin sách thành công"]);
        } else {
            echo json_encode(["error" => "Cập nhật dữ liệu thất bại"]);
        }
        break;

    case 'DELETE':
        $bookid = $_GET['bookid'] ?? $data['bookid'] ?? null;
        
        if (!$bookid) {
            echo json_encode(["error" => "Thiếu thông tin mã sách cần xóa"]);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM book WHERE bookid=?");
        $stmt->bind_param("s", $bookid);
        
        if ($stmt->execute()) {
            logAction($myUsername, $myRole, 'DELETE', "Xóa vĩnh viễn dữ liệu đầu sách có mã: $bookid khỏi hệ thống");
            echo json_encode(["message" => "Xóa sách khỏi hệ thống thành công"]);
        } else {
            echo json_encode(["error" => "Xóa dữ liệu thất bại"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức HTTP không được hỗ trợ"]);
}
?>