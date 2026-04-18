<?php
session_start();
include("librarydb.php");

// Kiểm tra đăng nhập
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $username = $_SESSION['username'];
    $file = $_FILES['avatar'];

    // 1. Tạo thư mục uploads nếu chưa có
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 2. Kiểm tra thông tin file
    $file_name = $file['name'];
    $file_tmp  = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Các định dạng ảnh được phép
    $allowed = array("jpg", "jpeg", "png", "gif");

    if (in_array($file_ext, $allowed)) {
        // Kiểm tra dung lượng (giới hạn 2MB cho nhẹ hệ thống)
        if ($file_size <= 2 * 1024 * 1024) {
            
            // 3. Đặt tên file theo username để cố định link ảnh
            // Em dùng đuôi .jpg làm chuẩn để Dashboard dễ gọi
            $new_file_name = $username . ".jpg";
            $target_file = $target_dir . $new_file_name;

            // 4. Di chuyển file vào thư mục uploads
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Thành công: Quay về trang chủ kèm thông báo
                header("Location: index.php?upload=success");
            } else {
                echo "<script>alert('Lỗi: Không thể lưu file!'); window.location='index.php';</script>";
            }
        } else {
            echo "<script>alert('Lỗi: File quá lớn (Tối đa 2MB)!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Lỗi: Chỉ chấp nhận file ảnh (JPG, PNG, GIF)!'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>