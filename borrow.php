<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = "";

if(isset($_POST['borrow'])){
    $borrowid = trim($_POST['borrowid']);
    $studentid = trim($_POST['studentid']);
    $bookid = trim($_POST['bookid']);
    $date = $_POST['date_borrowed'];

    $check = $conn->prepare("SELECT borrowid FROM borrow WHERE borrowid=?");
    $check->bind_param("s", $borrowid);
    $check->execute();
    if($check->get_result()->num_rows > 0){
        $msg = "Lỗi: Mã lượt mượn này đã tồn tại!"; $msg_type = "error";
    } else {
        $s_stmt = $conn->prepare("SELECT studentname FROM student WHERE studentid=?");
        $s_stmt->bind_param("s", $studentid);
        $s_stmt->execute();
        $student_data = $s_stmt->get_result()->fetch_assoc();

        $b_stmt = $conn->prepare("SELECT bookname, available FROM book WHERE bookid=?");
        $b_stmt->bind_param("s", $bookid);
        $b_stmt->execute();
        $book_data = $b_stmt->get_result()->fetch_assoc();

        if(!$student_data){
            $msg = "Lỗi: Mã sinh viên không tồn tại!"; $msg_type = "error";
        } elseif(!$book_data){
            $msg = "Lỗi: Mã sách không hợp lệ!"; $msg_type = "error";
        } elseif($book_data['available'] <= 0){
            $msg = "Sách này hiện đã hết trong kho!"; $msg_type = "error";
        } else {
            $status = "BORROWING";
            $ins = $conn->prepare("INSERT INTO borrow (borrowid, studentid, bookid, date_borrowed, status) VALUES(?,?,?,?,?)");
            $ins->bind_param("sssss", $borrowid, $studentid, $bookid, $date, $status);
            
            if($ins->execute()){
                $up_book = $conn->prepare("UPDATE book SET available = available - 1 WHERE bookid=?");
                $up_book->bind_param("s", $bookid);
                $up_book->execute();
                $msg = "Đã thực hiện cho mượn thành công!"; $msg_type = "success";
            }
        }
    }
}
if(isset($_POST['return'])){
    $borrowid = trim($_POST['borrow_id_return']);
    $date_return = $_POST['date_return'];

    $q = $conn->prepare("SELECT bookid, status FROM borrow WHERE borrowid=?");
    $q->bind_param("s", $borrowid);
    $q->execute();
    $data = $q->get_result()->fetch_assoc();

    if(!$data){
        $msg = "Không tìm thấy mã giao dịch!"; $msg_type = "error";
    } elseif($data['status'] == "RETURNED"){
        $msg = "Sách này đã được trả trước đó!"; $msg_type = "error";
    } else {
        $status_new = "RETURNED";
        $up = $conn->prepare("UPDATE borrow SET date_return=?, status=? WHERE borrowid=?");
        $up->bind_param("sss", $date_return, $status_new, $borrowid);
        
        if($up->execute()){
            $up_book = $conn->prepare("UPDATE book SET available = available + 1 WHERE bookid=?");
            $up_book->bind_param("s", $data['bookid']);
            $up_book->execute();
            $msg = "Đã nhận lại sách và cập nhật kho!"; $msg_type = "success";
        }
    }
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    
    $q = $conn->prepare("SELECT bookid, status FROM borrow WHERE borrowid=?");
    $q->bind_param("s", $id);
    $q->execute();
    $data = $q->get_result()->fetch_assoc();

    if($data) {
        if($data['status'] == 'BORROWING') {
            $up_book = $conn->prepare("UPDATE book SET available = available + 1 WHERE bookid=?");
            $up_book->bind_param("s", $data['bookid']);
            $up_book->execute();
        }
        
        $del = $conn->prepare("DELETE FROM borrow WHERE borrowid=?");
        $del->bind_param("s", $id);
        $del->execute();
    }
    header("Location: borrow.php?status=deleted");
    exit();
}
$search = $_GET['search'] ?? "";
$query = "SELECT b.*, s.studentname, bk.bookname FROM borrow b 
          JOIN student s ON b.studentid = s.studentid 
          JOIN book bk ON b.bookid = bk.bookid";

if($search != ""){
    $query .= " WHERE b.borrowid LIKE ? OR s.studentname LIKE ? OR bk.bookname LIKE ?";
    $stmt = $conn->prepare($query . " ORDER BY b.date_borrowed DESC");
    $param = "%$search%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query . " ORDER BY CASE WHEN b.status = 'BORROWING' THEN 1 ELSE 2 END, b.date_borrowed DESC");
}

