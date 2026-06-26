<?php
function logAction($username, $role, $action, $detail) {
    // Kết nối đến CSDL (Cổng 3307 theo cấu hình của anh)
    $conn = new mysqli("localhost", "root", "", "librarydb", 3307);

    if ($conn->connect_error) {
        return false;
    }

    // Đảm bảo ghi nhận tiếng Việt có dấu không bị lỗi font trong DB
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare(
        "INSERT INTO system_log(username, role, action_type, action_detail, action_time) 
         VALUES (?, ?, ?, ?, NOW())"
    );

    if(!$stmt){
        $conn->close();
        return false;
    }

    $stmt->bind_param("ssss", $username, $role, $action, $detail);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}
?>