<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Nếu đã đăng nhập rồi thì chuyển hướng về trang chủ
if(isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANG NHAP </title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <style>
        /* --- GIAO DIỆN RETRO GAME START MENU --- */
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'VT323', monospace; 
        }
        
        body {
            background-color: #000000; /* Nền đen tuyệt đối chuẩn game cổ điển */
            color: #00FF00; /* Chữ màu xanh lá cây neon */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            overflow: hidden;
            position: relative;
        }

        /* Hiệu ứng màn hình CRT (Sọc sọc tivi cổ điển) */
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

        /* KHUNG ĐĂNG NHẬP KIỂU PANEL TRONG GAME */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #000000; 
            padding: 40px 30px;
            border: 4px double #00FF00; /* Viền đôi màu xanh neon */
            box-shadow: 8px 8px 0px #004400; /* Đổ bóng khối cứng 2D */
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 38px;
            font-weight: bold;
            color: #FFFF00; /* Tiêu đề màu vàng chanh nổi bật */
            text-shadow: 3px 3px #FF0000; /* Đổ bóng đỏ phong cách Arcade */
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 20px;
            color: #00FFFF; /* Màu xanh cyan */
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 18px;
            margin-bottom: 6px;
            color: #FFFF00;
            text-transform: uppercase;
        }

        /* Ô nhập liệu phong cách dòng lệnh (Command Line) */
        .form-group input {
            width: 100%;
            padding: 10px 15px;
            background: #001100; /* Nền xanh rêu cực tối */
            border: 2px solid #00FF00;
            font-size: 22px;
            color: #00FF00;
            outline: none;
        }

        /* Hiệu ứng nhấp nháy hoặc đổi màu khi click vào ô nhập */
        .form-group input:focus {
            border-color: #FFFF00;
            background: #002200;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }

        /* NÚT XÁC NHẬN BUTTON GIỐNG NÚT BẤM "START GAME" */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #00FF00; 
            color: #000000;
            border: 2px dashed #000000;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .btn-submit:hover {
            background-color: #FFFF00; /* Đổi sang vàng chanh khi trỏ vào */
            color: #000000;
        }

        .btn-submit:disabled {
            background-color: #004400;
            color: #00FF00;
            cursor: not-allowed;
            border: 2px solid #00FF00;
        }

        /* HỘP BÁO LỖI KIỂU CẢNH BÁO GAME OVER / SYSTEM ERROR */
        .error-box {
            background-color: #000000;
            color: #FF00FF; /* Chữ màu hồng neon cảnh báo */
            padding: 10px;
            font-size: 18px;
            margin-bottom: 25px;
            border: 2px dashed #FF00FF;
            display: none;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header">
        <h2>► START GAME</h2>
        <p>QUAN LY THU VIEN </p>
    </div>

    <div id="errorBox" class="error-box"></div>

    <form id="loginForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <label for="username">> TÊN ĐĂNG NHẬP (USER):</label>
            <input type="text" id="username" name="username" placeholder="Nhap tai khoan..." required autofocus autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">> MẬT KHẨU (PASSWORD):</label>
            <input type="password" id="password" name="password" placeholder="********" required>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">[ XAC NHAN DANG NHAP ]</button>
    </form>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const errorBox = document.getElementById('errorBox');
    const submitBtn = document.getElementById('submitBtn');

    loginForm.onsubmit = async (e) => {
        e.preventDefault();
        errorBox.style.display = 'none';
        submitBtn.innerText = '[ DANG XAC THUC... ]';
        submitBtn.disabled = true;

        const formData = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
            csrf_token: loginForm.querySelector('input[name="csrf_token"]').value
        };

        try {
            const response = await fetch('login_api.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData) 
            });
            const data = await response.json();

            if (data.status) {
                submitBtn.innerText = '[ SUCCESS! ENTERING GAME ]';
                window.location.href = 'index.php';
            } else {
                errorBox.innerText = "! ERROR: " + data.message;
                errorBox.style.display = 'block';
                submitBtn.innerText = '[ XAC NHAN DANG NHAP ]';
                submitBtn.disabled = false;
            }
        } catch (err) {
            errorBox.innerText = "! ERROR: MAT KET NOI API SERVER!";
            errorBox.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerText = '[ XAC NHAN DANG NHAP ]';
        }
    };
</script>

</body>
</html>
<!-- Quang huy update-->
<!--Quang huy update lan 2-->
<!--Quang huy update lan 3-->
<!--xin chao-->
<!--my name is Huy-->
