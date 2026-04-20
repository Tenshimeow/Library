<?php
session_start();
include("librarydb.php");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if ($token !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Lỗi bảo mật (CSRF)']);
        exit;
    }

    $stmt = $conn->prepare("SELECT librarianname, password, role FROM librarian WHERE username = ? AND status = 'ACTIVE'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username; 
            $_SESSION['display_name'] = $row['librarianname']; 
            $_SESSION['role'] = $row['role'];

            $log_detail = "Thủ thư " . $row['librarianname'] . " ($username) đã đăng nhập hệ thống.";
            $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'LOGIN', ?, NOW())");
            $log_stmt->bind_param("ss", $username, $log_detail);
            $log_stmt->execute();

            echo json_encode(['success' => true, 'redirect' => 'index.php']);
        } else {
            $log_fail = "Cảnh báo: Thử đăng nhập sai mật khẩu cho tài khoản: $username";
            $log_stmt = $conn->prepare("INSERT INTO system_log (username, action_type, action_detail, action_time) VALUES (?, 'LOGIN_FAIL', ?, NOW())");
            $log_stmt->bind_param("ss", $username, $log_fail);
            $log_stmt->execute();

            echo json_encode(['success' => false, 'message' => 'Mật khẩu không chính xác']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: #1e293b; 
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            color: #94a3b8;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #cbd5e1;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: #0f172a;
            border: 1px solid #475569;
            border-radius: 6px;
            font-size: 15px;
            color: #ffffff;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #3b82f6;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #3b82f6; 
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .btn-submit:disabled {
            background-color: #475569;
            cursor: not-allowed;
        }

        .error-box {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: none;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header">
        <h2>Đăng nhập</h2>
        <p>Quản lý Thư viện</p>
    </div>

    <div id="errorBox" class="error-box"></div>

    <form id="loginForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="ajax_login" value="1">

        <div class="form-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" placeholder="Tài khoản thủ thư" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">Xác nhận</button>
    </form>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const errorBox = document.getElementById('errorBox');
    const submitBtn = document.getElementById('submitBtn');

    loginForm.onsubmit = async (e) => {
        e.preventDefault();
        errorBox.style.display = 'none';
        submitBtn.innerText = 'Đang kiểm tra...';
        submitBtn.disabled = true;

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                submitBtn.innerText = 'Thành công!';
                window.location.href = data.redirect;
            } else {
                errorBox.innerText = data.message;
                errorBox.style.display = 'block';
                submitBtn.innerText = 'Xác nhận';
                submitBtn.disabled = false;
            }
        } catch (err) {
            errorBox.innerText = "Lỗi kết nối máy chủ!";
            errorBox.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerText = 'Xác nhận';
        }
    };
</script>

</body>
</html>