<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role']; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOI MAT KHAU </title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            font-size: 22px;
            position: relative;
            overflow: hidden;
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
        
        .card { 
            background: #000000; 
            padding: 35px 30px; 
            border: 4px double #00FF00; 
            width: 100%;
            max-width: 440px; 
            box-shadow: 6px 6px 0px #003300; 
            position: relative;
        }
        
        .header-info { 
            background: #001100; 
            padding: 12px; 
            border: 2px dashed #00FF00; 
            margin-bottom: 25px; 
            text-align: center; 
            font-size: 18px;
            color: #00FFFF; 
        }
        
        h2 { 
            margin-bottom: 20px; 
            font-size: 36px; 
            text-align: center; 
            color: #FFFF00; 
            font-weight: bold;
            text-shadow: 2px 2px #FF0000;
            text-transform: uppercase;
        }
        
        .form-group { margin-bottom: 15px; }
        
        label { 
            display: block; 
            font-size: 18px; 
            color: #00FFFF; 
            margin-bottom: 6px; 
            font-weight: bold;
        }
        
    
        input { 
            width: 100%; 
            padding: 10px 12px; 
            background: #000000; 
            border: 2px solid #00FF00; 
            color: #00FF00; 
            outline: none; 
            font-size: 22px;
        }
        
        input:focus { 
            border-color: #FFFF00; 
            background: #001100;
        }
        
    
        .btn-save { 
            width: 100%; 
            padding: 12px; 
            background: #00FF00; 
            color: #000000; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 22px; 
            margin-top: 15px; 
            text-transform: uppercase;
        }
        
        .btn-save:hover:not(:disabled) { 
            background: #FFFF00; 
        }
        .btn-save:disabled {
            background: #004400;
            color: #008800;
            cursor: not-allowed;
        }
        
        .alert { 
            padding: 12px; 
            margin-bottom: 20px; 
            text-align: center; 
            font-size: 18px; 
            font-weight: bold; 
            display: none;
            text-transform: uppercase;
        }
        .alert-error { 
            background: #000000; 
            color: #FF00FF; 
            border: 2px dashed #FF00FF; 
        }
        .alert-success { 
            background: #000000; 
            color: #00FF00; 
            border: 2px dashed #00FF00; 
        }
        
        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 25px; 
            color: #FFFF00; 
            text-decoration: none; 
            font-size: 18px; 
        }
        .back-link:hover { 
            color: #00FFFF;
            text-shadow: 0 0 5px #00FFFF; 
        }
    </style>
</head>
<body>

<div class="card">
    <div class="header-info">
        <i class="fa fa-user-circle"></i> USER: <strong style="color: #FFFF00"><?php echo strtoupper(htmlspecialchars($username)); ?></strong> [<?php echo strtoupper(htmlspecialchars($role)); ?>]
    </div>

    <h2><i class="fa fa-key"></i> DOI MAT KHAU</h2>

    <div id="alertBox" class="alert"></div>

    <form id="changePassForm">
        <input type="hidden" id="api_username" value="<?php echo htmlspecialchars($username); ?>">

        <div class="form-group">
            <label>> MAT KHAU HIEN TAI:</label>
            <input type="password" id="old_password" required placeholder="Nhap mat khau dang dung" autocomplete="off">
        </div>
        <div class="form-group">
            <label>> MAT KHAU MOI:</label>
            <input type="password" id="new_password" required placeholder="Toi thieu 6 ky tu" autocomplete="off">
        </div>
        <div class="form-group">
            <label>> XAC NHAN MAT KHAU MOI:</label>
            <input type="password" id="confirm_password" required placeholder="Nhap lai mat khau moi" autocomplete="off">
        </div>
        <button type="submit" id="btnSave" class="btn-save">XAC NHAN THAY DOI</button>
    </form>

    <a href="index.php" class="back-link"><i class="fa fa-chevron-left"></i> [ QUAY LAI TRANG CHU ]</a>
</div>

<script>
    const changePassForm = document.getElementById('changePassForm');
    const alertBox = document.getElementById('alertBox');
    const btnSave = document.getElementById('btnSave');

    changePassForm.onsubmit = async (e) => {
        e.preventDefault();
        alertBox.style.display = 'none';

        const oldPass = document.getElementById('old_password').value;
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;

        if (newPass.length < 6) {
            showAlert('! LOI: MAT KHAU MOI PHAI TU 6 KY TU TRO LEN !', 'alert-error');
            return;
        }

        if (newPass !== confirmPass) {
            showAlert('! LOI: MAT KHAU XAC NHAN KHONG TRUNG KHOP !', 'alert-error');
            return;
        }

        btnSave.innerText = 'LOADING...';
        btnSave.disabled = true;

        const requestData = {
            username: document.getElementById('api_username').value,
            old_password: oldPass,
            new_password: newPass,
            confirm_password: confirmPass
        };

        try {
            const response = await fetch('change_password_api.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            });
            const data = await response.json();

            if (data.status === 'success') {
                showAlert('>> SUCCESS: ' + (data.message || 'CAP NHAT MAT KHAU THANH CONG!'), 'alert-success');
                changePassForm.reset();
            } else {
                showAlert('! LOI: ' + (data.message || 'KHONG THE THAY DOI MAT KHAU!'), 'alert-error');
            }
        } catch (err) {
            showAlert('! LOI: KHONG THE KET NOI DEN HE THONG MAY CHU !', 'alert-error');
        } finally {
            btnSave.innerText = 'XAC NHAN THAY DOI';
            btnSave.disabled = false;
        }
    };

    function showAlert(text, className) {
        alertBox.innerText = text;
        alertBox.className = `alert ${className}`;
        alertBox.style.display = 'block';
    }
</script>

</body>
</html>