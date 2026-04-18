<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

/* ================= 1. XỬ LÝ GỬI TIN & XÓA (ADMIN ONLY) ================= */
if(isset($_POST['add_note']) && $role === "ADMIN"){
    $note = $_POST['note_content'];
    $stmt = $conn->prepare("INSERT INTO admin_notes (username, note_content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $note);
    $stmt->execute();
    header("Location: system_log.php");
    exit();
}

if(isset($_GET['del_note']) && $role === "ADMIN"){
    $id = (int)$_GET['del_note'];
    $conn->query("DELETE FROM admin_notes WHERE id = $id");
    header("Location: system_log.php");
    exit();
}

if(isset($_GET['delete_log']) && $role === "ADMIN"){
    $id = (int)$_GET['delete_log'];
    $conn->query("DELETE FROM system_log WHERE id = $id");
    header("Location: system_log.php");
    exit();
}

/* ================= 2. TRUY VẤN DỮ LIỆU ================= */

// Lấy 10 thông báo mới nhất
$notes_res = $conn->query("SELECT * FROM admin_notes ORDER BY created_at DESC LIMIT 10");

// Xử lý tìm kiếm nhật ký
$search = $_GET['search'] ?? '';
$where_clause = ($role !== "ADMIN") ? "WHERE l.username = '$username'" : "WHERE 1=1";

if ($role === "ADMIN" && !empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    // Tìm theo tên nhân viên hoặc loại hành động
    $where_clause .= " AND (l.username LIKE '%$search_safe%' OR l.action_type LIKE '%$search_safe%')";
}

$logs_query = "SELECT l.*, lb.role as user_role 
               FROM system_log l 
               LEFT JOIN librarian lb ON l.username = lb.username 
               $where_clause 
               ORDER BY l.action_time DESC 
               LIMIT 100";
$logs_res = $conn->query($logs_query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhật ký hệ thống</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Rút gọn, tập trung vào sự tối giản */
        :root { --primary: #6366f1; --bg: #0f172a; --card: #1e293b; --border: rgba(255,255,255,0.1); }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); color: #f1f5f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-back { color: #94a3b8; text-decoration: none; font-size: 14px; }

        .admin-msg-card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .msg-item { border-bottom: 1px solid var(--border); padding: 10px 0; }
        .msg-item:last-child { border: none; }
        .msg-meta { font-size: 12px; color: var(--primary); margin-bottom: 5px; }

        /* PHẦN TÌM KIẾM BÌNH THƯỜNG */
        .search-box {
            background: #1e293b;
            border: 1px solid #334155;
            color: white;
            padding: 8px 15px;
            border-radius: 4px; /* Vuông vắn hơn */
            width: 250px;
            outline: none;
        }
        .search-box:focus { border-color: var(--primary); }

        .table-res { background: var(--card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(255,255,255,0.05); padding: 12px; text-align: left; font-size: 13px; color: #94a3b8; }
        td { padding: 12px; border-bottom: 1px solid var(--border); font-size: 13px; }
        
        .role-admin { color: #fb7185; font-weight: bold; }
        .role-staff { color: #38bdf8; font-weight: bold; }
        .action-tag { background: #334155; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
        .btn-del { color: #64748b; text-decoration: none; }
        .btn-del:hover { color: #f43f5e; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fa fa-history"></i> Nhật ký hoạt động</h2>
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="admin-msg-card">
        <h4 style="margin-top:0"><i class="fa fa-bullhorn"></i> Thông báo từ Admin</h4>
        <?php if($role === "ADMIN"): ?>
        <form method="POST" style="display:flex; gap:10px; margin-bottom:15px;">
            <input type="text" name="note_content" placeholder="Nhập nội dung thông báo..." style="flex:1; background:#0f172a; border:1px solid #334155; color:white; padding:8px; border-radius:4px;" required>
            <button type="submit" name="add_note" style="background:var(--primary); color:white; border:none; padding:8px 20px; border-radius:4px; cursor:pointer;">Gửi</button>
        </form>
        <?php endif; ?>

        <div class="msg-list">
            <?php while($n = $notes_res->fetch_assoc()): ?>
            <div class="msg-item">
                <div class="msg-meta">
                    <b>Admin</b> • <?php echo date('H:i - d/m/Y', strtotime($n['created_at'])); ?>
                    <?php if($role === "ADMIN"): ?>
                        <a href="?del_note=<?php echo $n['id']; ?>" class="btn-del" style="float:right"><i class="fa fa-times"></i></a>
                    <?php endif; ?>
                </div>
                <div style="font-size: 14px;"><?php echo htmlspecialchars($n['note_content']); ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <span style="font-size: 14px; color: #94a3b8;">Danh sách 100 hoạt động gần nhất</span>
        
        <?php if($role === "ADMIN"): ?>
        <form method="GET">
            <input type="text" name="search" class="search-box" placeholder="Tìm tên nhân viên..." value="<?php echo htmlspecialchars($search); ?>">
            <?php if(!empty($search)): ?>
                <a href="system_log.php" style="color:#64748b; font-size:12px; margin-left:5px; text-decoration:none;">Hủy</a>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </div>

    <div class="table-res">
        <table>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Nhân sự</th>
                    <th>Hành động</th>
                    <th>Chi tiết</th>
                    <?php if($role === "ADMIN"): ?><th><i class="fa fa-trash"></i></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while($l = $logs_res->fetch_assoc()): ?>
                <tr>
                    <td style="color:#64748b;"><?php echo date('d/m H:i', strtotime($l['action_time'])); ?></td>
                    <td>
                        <span class="<?php echo ($l['user_role'] === 'ADMIN') ? 'role-admin' : 'role-staff'; ?>">
                            <?php echo htmlspecialchars($l['username']); ?>
                        </span>
                    </td>
                    <td><span class="action-tag"><?php echo htmlspecialchars($l['action_type']); ?></span></td>
                    <td style="color:#cbd5e1;"><?php echo htmlspecialchars($l['action_detail']); ?></td>
                    <?php if($role === "ADMIN"): ?>
                    <td>
                        <a href="?delete_log=<?php echo $l['id']; ?>" class="btn-del" onclick="return confirm('Xóa dòng này?')"><i class="fa fa-trash"></i></a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>