<?php
session_start();
include("librarydb.php");

// Kiểm tra đăng nhập
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // lấy role
$message = "";
$status = ""; 

if (isset($_POST['change'])) {
    $old = trim($_POST['old_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    if(empty($old) || empty($new) || empty($confirm)){
        $message = "Vui lòng nhập đầy đủ các trường!";
        $status = "error";
    } elseif($new !== $confirm){
        $message = "Mật khẩu xác nhận không khớp!";
        $status = "error";
    } elseif(strlen($new) < 6){
        $message = "Mật khẩu mới phải từ 6 ký tự trở lên!";
        $status = "error";
    } else {

        // 🔥 FIX: kiểm tra theo username + role
        $sql = "SELECT password FROM librarian WHERE username = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ss", $username, $role);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($row = $result->fetch_assoc()){
                
                // So sánh mật khẩu cũ
                if($old === $row['password']){
                    
                    // Update mật khẩu
                    $update_sql = "UPDATE librarian SET password = ? WHERE username = ? AND role = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("sss", $new, $username, $role);
                    
                    if($update_stmt->execute()){
                        $message = "Chúc mừng! Tài khoản <b>$username</b> đã đổi mật khẩu thành công.";
                        $status = "success";
                    } else {
                        $message = "Lỗi cập nhật: " . $conn->error;
                        $status = "error";
                    }
                } else {
                    $message = "Mật khẩu cũ không chính xác!";
                    $status = "error";
                }
            } else {
                $message = "Lỗi: Không tìm thấy tài khoản '$username' trong hệ thống!";
                $status = "error";
            }
        } else {
            $message = "Lỗi truy vấn SQL: " . $conn->error;
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu | Hệ thống thư viện</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #0f172a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #1e293b; padding: 30px; border-radius: 12px; width: 380px; border: 1px solid #334155; box-shadow: 0 15px 30px rgba(0,0,0,0.4); }
        .header-info { background: #312e81; padding: 12px; border-radius: 8px; margin-bottom: 25px; text-align: center; border-left: 4px solid #818cf8; }
        h2 { margin: 0 0 15px 0; font-size: 22px; text-align: center; color: #818cf8; letter-spacing: 0.5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 12px; color: #94a3b8; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; box-sizing: border-box; outline: none; transition: 0.2s; }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); }
        .btn-save { width: 100%; padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 15px; margin-top: 10px; transition: 0.3s; }
        .btn-save:hover { background: #4338ca; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 13px; font-weight: 500; border: 1px solid; }
        .alert-error { background: rgba(220, 38, 38, 0.2); color: #fca5a5; border-color: #991b1b; }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: #a7f3d0; border-color: #065f46; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 13px; }
        .back-link:hover { color: #94a3b8; }
    </style>
</head>
<body>

<div class="card">
    <div class="header-info">
        <i class="fa fa-id-badge"></i> Xin chào: <b><?php echo htmlspecialchars($username); ?></b> (<?php echo htmlspecialchars($role); ?>)
    </div>

    <h2><i class="fa fa-sync-alt"></i> Cập nhật mật khẩu</h2>

    <?php if($message): ?>
        <div class="alert alert-<?php echo $status; ?>">
            <i class="fa <?php echo ($status == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>MẬT KHẨU HIỆN TẠI</label>
            <input type="password" name="old_password" required placeholder="VD: 999999">
        </div>
        <div class="form-group">
            <label>MẬT KHẨU MỚI</label>
            <input type="password" name="new_password" required placeholder="VD:123456">
        </div>
        <div class="form-group">
            <label>XÁC NHẬN MẬT KHẨU MỚI</label>
            <input type="password" name="confirm_password" required placeholder="VD:123456">
        </div>
        <button type="submit" name="change" class="btn-save">XÁC NHẬN THAY ĐỔI</button>
    </form>

    <a href="index.php" class="back-link"><i class="fa fa-chevron-left"></i> Quay lại trang chủ</a>
</div>

</body>
</html>