$return_id = $_GET['fill_return'] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý mượn trả - Library System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; padding: 30px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .nav-bar { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .btn-back { text-decoration: none; color: #94a3b8; transition: 0.3s; }
        .btn-back:hover { color: #3b82f6; }
        .form-flex { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background: #1e293b; padding: 25px; border-radius: 12px; border: 1px solid #334155; flex: 1; }
        h3 { font-size: 18px; margin-bottom: 20px; color: #3b82f6; display: flex; align-items: center; gap: 10px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px; }
        input { width: 100%; background: #0f172a; border: 1px solid #475569; padding: 10px; border-radius: 6px; color: #fff; outline: none; }
        input:focus { border-color: #3b82f6; }
        input[type="date"] { color-scheme: dark; }
        .btn { padding: 12px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-blue { background: #3b82f6; color: #white; }
        .btn-green { background: #10b981; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .table-wrap { background: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: rgba(255,255,255,0.03); color: #94a3b8; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #334155; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-brw { background: rgba(234, 179, 8, 0.1); color: #eab308; }
        .status-ret { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .msg { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid #f87171; }
        .search-box { width: 100%; background: #1e293b; border: 1px solid #334155; padding: 12px 40px; border-radius: 8px; color: #fff; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> Quay về trang chủ</a>
        <span>Thủ thư: <b><?php echo $_SESSION['username']; ?></b></span>
    </div>

    <?php if($msg != ""): ?>
        <div class="msg <?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="form-flex">
        <div class="card">
            <h3><i class="fa fa-plus-circle"></i> Cho mượn sách</h3>
            <form method="post">
                <div class="input-group">
                    <label>Mã mượn sách</label>
                    <input type="text" name="borrowid" placeholder="VD: 111222" required>
                </div>
                <div class="input-group">
                    <label>Mã Sinh viên</label>
                    <input type="text" name="studentid" placeholder="Nhập mã SV..." required>
                </div>
                <div class="input-group">
                    <label>Mã Sách</label>
                    <input type="text" name="bookid" placeholder="Nhập mã sách..." required>
                </div>
                <div class="input-group">
                    <label>Ngày mượn</label>
                    <input type="date" name="date_borrowed" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" name="borrow" class="btn btn-blue">Xác nhận cho mượn</button>
            </form>
        </div>

        <div class="card">
            <h3><i class="fa fa-undo"></i> Nhận trả sách</h3>
            <form method="post">
                <div class="input-group">
                    <label>Mã trả sách</label>
                    <input type="text" name="borrow_id_return" value="<?php echo $return_id; ?>" placeholder="Nhập mã cần trả..." required>
                </div>
                <div class="input-group">
                    <label>Ngày trả thực tế</label>
                    <input type="date" name="date_return" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div style="height: 148px;"></div>
                <button type="submit" name="return" class="btn btn-green">Xác nhận trả sách</button>
            </form>
        </div>
    </div>

    <form method="get" style="position: relative;">
        <i class="fa fa-search" style="position: absolute; left: 15px; top: 15px; color: #64748b;"></i>
        <input type="text" name="search" class="search-box" placeholder="Tìm theo tên SV, sách hoặc mã mượn..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã Mượn</th>
                    <th>Sinh viên</th>
                    <th>Tên Sách</th>
                    <th>Ngày mượn</th>
                    <th>Ngày trả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td><b style="color: #3b82f6;"><?php echo $row['borrowid']; ?></b></td>
                    <td>
                        <div><?php echo $row['studentname']; ?></div>
                        <small style="color: #64748b;"><?php echo $row['studentid']; ?></small>
                    </td>
                    <td><?php echo $row['bookname']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['date_borrowed'])); ?></td>
                    <td><?php echo ($row['date_return']) ? date('d/m/Y', strtotime($row['date_return'])) : "---"; ?></td>
                    <td>
                        <span class="badge <?php echo $row['status']=='BORROWING' ? 'status-brw' : 'status-ret'; ?>">
                            <?php echo $row['status']=='BORROWING' ? 'Đang mượn' : 'Đã trả'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($row['status'] == 'BORROWING'): ?>
                            <a href="?fill_return=<?php echo $row['borrowid']; ?>" style="color: #eab308; margin-right: 15px;" title="Trả nhanh"><i class="fa fa-share-square"></i></a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $row['borrowid']; ?>" style="color: #f87171;" onclick="return confirm('Cảnh báo: Xóa đơn này sẽ hồi lại số lượng sách nếu chưa trả. Bạn chắc chứ?')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>