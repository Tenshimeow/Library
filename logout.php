<?php
session_start();
include("librarydb.php"); 


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $display_name = $_SESSION['display_name'] ?? $username;

    $log_detail = "Thủ thư $display_name ($username) đã đăng xuất khỏi hệ thống.";
    $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'LOGOUT', ?, NOW())");
    $log_stmt->bind_param("ss", $username, $log_detail);
    $log_stmt->execute();
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit();
?>