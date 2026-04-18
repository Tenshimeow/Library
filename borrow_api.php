<?php
header("Content-Type: application/json");

// Kết nối DB
include "librarydb.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ================= GET (LẤY + SEARCH) =================
    case 'GET':
        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $sql = "SELECT * FROM borrow 
                    WHERE borrowid LIKE '%$key%' 
                    OR studentid LIKE '%$key%' 
                    OR bookid LIKE '%$key%'";
        } else {
            $sql = "SELECT * FROM borrow";
        }

        $result = $conn->query($sql);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;


    // ================= POST (MƯỢN SÁCH) =================
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $borrowid = $data['borrowid'];
        $studentid = $data['studentid'];
        $bookid = $data['bookid'];
        $date_borrowed = $data['date_borrowed'];

        // check borrowid
        $check = $conn->query("SELECT * FROM borrow WHERE borrowid='$borrowid'");
        if ($check->num_rows > 0) {
            echo json_encode(["error" => "Borrow ID đã tồn tại"]);
            return;
        }

        // check student
        $check = $conn->query("SELECT * FROM student WHERE studentid='$studentid'");
        if ($check->num_rows == 0) {
            echo json_encode(["error" => "Student không tồn tại"]);
            return;
        }

        // check book
        $check = $conn->query("SELECT available FROM book WHERE bookid='$bookid'");
        if ($check->num_rows == 0) {
            echo json_encode(["error" => "Book không tồn tại"]);
            return;
        }

        $row = $check->fetch_assoc();
        if ($row['available'] <= 0) {
            echo json_encode(["error" => "Sách đã hết"]);
            return;
        }

        // insert borrow
        $sql = "INSERT INTO borrow(borrowid, studentid, bookid, date_borrowed, date_return, status)
                VALUES('$borrowid','$studentid','$bookid','$date_borrowed', NULL, 'BORROWING')";

        if ($conn->query($sql)) {

            // trừ sách
            $conn->query("UPDATE book SET available = available - 1 WHERE bookid='$bookid'");

            echo json_encode(["message" => "Mượn sách thành công"]);
        } else {
            echo json_encode(["error" => "Lỗi mượn sách"]);
        }
        break;


    // ================= PUT (TRẢ SÁCH) =================
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $borrowid = $data['borrowid'];
        $date_return = $data['date_return'];

        // check borrow
        $check = $conn->query("SELECT * FROM borrow WHERE borrowid='$borrowid'");
        if ($check->num_rows == 0) {
            echo json_encode(["error" => "Borrow không tồn tại"]);
            return;
        }

        $row = $check->fetch_assoc();

        if ($row['status'] == "RETURNED") {
            echo json_encode(["error" => "Sách đã trả rồi"]);
            return;
        }

        $bookid = $row['bookid'];

        // update borrow
        $sql = "UPDATE borrow 
                SET date_return='$date_return', status='RETURNED'
                WHERE borrowid='$borrowid'";

        if ($conn->query($sql)) {

            // cộng lại sách
            $conn->query("UPDATE book SET available = available + 1 WHERE bookid='$bookid'");

            echo json_encode(["message" => "Trả sách thành công"]);
        } else {
            echo json_encode(["error" => "Lỗi trả sách"]);
        }
        break;


    // ================= DELETE (XÓA) =================
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);

        $borrowid = $data['borrowid'];

        // check trạng thái
        $check = $conn->query("SELECT status FROM borrow WHERE borrowid='$borrowid'");
        if ($check->num_rows == 0) {
            echo json_encode(["error" => "Không tồn tại"]);
            return;
        }

        $row = $check->fetch_assoc();
        if ($row['status'] == "BORROWING") {
            echo json_encode(["error" => "Chưa trả sách, không thể xóa"]);
            return;
        }

        $sql = "DELETE FROM borrow WHERE borrowid='$borrowid'";

        if ($conn->query($sql)) {
            echo json_encode(["message" => "Xóa thành công"]);
        } else {
            echo json_encode(["error" => "Lỗi xóa"]);
        }
        break;

    default:
        echo json_encode(["error" => "Method không hỗ trợ"]);
}
?>