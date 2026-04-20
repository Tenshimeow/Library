<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = ""; 

// thêm
if(isset($_POST['insert'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $class = $_POST['class'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if(empty($id) || empty($name) || empty($email)){
        $msg = "Các trường MSSV, Họ tên và Email không được để trống!";
        $msg_type = "error";
    } else {
        $check = $conn->prepare("SELECT studentid FROM student WHERE studentid=?");
        $check->bind_param("s", $id);
        $check->execute();
        if($check->get_result()->num_rows > 0){
            $msg = "Mã số sinh viên này đã tồn tại!";
            $msg_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO student (studentid, studentname, gender, birthday, class, email, address) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss", $id, $name, $gender, $birthday, $class, $email, $address);
            if($stmt->execute()){
                $log_detail = "Đã thêm sinh viên mới: $name (MSSV: $id)";
                $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'THÊM', ?, NOW())");
                $log_stmt->bind_param("ss", $_SESSION['username'], $log_detail);
                $log_stmt->execute();
                header("Location: student.php?status=inserted");
                exit();
            }
        }
    }
}

// sửa
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $class = $_POST['class'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE student SET studentname=?, gender=?, birthday=?, class=?, email=?, address=? WHERE studentid=?");
    $stmt->bind_param("sssssss", $name, $gender, $birthday, $class, $email, $address, $id);
    if($stmt->execute()){
        $log_detail = "Đã cập nhật thông tin sinh viên MSSV: $id";
        $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'SỬA', ?, NOW())");
        $log_stmt->bind_param("ss", $_SESSION['username'], $log_detail);
        $log_stmt->execute();
        header("Location: student.php?status=updated");
        exit();
    }
}

// xóa
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM student WHERE studentid=?");
    $stmt->bind_param("s", $id);
    if($stmt->execute()){
        $log_detail = "Đã xóa hồ sơ sinh viên MSSV: $id";
        $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'XÓA', ?, NOW())");
        $log_stmt->bind_param("ss", $_SESSION['username'], $log_detail);
        $log_stmt->execute();
        header("Location: student.php?status=deleted");
        exit();
    }
}

if(isset($_GET['status'])){
    if($_GET['status'] == 'inserted'){ $msg = "Đã thêm sinh viên mới thành công!"; $msg_type = "success"; }
    if($_GET['status'] == 'deleted'){ $msg = "Đã xóa hồ sơ thành công!"; $msg_type = "success"; }
    if($_GET['status'] == 'updated'){ $msg = "Đã cập nhật thông tin thành công!"; $msg_type = "success"; }
}

// tìm kiếm 
$search = $_GET['search'] ?? "";
if($search != ""){
    $query = "SELECT * FROM student WHERE studentid LIKE ? OR studentname LIKE ? OR class LIKE ?";
    $param = "%$search%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM student ORDER BY studentid DESC");
}

