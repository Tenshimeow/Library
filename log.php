<?php
function logAction($username, $role, $action, $detail) {
    $conn = new mysqli("localhost", "root", "", "librarydb");

    $stmt = $conn->prepare(
        "INSERT INTO system_log(username, role, action_type, action_detail) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $username, $role, $action, $detail);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>