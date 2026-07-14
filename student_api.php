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
        $studentid = $_GET['studentid'] ?? null;
        $studentname = $_GET['studentname'] ?? null;

        if ($studentid) {
            $stmt = $conn->prepare("SELECT * FROM student WHERE studentid = ?");
            $stmt->bind_param("s", $studentid);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            
            if($res) {
                logAction($myUsername, $myRole, 'ACCESS', "Xem chi tiết thông tin Sinh viên có mã: $studentid");
            }
            echo json_encode($res ? $res : ["message" => "Khong tim thay sinh vien"]);
        } 
        elseif ($studentname) {
            $search = "%" . $studentname . "%";
            $stmt = $conn->prepare("SELECT * FROM student WHERE studentname LIKE ? OR studentid LIKE ? OR class LIKE ?");
            $stmt->bind_param("sss", $search, $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();     
            $data_list = [];
            while ($row = $result->fetch_assoc()) { $data_list[] = $row; }
            
            logAction($myUsername, $myRole, 'SEARCH', "Tìm kiếm Sinh viên với từ khóa: '$studentname'");
            echo json_encode($data_list); 
        } 
        else {
            $result = $conn->query("SELECT * FROM student ORDER BY studentid DESC");
            $data_list = [];
            while ($row = $result->fetch_assoc()) { $data_list[] = $row; }
           
            logAction($myUsername, $myRole, 'ACCESS', "Truy cập Form Quản lý Sinh viên");
            echo json_encode($data_list);
        }
        break;

    case 'POST':
        if (empty($data['studentid']) || empty($data['studentname']) || empty($data['email'])) {
            echo json_encode(["error" => "Khong duoc de trong studentid, studentname, email!"]);
            break;
        }

        if (!is_numeric($data['studentid'])) {
            echo json_encode(["error" => "Mã sinh viên không hợp lệ! Hệ thống chỉ chấp nhận giá trị số nguyên."]);
            break;
        }

        $check = $conn->prepare("SELECT studentid FROM student WHERE studentid=?");
        $check->bind_param("s", $data['studentid']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(["error" => "Ma sinh vien nay da ton tai!"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO student (studentid, studentname, gender, birthday, class, email, address) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss", 
                $data['studentid'], $data['studentname'], $data['gender'], 
                $data['birthday'], $data['class'], $data['email'], $data['address']
            );

            if ($stmt->execute()) {
                logAction(
                    $myUsername, 
                    $myRole, 
                    'INSERT', 
                    "Thêm mới Sinh viên thành công. MSSV: " . $data['studentid'] . " - Họ tên: " . $data['studentname'] . " - Lớp: " . $data['class']
                );
                echo json_encode(["message" => "Them sinh vien thanh cong"]);
            } else {
                echo json_encode(["error" => "Khong the them sinh vien"]);
            }
        }
        break;

    case 'PUT':
        if (empty($data['studentid'])) {
            echo json_encode(["error" => "Thieu studentid de cap nhat"]);
            break;
        }

        if (!is_numeric($data['studentid'])) {
            echo json_encode(["error" => "Mã sinh viên không hợp lệ!"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE student SET studentname=?, gender=?, birthday=?, class=?, email=?, address=? WHERE studentid=?");
        $stmt->bind_param("sssssss", 
            $data['studentname'], $data['gender'], $data['birthday'], 
            $data['class'], $data['email'], $data['address'], $data['studentid']
        );

        if ($stmt->execute()) {
            logAction(
                $myUsername, 
                $myRole, 
                'UPDATE', 
                "Cập nhật thông tin Sinh viên. MSSV: " . $data['studentid'] . " thành: " . $data['studentname'] . " - Lớp: " . $data['class']
            );
            echo json_encode(["message" => "Cap nhat thong tin thanh cong"]);
        } else {
            echo json_encode(["error" => "Cap nhat that bai"]);
        }
        break;

    case 'DELETE':
        $studentid = $_GET['studentid'] ?? $data['studentid'] ?? null;

        if (!$studentid) {
            echo json_encode(["error" => "Thieu studentid de xoa"]);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM student WHERE studentid=?");
        $stmt->bind_param("s", $studentid);

        if ($stmt->execute()) {
            logAction($myUsername, $myRole, 'DELETE', "Xóa vĩnh viễn dữ liệu Sinh viên có mã MSSV: $studentid");
            echo json_encode(["message" => "Xoa sinh vien thanh cong"]);
        } else {
            echo json_encode(["error" => "Xoa that bai"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method khong ho tro"]);
}
?>