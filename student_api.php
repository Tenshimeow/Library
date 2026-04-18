<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "librarydb.php";

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
            echo json_encode($res ? $res : ["message" => "Khong tim thay sinh vien"]);
        } 
        elseif ($studentname) {
            $search = "%" . $studentname . "%";
            $stmt = $conn->prepare("SELECT * FROM student WHERE studentname LIKE ?");
            $stmt->bind_param("s", $search);
            $stmt->execute();
            $result = $stmt->get_result();     
            $data_list = [];
            while ($row = $result->fetch_assoc()) { $data_list[] = $row; }
            echo json_encode($data_list); 
        } 
        else {

            $result = $conn->query("SELECT * FROM student ORDER BY studentid DESC");
            $data_list = [];
            while ($row = $result->fetch_assoc()) { $data_list[] = $row; }
            echo json_encode($data_list);
        }
        break;


    case 'POST':
        if (empty($data['studentid']) || empty($data['studentname']) || empty($data['email'])) {
            echo json_encode(["error" => "Khong duoc de trong studentid, studentname, email!"]);
            break;
        }

        $check = $conn->prepare("SELECT studentid FROM student WHERE studentid=?");
        $check->bind_param("s", $data['studentid']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(["error" => "Ma sinh vien nay da ton tai!"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO student (studentid, studentname, email, address, gender, birthday, class) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss", 
                $data['studentid'], $data['studentname'], $data['email'], 
                $data['address'], $data['gender'], $data['birthday'], $data['class']
            );

            if ($stmt->execute()) {
                echo json_encode(["message" => "Them sinh vien thanh cong"]);
            } else {
                echo json_encode(["error" => $stmt->error]);
            }
        }
        break;


    case 'PUT':
        if (empty($data['studentid'])) {
            echo json_encode(["error" => "Thieu studentid de cap nhat"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE student SET studentname=?, email=?, address=?, gender=?, birthday=?, class=? WHERE studentid=?");
        $stmt->bind_param("sssssss", 
            $data['studentname'], $data['email'], $data['address'], 
            $data['gender'], $data['birthday'], $data['class'], $data['studentid']
        );

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cap nhat thanh cong"]);
        } else {
            echo json_encode(["error" => $stmt->error]);
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