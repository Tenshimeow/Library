<?php
header("Content-Type: application/json; charset=UTF-8");
include "librarydb.php";

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
        } else {
            $result = $conn->query("SELECT * FROM book ORDER BY bookid DESC");
        }

        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        echo json_encode($books);
        break;


    case 'POST':
        if (!empty($data['bookid']) && !empty($data['bookname'])) {
            $stmt = $conn->prepare("INSERT INTO book(bookid, bookname, author, publisher, category, quantity, available) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssii", $data['bookid'], $data['bookname'], $data['author'], $data['publisher'], $data['category'], $data['quantity'], $data['quantity']);
            
            if ($stmt->execute()) {
                echo json_encode(["message" => "Thêm sách thành công"]);
            } else {
                echo json_encode(["error" => "Lỗi: " . $stmt->error]);
            }
        } else {
            echo json_encode(["error" => "Thiếu dữ liệu đầu vào"]);
        }
        break;


    case 'PUT':
        if (!empty($data['bookid'])) {
            $stmt = $conn->prepare("UPDATE book SET bookname=?, author=?, publisher=?, category=?, quantity=?, available=? WHERE bookid=?");
            $stmt->bind_param("ssssiis", $data['bookname'], $data['author'], $data['publisher'], $data['category'], $data['quantity'], $data['available'], $data['bookid']);
            
            if ($stmt->execute()) {
                echo json_encode(["message" => "Cập nhật thành công"]);
            } else {
                echo json_encode(["error" => "Lỗi cập nhật"]);
            }
        }
        break;

 
    case 'DELETE':
        $bookid = $_GET['bookid'] ?? $data['bookid'] ?? null;
        
        if ($bookid) {
            $stmt = $conn->prepare("DELETE FROM book WHERE bookid=?");
            $stmt->bind_param("s", $bookid);
            if ($stmt->execute()) {
                echo json_encode(["message" => "Xóa thành công"]);
            } else {
                echo json_encode(["error" => "Lỗi xóa"]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method không được phép"]);
}
?>