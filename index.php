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
    <title>DANH SACH DAU SACH </title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'VT323', monospace; 
        }
        
        body { 
            background-color: #000000; 
            color: #00FF00; 
            display: flex; 
            min-height: 100vh; 
            font-size: 22px; 
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: " ";
            display: block;
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            z-index: 999;
            background-size: 100% 4px, 3px 100%;
            pointer-events: none;
        }

        .sidebar { 
            width: 260px; 
            background: #000000; 
            border-right: 4px double #00FF00; 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100vh; 
            z-index: 99;
            padding: 10px;
        }
        .sidebar-header { 
            padding: 15px 10px; 
            font-size: 28px; 
            font-weight: bold;
            color: #FFFF00; 
            text-align: center;
            border-bottom: 4px double #00FF00;
            text-transform: uppercase;
            text-shadow: 2px 2px #FF0000; 
        }
        .sidebar-title {
            padding: 20px 10px 5px;
            font-size: 20px;
            color: #00FFFF; 
            text-transform: uppercase;
        }
        .sidebar-menu { padding: 0 5px; flex-grow: 1; }
        .sidebar-menu a { 
            display: block; 
            padding: 8px 12px; 
            color: #00FF00; 
            text-decoration: none; 
            margin-bottom: 5px;
            border: 2px solid transparent;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { 
            background: #00FF00; 
            color: #000000; 
            font-weight: bold;
            border: 2px dashed #000000;
        }
        .sidebar-menu a::before {
            content: "[ ] ";
        }
        .sidebar-menu a:hover::before, .sidebar-menu a.active::before {
            content: "[>] "; 
        }

        .main { margin-left: 260px; width: calc(100% - 260px); display: flex; flex-direction: column; }
        
        .top-nav { 
            padding: 0 20px; 
            height: 60px;
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #000000; 
            border-bottom: 4px double #00FF00; 
        }
        .top-nav .nav-title { font-size: 24px; color: #FFFF00; font-weight: bold; }
        
        .profile-container { position: relative; }
        .avatar-trigger { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .avatar-circle { 
            width: 36px; 
            height: 36px; 
            border: 2px solid #00FF00; 
            overflow: hidden; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            background: #000000; 
            font-weight: bold; 
            color: #00FF00; 
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1) contrast(2); }
        
        .dropdown-menu { 
            position: absolute; 
            top: 50px; 
            right: 0; 
            width: 160px; 
            background: #000000; 
            border: 3px solid #00FF00; 
            display: none; 
            z-index: 105;
        }
        .dropdown-menu.active { display: block; }
        .dropdown-menu a { 
            display: block; 
            padding: 8px 12px; 
            color: #00FF00; 
            text-decoration: none; 
        }
        .dropdown-menu a:hover { background: #FFFF00; color: #000000; }

        .container { padding: 25px; max-width: 1200px; width: 100%; margin: 0 auto; }
        
        .page-header { margin-bottom: 30px; text-align: center; }
        .page-header h1 { font-size: 36px; color: #FFFF00; text-shadow: 2px 2px #00FFFF; }

        .section-title { 
            font-size: 24px; 
            color: #FFFF00; 
            margin-bottom: 15px; 
            padding: 5px 10px;
            background: #002200; 
            border: 2px solid #00FF00;
            display: inline-block;
        }

        .stats-bar { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 20px; 
            margin-bottom: 35px; 
        }
        .stat-card { 
            background: #000000; 
            padding: 15px; 
            border: 3px solid #00FF00; 
            box-shadow: 5px 5px 0px #004400; 
        }
        .stat-card .label { font-size: 18px; color: #00FFFF; margin-bottom: 5px; }
        .stat-card .value { font-size: 42px; color: #00FF00; font-weight: bold; }
        .stat-card .value.alert { color: #FF00FF; } 

        .action-list { 
            display: flex;
            flex-direction: column;
            background: #000000;
            border: 3px solid #00FF00;
            margin-bottom: 20px;
        }
        .action-item { 
            display: block; 
            padding: 15px 20px; 
            border-bottom: 2px dashed #004400;
            text-decoration: none; 
            color: #00FF00; 
            transition: all 0.1s;
        }
        .action-item:last-child { border-bottom: none; }
        .action-item:hover { 
            background: #002200; 
            padding-left: 30px; 
        }
        
        .card-info h4 { font-size: 26px; color: #00FF00; display: flex; align-items: center; }
        .action-item:hover h4 { color: #FFFF00; }
        .card-info h4::before { content: "► "; margin-right: 10px; color: #FFFF00; }
        .card-info p { font-size: 18px; color: #a0aec0; margin-top: 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">THU VIEN NHOM 7</div>
    
    <div class="sidebar-title">--- MENU ---</div>
    <div class="sidebar-menu">
        <a href="index.php" class="active">Trang chu</a>
        <a href="student.php">Quan ly sinh vien</a>
        <a href="book.php">Quan ly sach</a>
        <a href="borrow.php">Muon va tra sach</a>
        <a href="system_log.php">Nhat ky he thong</a>
        
        <?php if($role == "ADMIN"): ?>
            <div class="sidebar-title">--- ADMIN ---</div>
            <a href="librarian.php">Quan ly thu thu</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    <nav class="top-nav">
        <div class="nav-title">HE THONG QUAN LY THU VIEN </div>
        
        <div class="profile-container">
            <div class="avatar-trigger" onclick="toggleMenu()">
                <div style="text-align: right; margin-right: 5px;">
                    <div style="font-size: 18px; font-weight: bold; color: #00FF00;"><?php echo htmlspecialchars($username); ?></div>
                    <div style="font-size: 14px; color: #FFFF00;">[<?php echo htmlspecialchars($role); ?>]</div>
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
                <a href="change_password.php">> Doi mat khau</a>
                <a href="logout.php" style="color: #FF0000; border-top: 2px dashed #00FF00;">> Dang xuat</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>CHAO MUNG TRO LAI: <?php echo strtoupper(htmlspecialchars($username)); ?></h1>
        </div>

        <div class="section-title">SO LIEU THU VIEN HIEN TAI</div>
        <div class="stats-bar">
            <div class="stat-card">
                <div class="label">TONG SO SINH VIEN</div>
                <div class="value"><?php echo number_format($count_students); ?></div>
            </div>
            <div class="stat-card">
                <div class="label">TONG SO DAU SACH</div>
                <div class="value"><?php echo number_format($count_books); ?></div>
            </div>
            <div class="stat-card">
                <div class="label">SACH DANG MUON</div>
                <div class="value alert"><?php echo number_format($count_borrowing); ?></div>
            </div>
            <?php if($role == "ADMIN"): ?>
                <div class="stat-card">
                    <div class="label">SO LUONG THU THU</div>
                    <div class="value"><?php echo $count_librarians; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-title">CAC CHUC NANG CO THE THUC HIEN</div>
        <div class="action-list">
            <a href="student.php" class="action-item">
                <div class="card-info">
                    <h4>QUAN LY SINH VIEN</h4>
                    <p>Xem danh sach, them tai khoan sinh vien moi hoac sua thong tin ho so sinh vien.</p>
                </div>
            </a>
            
            <a href="book.php" class="action-item">
                <div class="card-info">
                    <h4>QUAN LY SACH</h4>
                    <p>Theo doi so luong sach trong kho, them dau sach moi hoac chinh sua danh muc.</p>
                </div>
            </a>
            
            <a href="borrow.php" class="action-item">
                <div class="card-info">
                    <h4>MUON VA TRA SACH</h4>
                    <p>Tao phieu cho muon sach, xu ly nhan sach tra va kiem tra cac phieu qua han.</p>
                </div>
            </a>
            
            <?php if($role == "ADMIN"): ?>
            <a href="librarian.php" class="action-item">
                <div class="card-info">
                    <h4>QUAN LY THU THU</h4>
                    <p>Them nhan su moi, phan quyen truy cap he thong hoac khoa tai khoan nhan vien.</p>
                </div>
            </a>
            <?php endif; ?>
            
            <a href="system_log.php" class="action-item">
                <div class="card-info">
                    <h4>NHAT KY HE THONG</h4>
                    <p>Theo doi lich su dang nhap, cac thao tac chinh sua du lieu cua cac tai khoan.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    function toggleMenu() { 
        document.getElementById('userMenu').classList.toggle('active'); 
    }
    window.onclick = function(event) { 
        if (!event.target.closest('.profile-container')) { 
            var menu = document.getElementById('userMenu'); 
            if (menu.classList.contains('active')) menu.classList.remove('active'); 
        } 
    }
</script>
</body>
</html>