// lấy dữ liệu
$edit = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM student WHERE studentid=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}
?>
 
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sinh viên | Library System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { text-decoration: none; color: #94a3b8; font-size: 14px; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-back:hover { color: #3b82f6; }

        .card { background: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 30px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        h1 { font-size: 26px; margin-bottom: 10px; color: #fff; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; }
        
        input, select { 
            width: 100%; 
            background: #0f172a; 
            border: 1px solid #475569; 
            padding: 12px; 
            border-radius: 8px; 
            color: #fff; 
            outline: none; 
            transition: 0.2s;
            font-size: 14px;
        }
        input[type="date"] { color-scheme: dark; }
        input:focus, select:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        
        .btn { padding: 12px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit { background: #3b82f6; color: white; }
        .btn-submit:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-edit { background: #eab308; color: #000; }
        .btn-edit:hover { background: #ca8a04; }

        .search-container { margin-bottom: 25px; position: relative; }
        .search-container input { padding-left: 45px; background: #1e293b; border-radius: 50px; }
        .search-container i { position: absolute; left: 18px; top: 15px; color: #64748b; }

        .table-wrap { overflow-x: auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        th { text-align: left; padding: 15px; background: rgba(255,255,255,0.05); font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 14px; color: #cbd5e1; }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        .msg { padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-weight: 600; animation: fadeIn 0.3s ease-in; }
        .success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid #f87171; }

        .address-col { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> Quay về trang chủ</a>
        <div style="font-size: 14px; color: #94a3b8">
            Thủ thư: <b style="color: #fff"><?php echo htmlspecialchars($_SESSION['username']); ?></b>
        </div>
    </div>

    <h1>Hồ sơ Sinh viên</h1>
    <p style="color: #64748b; margin-bottom: 30px;">Quản lý thông tin chi tiết và danh sách sinh viên thư viện.</p>

    <?php if($msg != ""): ?>
        <div class="msg <?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 16px; color: #3b82f6;">
            <i class="fa <?php echo $edit ? 'fa-user-pen' : 'fa-user-plus'; ?>"></i>
            <?php echo $edit ? "Cập nhật thông tin: " . $edit['studentid'] : "Đăng ký sinh viên mới"; ?>
        </h3>
        <form method="post">
            <div class="form-grid">
                <div class="input-group">
                    <label>Mã sinh viên (MSSV)</label>
                    <input type="text" name="id" placeholder="VD: 123456" value="<?php echo $edit['studentid'] ?? ''; ?>" <?php echo $edit ? 'readonly style="opacity:0.6; cursor:not-allowed;"' : ''; ?> required>
                </div>
                <div class="input-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="name" placeholder="Nguyễn Văn A" value="<?php echo $edit['studentname'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Giới tính</label>
                    <select name="gender">
                        <option value="Nam" <?php if(($edit['gender'] ?? '')=="Nam") echo "selected"; ?>>Nam</option>
                        <option value="Nữ" <?php if(($edit['gender'] ?? '')=="Nữ") echo "selected"; ?>>Nữ</option>
                        <option value="Khác" <?php if(($edit['gender'] ?? '')=="Khác") echo "selected"; ?>>Khác</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthday" value="<?php echo $edit['birthday'] ?? ''; ?>">
                </div>
                <div class="input-group">
                    <label>Lớp</label>
                    <input type="text" name="class" placeholder="VD: 74DCTT22" value="<?php echo $edit['class'] ?? ''; ?>">
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="example@mail.com" value="<?php echo $edit['email'] ?? ''; ?>" required>
                </div>
            </div>
            <div class="input-group" style="margin-bottom: 25px;">
                <label>Địa chỉ thường trú</label>
                <input type="text" name="address" placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="<?php echo $edit['address'] ?? ''; ?>">
            </div>

            <div style="text-align: right; border-top: 1px solid #334155; padding-top: 20px;">
                <?php if($edit): ?>
                    <a href="student.php" style="color: #94a3b8; margin-right: 20px; text-decoration: none; font-size: 14px;">Hủy bỏ</a>
                    <button type="submit" name="update" class="btn btn-edit"><i class="fa fa-save"></i> Lưu cập nhật</button>
                <?php else: ?>
                    <button type="submit" name="insert" class="btn btn-submit"><i class="fa fa-plus"></i> Thêm sinh viên</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="search-container">
        <form method="get">
            <i class="fa fa-search"></i>
            <input type="text" name="search" placeholder="Tìm kiếm theo mã, tên hoặc lớp..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ và Tên</th>
                    <th>Giới tính</th>
                    <th>Ngày sinh</th>
                    <th>Lớp</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <td><b style="color: #3b82f6;"><?php echo $row['studentid']; ?></b></td>
                        <td style="color: #fff; font-weight: 600;"><?php echo $row['studentname']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td>
                            <?php echo (!empty($row['birthday']) && $row['birthday'] != '0000-00-00') ? date('d/m/Y', strtotime($row['birthday'])) : '---'; ?>
                        </td>
                        <td><span style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; padding: 2px 8px; border-radius: 4px; font-size: 12px;"><?php echo $row['class']; ?></span></td>
                        <td><?php echo $row['email']; ?></td>
                        <td class="address-col" title="<?php echo $row['address']; ?>"><?php echo $row['address']; ?></td>
                        <td style="text-align: center;">
                            <a href="?edit=<?php echo $row['studentid']; ?>" style="color: #eab308; margin-right: 15px;" title="Sửa"><i class="fa fa-pen-to-square"></i></a>
                            <a href="?delete=<?php echo $row['studentid']; ?>" style="color: #f87171;" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này khỏi hệ thống?')" title="Xóa"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">Không tìm thấy dữ liệu sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>