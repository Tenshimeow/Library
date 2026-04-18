<?php
session_start();
include("librarydb.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$avatar_path = "uploads/" . $username . ".jpg";
if (!file_exists($avatar_path)) { $avatar_path = null; }

$count_students = $conn->query("SELECT COUNT(*) as t FROM student")->fetch_assoc()['t'];
$count_books = $conn->query("SELECT COUNT(*) as t FROM book")->fetch_assoc()['t'];
$count_borrowing = $conn->query("SELECT COUNT(*) as t FROM borrow WHERE status='BORROWING'")->fetch_assoc()['t'];
$count_librarians = ($role == "ADMIN") ? $conn->query("SELECT COUNT(*) as t FROM librarian")->fetch_assoc()['t'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ | Library System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --secondary: #06b6d4; --bg-dark: #0f172a; --card-glass: rgba(255, 255, 255, 0.03); --sidebar-color: #1e293b; --danger: #ef4444; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at top right, #1e293b, #020617); color: #f1f5f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--sidebar-color); border-right: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .sidebar-header { padding: 40px 20px; text-align: center; font-size: 20px; font-weight: 800; background: linear-gradient(to right, #818cf8, #22d3ee); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 10px; flex-grow: 1; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: 0.3s; border-radius: 12px; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(5px); }
        .sidebar-menu a i { width: 30px; }
        .main { margin-left: 260px; width: calc(100% - 260px); }
        .top-nav { padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.05); position: sticky; top: 0; z-index: 100; }
        .avatar-circle { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--primary); overflow: hidden; display: flex; justify-content: center; align-items: center; background: var(--primary); font-weight: bold; font-size: 18px; color: white; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .dropdown-menu { position: absolute; top: 55px; right: 0; width: 220px; background: #1e293b; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none; overflow: hidden; }
        .dropdown-menu.active { display: block; animation: slideDown 0.3s; }
        .dropdown-menu a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #cbd5e1; text-decoration: none; font-size: 14px; transition: 0.2s; }
        .dropdown-menu a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .container { padding: 40px; }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: var(--card-glass); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); text-align: center; }
        .stat-card b { font-size: 24px; color: var(--secondary); display: block; margin-top: 5px; }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .action-card { background: var(--card-glass); padding: 30px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 20px; cursor: pointer; transition: 0.4s; text-decoration: none; color: inherit; position: relative; }
        .action-card:hover { background: rgba(99, 102, 241, 0.1); border-color: var(--primary); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .icon-box { width: 60px; height: 60px; border-radius: 16px; display: flex; justify-content: center; align-items: center; font-size: 24px; }
        .blue { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .green { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .purple { background: rgba(168, 85, 247, 0.2); color: #c084fc; }
        .red { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .orange { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .card-info h4 { margin: 0; font-size: 18px; }
        .card-info p { margin: 5px 0 0; font-size: 13px; color: #94a3b8; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">LIBRARY TENSHIMEOW</div>
    <div class="sidebar-menu">
        <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Tổng quan</a>
        <a href="student.php"><i class="fa-solid fa-users"></i> Sinh viên</a>
        <a href="book.php"><i class="fa-solid fa-book"></i> Sách</a>
        <a href="borrow.php"><i class="fa-solid fa-exchange-alt"></i> Mượn & Trả</a>
        <a href="system_log.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử & thông báo</a>
        <?php if($role == "ADMIN"): ?>
            <div style="margin: 20px 20px 10px; font-size: 11px; color: #475569; font-weight: bold; text-transform: uppercase;">Quản trị viên</div>
            <a href="librarian.php"><i class="fa-solid fa-shield-halved"></i> Quản lý thủ thư</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    <nav class="top-nav">
        <div style="font-weight: 600; font-size: 14px; color: #94a3b8;">HỆ THỐNG QUẢN LÝ THƯ VIỆN</div>
        <div class="profile-container" style="position:relative;">
            <div class="avatar-trigger" onclick="toggleMenu()" style="display:flex; align-items:center; gap:12px; cursor:pointer;">
                <div style="text-align: right">
                    <div style="font-size: 14px; font-weight: bold;"><?php echo $username; ?></div>
                    <div style="font-size: 11px; color: var(--secondary);"><?php echo $role; ?></div>
                </div>
                <div class="avatar-circle">
                    <?php if($avatar_path): ?>
                        <img src="<?php echo $avatar_path; ?>?t=<?php echo time(); ?>" alt="Avatar">
                    <?php else: ?>
                        <?php echo strtoupper(substr($username, 0, 1)); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dropdown-menu" id="userMenu">
                <a href="change_password.php"><i class="fa fa-key"></i> Đổi mật khẩu</a>
                <a href="logout.php" style="color: #f87171; border-top: 1px solid rgba(255,255,255,0.05);"><i class="fa fa-sign-out"></i> Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="margin-bottom: 35px;">
            <h1 style="margin: 0; font-size: 28px;">Xin chào, <?php echo $username; ?> 👋</h1>
            <p style="color: #94a3b8; margin-top: 5px;">Hệ thống đang hoạt động ổn định.</p>
        </div>

        <div class="stats-bar">
            <div class="stat-card">Sinh viên <b><?php echo number_format($count_students); ?></b></div>
            <div class="stat-card">Tổng sách <b><?php echo number_format($count_books); ?></b></div>
            <div class="stat-card">Đang mượn <b style="color: #fbbf24;"><?php echo number_format($count_borrowing); ?></b></div>
            <?php if($role == "ADMIN"): ?>
                <div class="stat-card">Thủ thư <b><?php echo $count_librarians; ?></b></div>
            <?php endif; ?>
        </div>

        <div class="action-grid">
            <a href="student.php" class="action-card">
                <div class="icon-box blue"><i class="fa-solid fa-user-plus"></i></div>
                <div class="card-info"><h4>Sinh Viên</h4><p>Quản lý sinh viên</p></div>
            </a>
            <a href="book.php" class="action-card">
                <div class="icon-box green"><i class="fa-solid fa-book"></i></div>
                <div class="card-info"><h4>Sách</h4><p>Kho sách thư viện</p></div>
            </a>
            <a href="borrow.php" class="action-card">
                <div class="icon-box purple"><i class="fa-solid fa-exchange-alt"></i></div>
                <div class="card-info"><h4>Mượn & Trả</h4><p>Quản lý mượn trả</p></div>
            </a>
            <?php if($role == "ADMIN"): ?>
            <a href="librarian.php" class="action-card">
                <div class="icon-box red"><i class="fa-solid fa-user-gear"></i></div>
                <div class="card-info"><h4>Thủ Thư</h4><p>Quản lý nhân sự</p></div>
            </a>
            <?php endif; ?>
            <a href="system_log.php" class="action-card">
                <div class="icon-box orange"><i class="fa-solid fa-list-check"></i></div>
                <div class="card-info"><h4>Lịch sử & thông báo</h4><p>Nhật ký hệ thống</p></div>
                </a>
        </div>
    </div>
</div>
<script>
    function toggleMenu() { document.getElementById('userMenu').classList.toggle('active'); }
    window.onclick = function(event) { if (!event.target.closest('.profile-container')) { var menu = document.getElementById('userMenu'); if (menu.classList.contains('active')) menu.classList.remove('active'); } }
</script>
</body>
</html>