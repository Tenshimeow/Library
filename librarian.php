<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = "";

if(isset($_POST['add'])){
    $id = $_POST['librarianid'];
    $name = $_POST['librarianname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $address = $_POST['address'];

    $check = $conn->prepare("SELECT librarianid FROM librarian WHERE librarianid=?");
    $check->bind_param("s", $id);
    $check->execute();
    if($check->get_result()->num_rows > 0){
        $msg = "ID nhân viên này đã tồn tại!"; 
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO librarian (librarianid, librarianname, email, address, phone, username, password, role, status) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssss", $id, $name, $email, $address, $phone, $user, $pass, $role, $status);
        if($stmt->execute()){
            $msg = "Đã đăng ký thủ thư mới thành công!"; 
            $msg_type = "success";
        } else {
            $msg = "Lỗi: " . $stmt->error;
            $msg_type = "error";
        }
    }
}

if(isset($_POST['update'])){
    $id = $_POST['librarianid'];
    $stmt = $conn->prepare("UPDATE librarian SET librarianname=?, email=?, address=?, phone=?, username=?, password=?, role=?, status=? WHERE librarianid=?");
    $stmt->bind_param("sssssssss", $_POST['librarianname'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['username'], $_POST['password'], $_POST['role'], $_POST['status'], $id);
    if($stmt->execute()){
        $msg = "Cập nhật hồ sơ nhân sự thành công!"; 
        $msg_type = "success";
    } else {
        $msg = "Lỗi cập nhật!";
        $msg_type = "error";
    }
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("UPDATE librarian SET status='INACTIVE' WHERE librarianid=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    header("Location: librarian.php?msg=deactivated");
    exit();
}

if(isset($_GET['msg']) && $_GET['msg'] == 'deactivated'){
    $msg = "Đã tạm khóa tài khoản nhân sự!";
    $msg_type = "success";
}

$edit_data = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM librarian WHERE librarianid=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}

$search = $_GET['search'] ?? "";
if($search != ""){
    $query = "SELECT * FROM librarian WHERE librarianname LIKE ? OR librarianid LIKE ? ORDER BY status ASC";
    $stmt = $conn->prepare($query);
    $param = "%$search%";
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result_list = $stmt->get_result();
} else {
    $result_list = $conn->query("SELECT * FROM librarian ORDER BY status ASC, librarianid DESC");
}

$suggest_res = $conn->query("SELECT librarianid, librarianname FROM librarian");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hệ thống Thủ thư</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; padding: 40px 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { text-decoration: none; color: #94a3b8; font-size: 14px; transition: 0.2s; }
        .btn-back:hover { color: #3b82f6; }
        .card { background: #1e293b; padding: 25px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 30px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        h1 { font-size: 26px; margin-bottom: 20px; color: #fff; font-weight: 700; }
        h3 { font-size: 16px; margin-bottom: 20px; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; }
        
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; }
        input, select { width: 100%; background: #0f172a; border: 1px solid #475569; padding: 12px; border-radius: 8px; color: #fff; outline: none; font-size: 14px; transition: 0.3s; }
        input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        
        .btn-row { display: flex; gap: 10px; margin-top: 10px; }
        .btn { padding: 12px 25px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-submit { background: #3b82f6; color: white; flex: 1; }
        .btn-submit:hover { background: #2563eb; }
        .btn-cancel { background: #334155; color: white; text-decoration: none; text-align: center; line-height: 1.5; padding: 12px 25px; }
        
        .search-container { margin-bottom: 20px; position: relative; }
        .search-container input { padding-left: 40px; background: #1e293b; border-radius: 30px; border: 1px solid #334155; }
        .search-container i { position: absolute; left: 15px; top: 15px; color: #64748b; }
        
        .table-wrap { overflow-x: auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { text-align: left; padding: 15px; background: rgba(255,255,255,0.03); font-size: 13px; color: #94a3b8; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 14px; vertical-align: middle; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .status-active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-inactive { background: rgba(239, 68, 68, 0.1); color: #f87171; }
        .role-badge { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
        
        .msg { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; animation: fadeIn 0.5s; }
        .success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid #f87171; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> Quay về trang chủ</a>
        <div style="font-size: 14px; color: #94a3b8">
            Đang đăng nhập: <b style="color: #3b82f6"><?php echo $_SESSION['username']; ?></b>
        </div>
    </div>

    <h1>Quản lý Nhân sự Thủ thư</h1>

    <?php if($msg != ""): ?>
        <div class="msg <?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card">
        <h3><i class="fa <?php echo $edit_data ? 'fa-user-edit' : 'fa-user-plus'; ?>"></i> 
            <?php echo $edit_data ? 'Hiệu chỉnh thông tin' : 'Thêm thủ thư mới'; ?>
        </h3>
        <form method="POST">
            <div class="form-grid">
                <div class="input-group">
                    <label>Mã Thủ thư (Cố định)</label>
                    <input type="text" name="librarianid" placeholder="VD: 001" value="<?php echo $edit_data['librarianid'] ?? ''; ?>" <?php echo $edit_data ? 'readonly' : 'required'; ?>>
                </div>
                <div class="input-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="librarianname" placeholder="Nguyễn Văn B" value="<?php echo $edit_data['librarianname'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Email liên hệ</label>
                    <input type="email" name="email" placeholder="example@library.com" value="<?php echo $edit_data['email'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" placeholder="09xxx..." value="<?php echo $edit_data['phone'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Tên đăng nhập hệ thống</label>
                    <input type="text" name="username" placeholder="user123" value="<?php echo $edit_data['username'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Mật khẩu đăng nhập</label>
                    <input type="text" name="password" placeholder="999999" value="<?php echo $edit_data['password'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Phân quyền hệ thống</label>
                    <select name="role">
                        <option value="LIBRARIAN" <?php echo (isset($edit_data) && $edit_data['role']=='LIBRARIAN')?'selected':''; ?>>Thủ thư (Librarian)</option>
                        <option value="ADMIN" <?php echo (isset($edit_data) && $edit_data['role']=='ADMIN')?'selected':''; ?>>Quản trị viên (Admin)</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Trạng thái tài khoản</label>
                    <select name="status">
                        <option value="ACTIVE" <?php echo (isset($edit_data) && $edit_data['status']=='ACTIVE')?'selected':''; ?>>Đang hoạt động</option>
                        <option value="INACTIVE" <?php echo (isset($edit_data) && $edit_data['status']=='INACTIVE')?'selected':''; ?>>Tạm khóa</option>
                    </select>
                </div>
                <div class="input-group" style="grid-column: span 2;">
                    <label>Địa chỉ thường trú</label>
                    <input type="text" name="address" placeholder="Địa chỉ chi tiết..." value="<?php echo $edit_data['address'] ?? ''; ?>">
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" name="<?php echo $edit_data ? 'update' : 'add'; ?>" class="btn btn-submit">
                    <i class="fa fa-save"></i> <?php echo $edit_data ? 'Lưu thay đổi' : 'Xác nhận thêm mới'; ?>
                </button>
                <?php if($edit_data): ?>
                    <a href="librarian.php" class="btn btn-cancel">Hủy bỏ</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="search-container">
        <form method="get">
            <i class="fa fa-search"></i>
            <input type="text" name="search" list="staff_hints" placeholder="Tìm kiếm theo tên hoặc mã nhân viên..." value="<?php echo htmlspecialchars($search); ?>">
            <datalist id="staff_hints">
                <?php while($s = $suggest_res->fetch_assoc()): ?>
                    <option value="<?php echo $s['librarianid']; ?>"><?php echo $s['librarianname']; ?></option>
                <?php endwhile; ?>
            </datalist>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Liên hệ</th>
                    <th>Tài khoản</th>
                    <th>Mật khẩu</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result_list->num_rows > 0): ?>
                    <?php while($row = $result_list->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #fff;"><?php echo $row['librarianname']; ?></div>
                            <div style="font-size: 11px; color: #3b82f6; font-family: monospace;"><?php echo $row['librarianid']; ?></div>
                        </td>
                        <td>
                            <div style="font-size: 13px;"><i class="fa fa-envelope" style="font-size: 10px; color: #64748b;"></i> <?php echo $row['email']; ?></div>
                            <div style="font-size: 12px; color: #94a3b8;"><i class="fa fa-phone" style="font-size: 10px; color: #64748b;"></i> <?php echo $row['phone']; ?></div>
                        </td>
                        <td><code style="color: #e2e8f0; background: #0f172a; padding: 2px 6px; border-radius: 4px;"><?php echo $row['username']; ?></code></td>
                        <td>
                            <input type="password" value="<?php echo $row['password']; ?>" 
                                   readonly 
                                   style="background:none; border:none; color:#64748b; width:80px; cursor:pointer; font-family: password;"
                                   onclick="this.type=(this.type=='password'?'text':'password')" 
                                   title="Click để hiện/ẩn mật khẩu">
                        </td>
                        <td><span class="badge role-badge"><?php echo $row['role']; ?></span></td>
                        <td>
                            <span class="badge <?php echo $row['status']=='ACTIVE'?'status-active':'status-inactive'; ?>">
                                <?php echo $row['status']=='ACTIVE'?'Hoạt động':'Đã khóa'; ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="?edit=<?php echo $row['librarianid']; ?>" style="color: #3b82f6; margin-right: 15px; font-size: 18px;" title="Chỉnh sửa"><i class="fa fa-edit"></i></a>
                            <a href="?delete=<?php echo $row['librarianid']; ?>" style="color: #f87171; font-size: 18px;" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?')" title="Khóa tài khoản"><i class="fa fa-user-slash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">Không tìm thấy dữ liệu phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>