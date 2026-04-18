<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = ""; 


if(isset($_POST['insert'])){
    $id = $_POST['bookid'];
    $name = $_POST['bookname'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $qty = (int)$_POST['quantity'];

    if(empty($id) || empty($name)){
        $msg = "Vui lòng nhập đầy đủ Mã sách và Tên sách!";
        $msg_type = "error";
    } else {
        $check = $conn->prepare("SELECT bookid FROM book WHERE bookid=?");
        $check->bind_param("s", $id);
        $check->execute();
        if($check->get_result()->num_rows > 0){
            $msg = "Mã sách này đã tồn tại trong kho!";
            $msg_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO book (bookid, bookname, author, publisher, category, quantity, available) VALUES(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssii", $id, $name, $author, $publisher, $category, $qty, $qty);
            if($stmt->execute()){
                $msg = "Đã nhập sách vào kho thành công!";
                $msg_type = "success";
            }
        }
    }
}

if(isset($_POST['update'])){
    $id = $_POST['bookid'];
    $name = $_POST['bookname'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $qty = (int)$_POST['quantity'];
    $avail = (int)$_POST['available'];

    $stmt = $conn->prepare("UPDATE book SET bookname=?, author=?, publisher=?, category=?, quantity=?, available=? WHERE bookid=?");
    $stmt->bind_param("ssssiis", $name, $author, $publisher, $category, $qty, $avail, $id);
    if($stmt->execute()){
        header("Location: book.php?status=updated");
        exit();
    }
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM book WHERE bookid=?");
    $stmt->bind_param("s", $id);
    if($stmt->execute()){
        header("Location: book.php?status=deleted");
        exit();
    }
}


if(isset($_GET['status'])){
    if($_GET['status'] == 'deleted'){ $msg = "Đã xóa đầu sách thành công!"; $msg_type = "success"; }
    if($_GET['status'] == 'updated'){ $msg = "Đã cập nhật thông tin sách!"; $msg_type = "success"; }
}

$search = $_GET['search'] ?? "";
if($search != ""){
    $query = "SELECT * FROM book WHERE bookid LIKE ? OR bookname LIKE ?";
    $param = "%$search%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM book ORDER BY bookid DESC");
}

$edit = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM book WHERE bookid=?");
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
    <title>Quản lý Kho Sách - Thư viện</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; padding: 40px 20px; }
        .container { max-width: 1150px; margin: 0 auto; }
        
        .nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { text-decoration: none; color: #94a3b8; font-size: 14px; transition: 0.2s; }
        .btn-back:hover { color: #3b82f6; }

        .card { background: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid #334155; margin-bottom: 30px; }
        h1 { font-size: 26px; margin-bottom: 25px; color: #fff; display: flex; align-items: center; gap: 10px; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; }
        
        input, select { 
            width: 100%; background: #0f172a; border: 1px solid #475569; padding: 12px; 
            border-radius: 8px; color: #fff; outline: none; transition: 0.2s; font-size: 14px;
        }
        input::placeholder { color: #4b5563; }
        input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        
        .btn { padding: 12px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-submit { background: #3b82f6; color: white; }
        .btn-submit:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-edit { background: #eab308; color: #000; }

        .search-container { margin-bottom: 20px; position: relative; }
        .search-container input { padding-left: 45px; background: #1e293b; border-radius: 8px; height: 50px; }
        .search-container i { position: absolute; left: 18px; top: 18px; color: #64748b; }

        .table-wrap { overflow-x: auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        th { text-align: left; padding: 15px; background: rgba(255,255,255,0.03); font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 14px; vertical-align: middle; }
        
        .badge-id { color: #eab308; font-family: 'Courier New', monospace; font-weight: bold; }
        .msg { padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-weight: 600; }
        .success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid #f87171; }
        
        .action-btns a { font-size: 18px; transition: 0.2s; }
        .action-btns a:hover { opacity: 0.7; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> Quay về trang chủ</a>
        <div style="font-size: 14px; color: #94a3b8">
            <i class="fa fa-user-circle"></i> Thủ thư: <b style="color: #fff"><?php echo $_SESSION['username']; ?></b>
        </div>
    </div>

    <h1><i class="fa fa-book-open" style="color: #3b82f6;"></i> Quản lý Kho Sách</h1>

    <?php if($msg != ""): ?>
        <div class="msg <?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 16px; color: #3b82f6; display: flex; align-items: center; gap: 8px;">
            <i class="fa <?php echo $edit ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
            <?php echo $edit ? "Chỉnh sửa thông tin sách" : "Nhập sách mới vào kho"; ?>
        </h3>
        <form method="post">
            <div class="form-grid">
                <div class="input-group">
                    <label>Mã sách (ID)</label>
                    <input type="text" name="bookid" placeholder="VD: 001, 002..." value="<?php echo $edit['bookid'] ?? ''; ?>" <?php echo $edit ? 'readonly style="opacity:0.6"' : ''; ?> required>
                </div>
                <div class="input-group">
                    <label>Tên đầu sách</label>
                    <input type="text" name="bookname" placeholder="Ví dụ: Lập trình PHP cơ bản" value="<?php echo $edit['bookname'] ?? ''; ?>" required>
                </div>
                <div class="input-group">
                    <label>Tác giả</label>
                    <input type="text" name="author" placeholder="Nguyễn Văn A" value="<?php echo $edit['author'] ?? ''; ?>">
                </div>
                <div class="input-group">
                    <label>Nhà xuất bản</label>
                    <input type="text" name="publisher" placeholder="Ví dụ: NXB Giáo dục, NXB Trẻ..." value="<?php echo $edit['publisher'] ?? ''; ?>">
                </div>
                <div class="input-group">
                    <label>Phân loại</label>
                    <select name="category">
                        <option value="" disabled <?php echo !isset($edit) ? 'selected' : ''; ?>>-- Chọn thể loại --</option>
                        <?php 
                       $opts = ["CNTT", "Kinh tế", "Ngoại ngữ", "Kỹ năng", "Văn học", "Marketing", "BigData", "CSDL phân tán", "Đại Cương", "Khác"];
                        foreach($opts as $o){
                            $sel = (isset($edit) && $edit['category'] == $o) ? "selected" : "";
                            echo "<option value='$o' $sel>$o</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Tổng số lượng</label>
                    <input type="number" name="quantity" placeholder="Số lượng nhập kho (VD: 100)" value="<?php echo $edit['quantity'] ?? ''; ?>" required>
                </div>
                <?php if($edit): ?>
                <div class="input-group">
                    <label>Số lượng khả dụng (Sẵn có)</label>
                    <input type="number" name="available" placeholder="Số sách thực tế còn lại" value="<?php echo $edit['available'] ?? ''; ?>" style="border-color: #10b981;">
                </div>
                <?php endif; ?>
            </div>

            <div style="text-align: right; border-top: 1px solid #334155; padding-top: 20px;">
                <?php if($edit): ?>
                    <a href="book.php" style="color: #94a3b8; margin-right: 20px; text-decoration: none; font-size: 14px;">Hủy bỏ</a>
                    <button type="submit" name="update" class="btn btn-edit">Lưu thay đổi</button>
                <?php else: ?>
                    <button type="submit" name="insert" class="btn btn-submit">Xác nhận thêm</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="search-container">
        <form method="get">
            <i class="fa fa-search"></i>
            <input type="text" name="search" placeholder="Tìm theo tên sách hoặc mã định danh..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Sách</th>
                    <th>Tác giả</th>
                    <th>Nhà xuất bản</th>
                    <th>Loại</th>
                    <th style="text-align: center;">Kho</th>
                    <th style="text-align: center;">Sẵn có</th>
                    <th style="text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <td><span class="badge-id"><?php echo $row['bookid']; ?></span></td>
                        <td><b style="color: #fff;"><?php echo $row['bookname']; ?></b></td>
                        <td><?php echo $row['author'] ?: '<i style="color:#4b5563">Chưa rõ</i>'; ?></td>
                        <td><?php echo $row['publisher'] ?: '<i style="color:#4b5563">Chưa rõ</i>'; ?></td>
                        <td><span style="background: rgba(59,130,246,0.1); color: #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?php echo $row['category']; ?></span></td>
                        <td style="text-align: center;"><?php echo $row['quantity']; ?></td>
                        <td style="text-align: center; color: #10b981; font-weight: bold;"><?php echo $row['available']; ?></td>
                        <td style="text-align: center;" class="action-btns">
                            <a href="?edit=<?php echo $row['bookid']; ?>" style="color: #eab308; margin-right: 15px;" title="Sửa thông tin"><i class="fa fa-edit"></i></a>
                            <a href="?delete=<?php echo $row['bookid']; ?>" style="color: #f87171;" onclick="return confirm('Xóa đầu sách này khỏi kho? Thao tác không thể hoàn tác!')" title="Xóa sách"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                            Không tìm thấy cuốn sách nào phù hợp!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>