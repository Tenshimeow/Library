<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
header("Location: librarian_api.php");
exit();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUAN LY THU THU </title>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- STYLE GIAO DIỆN ĐỒ HỌA GAME RETRO CHỨC NĂNG VÀ HỆ THỐNG --- */
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'VT323', monospace; 
        }
        
        body { 
            background-color: #000000; /* Nền đen tivi cổ điển */
            color: #00FF00; /* Chữ màu xanh lá neon */
            padding: 20px; 
            font-size: 22px;
            position: relative;
            min-height: 100vh;
        }

        /* Hiệu ứng màn hình CRT (Sọc quét ngang tivi cổ điển) */
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

        .container { 
            max-width: 1140px; 
            margin: 0 auto; 
            background: #000000; 
            padding: 30px;
            border: 4px double #00FF00; /* Viền đôi xanh neon */
            box-shadow: 6px 6px 0px #003300; 
        }
        
        /* THANH ĐIỀU HƯỚNG TRÊN CÙNG TRONG CONSOLE */
        .nav-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding-bottom: 15px;
            border-bottom: 4px double #00FF00;
            margin-bottom: 25px; 
        }
        .btn-back { 
            text-decoration: none; 
            color: #FFFF00; 
        }
        .btn-back:hover { 
            color: #00FFFF;
            text-shadow: 0 0 5px #00FFFF; 
        }

        h1 { font-size: 38px; font-weight: bold; color: #FFFF00; margin-bottom: 25px; text-shadow: 2px 2px #FF0000; text-transform: uppercase;}
        
        /* TIÊU ĐỀ PHÂN ĐOẠN PHONG CÁCH COMMAND BOX */
        .section-title {
            font-size: 24px;
            color: #FFFF00;
            padding: 5px 10px;
            background: #001100;
            border: 2px solid #00FF00;
            margin-bottom: 20px;
            display: inline-block;
            text-transform: uppercase;
        }

        .form-container {
            background: #000000;
            border: 3px solid #00FF00;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 4px 4px 0px #002200;
        }

        /* GRID NHẬP LIỆU PHẲNG TRÊN ĐỒ HỌA TERMINAL */
        .form-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 15px; 
            margin-bottom: 15px; 
        }
        .input-group label { 
            display: block; 
            font-size: 18px; 
            color: #00FFFF; 
            margin-bottom: 5px; 
            font-weight: bold; 
        }
        
        /* Ô NHẬP LIỆU DÒNG LỆNH CỔ ĐIỂN */
        input, select { 
            width: 100%; 
            background: #000000; 
            border: 2px solid #00FF00; 
            padding: 8px 12px; 
            color: #00FF00; 
            outline: none;
            font-size: 22px;
        }
        input:focus, select:focus { 
            border-color: #FFFF00; 
            background: #001100;
        }
        select option {
            background: #000000;
            color: #00FF00;
        }
        
        .btn-row { display: flex; gap: 15px; margin-top: 15px; }
        
        /* NÚT BẤM ĐIỀU KHIỂN HÀNH ĐỘNG GAME */
        .btn { 
            padding: 10px 20px; 
            border: none; 
            font-weight: bold; 
            font-size: 20px;
            cursor: pointer; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-transform: uppercase;
        }
        .btn-submit { background: #00FF00; color: #000000; flex: 1; justify-content: center; }
        .btn-submit:hover { background: #FFFF00; }
        .btn-cancel { background: #000000; color: #FF00FF; border: 2px solid #FF00FF; }
        .btn-cancel:hover { background: #220022; text-shadow: 0 0 5px #FF00FF; }

        /* KHUNG TÌM KIẾM TỪ KHÓA BẤT ĐỒNG BỘ */
        .search-container { margin-bottom: 25px; position: relative; max-width: 400px; }
        .search-container input { padding-left: 35px; }
        .search-container i { position: absolute; left: 12px; top: 14px; color: #00FF00; font-size: 16px; }

        /* BẢNG DỮ LIỆU ĐỒ HỌA GRID LƯỚI TERMINAL */
        .table-wrap { overflow-x: auto; border: 3px solid #00FF00; }
        table { width: 100%; border-collapse: collapse; min-width: 950px; background: #000000; }
        th { 
            text-align: left; 
            padding: 12px 10px; 
            background: #002200; 
            font-size: 18px; 
            color: #FFFF00; 
            font-weight: bold;
            border-bottom: 3px solid #00FF00;
            text-transform: uppercase;
        }
        td { padding: 12px 10px; border-bottom: 1px dashed #004400; font-size: 22px; color: #00FF00; }
        tr:hover { background: #001100; } 
        
        /* BADGES TRẠNG THÁI PHÂN QUYỀN VÀ HOẠT ĐỘNG */
        .badge { padding: 2px 6px; border: 1px dashed; font-size: 16px; font-weight: bold; display: inline-block; text-transform: uppercase; }
        .status-active { color: #00FF00; border-color: #00FF00; }
        .status-inactive { color: #FF0000; border-color: #FF0000; }
        .role-badge { color: #00FFFF; border-color: #00FFFF; }
        
        /* KHUNG HIỂN THỊ THÔNG BÁO FLASH */
        .msg { padding: 12px; margin-bottom: 20px; text-align: center; font-weight: bold; display: none; text-transform: uppercase; }
        .success { background: #000000; color: #00FF00; border: 2px dashed #00FF00; }
        .error { background: #000000; color: #FF00FF; border: 2px dashed #FF00FF; }

        .txt-id { color: #FFFF00; font-weight: bold; }
        .txt-sub { font-size: 18px; color: #00FFFF; margin-top: 2px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> [ QUAY VE TRANG CHU ]</a>
        <div style="color: #00FFFF">
            <i class="fa fa-user-circle"></i> LOGGED IN: <strong style="color: #FFFF00"><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></strong>
        </div>
    </div>

    <h1><i class="fa fa-users-cog" style="color: #FFFF00;"></i> QUAN LY NHAN SU THU THU</h1>

    <div id="alert-msg" class="msg"></div>

    <div class="form-container">
        <div class="section-title" id="form-title">
            <i class="fa fa-user-plus"></i> THEM THU THU MOI
        </div>
        
        <form id="librarianForm">
            <input type="hidden" id="action_mode" value="insert">
            
            <div class="form-grid">
                <div class="input-group">
                    <label>> MA THU THU:</label>
                    <input type="text" id="librarianid" placeholder="VD: 001" required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> HO VA TEN:</label>
                    <input type="text" id="librarianname" placeholder="Nguyen Van B" required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> EMAIL LIEN HE:</label>
                    <input type="email" id="email" placeholder="example@library.com" required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> SO DIEN THOAI:</label>
                    <input type="text" id="phone" placeholder="09xxxx..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> TEN DANG NHAP (USERNAME):</label>
                    <input type="text" id="username" placeholder="user123" required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> MAT KHAU TRUY CAP:</label>
                    <input type="text" id="password" placeholder="999999" required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> PHAN QUYEN HE THONG:</label>
                    <select id="role">
                        <option value="LIBRARIAN">THU THU (LIBRARIAN)</option>
                        <option value="ADMIN">QUAN TRI VIEN (ADMIN)</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>> TRANG THAI TAI KHOAN:</label>
                    <select id="status">
                        <option value="ACTIVE">DANG HOAT DONG</option>
                        <option value="INACTIVE">TAM KHOA</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>> DIA CHI THUONG TRU:</label>
                <input type="text" id="address" placeholder="Dia chi chi tiet so nha, ten duong..." autocomplete="off">
            </div>

            <div class="btn-row">
                <button type="submit" id="btn-submit" class="btn btn-submit">
                    <i class="fa fa-save"></i> XAC NHAN THEM MOI
                </button>
                <button type="button" id="btn-cancel" class="btn btn-cancel" style="display: none;">[ HUY BO ]</button>
            </div>
        </form>
    </div>

    <div class="section-title"><i class="fa fa-users"></i> DANH SACH DOI NGU NHAN SU</div>
    
    <div class="search-container">
        <i class="fa fa-search"></i>
        <input type="text" id="search" placeholder="Tim kiem theo ten hoac ma nhan vien..." autocomplete="off">
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>NHAN VIEN</th>
                    <th>LIEN HE</th>
                    <th>TAI KHOAN</th>
                    <th>MAT KHAU</th>
                    <th>VAI TRO</th>
                    <th>TRANG THAI</th>
                    <th style="text-align: right;">THAO TAC</th>
                </tr>
            </thead>
            <tbody id="table-body">
                </tbody>
        </table>
    </div>
</div>

<script>
const apiUrl = 'librarian_api.php';

// 1. HÀM LOAD DANH SÁCH NHÂN SỰ THỦ THƯ (GET)
function loadLibrarians(searchKey = '') {
    let url = apiUrl;
    if(searchKey) {
        url += `?key=${encodeURIComponent(searchKey)}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('table-body');
            tbody.innerHTML = '';
            
            if(!data || data.length === 0 || data.message === "No records found") {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 30px; color: #FF00FF;">! KHONG TIM THAY DU LIEU PHU HOP !</td></tr>`;
                return;
            }

            data.forEach(row => {
                const isActive = row.status === 'ACTIVE';
                const statusBadge = isActive ? 
                    `<span class="badge status-active">HOAT DONG</span>` : 
                    `<span class="badge status-inactive">DA KHOA</span>`;

                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div style="font-weight: bold; color: #ffffff;">${row.librarianname}</div>
                            <div class="txt-id">#ID: ${row.librarianid}</div>
                        </td>
                        <td>
                            <div style="font-size: 18px;"><i class="fa fa-envelope" style="font-size: 14px; color: #00FFFF;"></i> ${row.email}</div>
                            <div class="txt-sub"><i class="fa fa-phone" style="font-size: 14px; color: #00FFFF;"></i> ${row.phone}</div>
                        </td>
                        <td><span style="color: #FFFF00;">${row.username}</span></td>
                        <td>
                            <input type="password" value="${row.password}" 
                                   readonly 
                                   style="background:none; border:none; color:#00FF00; width:100px; padding:0; cursor:pointer; font-size:22px;"
                                   onclick="this.type=(this.type=='password'?'text':'password')" 
                                   title="Click de hien/an mat khau">
                        </td>
                        <td><span class="badge role-badge">${row.role}</span></td>
                        <td>${statusBadge}</td>
                        <td style="text-align: right;">
                            <a href="#" onclick="editLibrarian('${row.librarianid}')" style="color: #00FFFF; margin-right: 15px; font-size: 20px;" title="Chỉnh sửa"><i class="fa fa-edit"></i></a>
                            <a href="#" onclick="deactivateLibrarian('${row.librarianid}')" style="color: #FF00FF; font-size: 20px;" title="Khóa tài khoản"><i class="fa fa-user-slash"></i></a>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error("Lỗi đồng bộ danh sách:", err);
        });
}

// Chạy khởi tạo danh sách khi vừa load trang
window.onload = () => loadLibrarians();

// 2. TÌM KIẾM THEO TỪ KHÓA TRONG KHI GÕ (REALTIME SEARCH)
document.getElementById('search').addEventListener('input', (e) => {
    loadLibrarians(e.target.value);
});

// 3. XỬ LÝ LỆNH THÊM MỚI HOẶC SỬA HỒ SƠ (POST / PUT)
document.getElementById('librarianForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const mode = document.getElementById('action_mode').value;
    const staffData = {
        librarianid: document.getElementById('librarianid').value,
        librarianname: document.getElementById('librarianname').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value,
        status: document.getElementById('status').value,
        address: document.getElementById('address').value
    };

    const method = (mode === 'insert') ? 'POST' : 'PUT';

    fetch(apiUrl, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(staffData)
    })
    .then(res => res.json())
    .then(resData => {
        if(resData.error) {
            showAlert("! LOI: " + resData.error, 'error');
        } else {
            showAlert(resData.message || '>> SUCCESS: CAP NHAT CORE HE THONG THANH CONG!', 'success');
            resetForm();
            loadLibrarians();
        }
    })
    .catch(err => {
        showAlert("! LOI: BI CHAN KET NOI TRUY XUAT DU LIEU !", 'error');
    });
});

// 4. XỬ LÝ TẠM KHÓA TRẠNG THÁI TÀI KHOẢN (DELETE CHUYỂN INACTIVE)
function deactivateLibrarian(id) {
    if(confirm(`CANH BAO: BAN CHAC CHAN MUON KHOA QUYEN TRUY CAP CUA THU THU MA CO DINH [${id}] KHOI CORE?`)) {
        fetch(`${apiUrl}?librarianid=${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) {
                showAlert("! LOI: " + resData.error, 'error');
            } else {
                showAlert(">> DEACTIVATED: " + resData.message, 'success');
                loadLibrarians();
            }
        })
        .catch(err => {
            showAlert("! LOI: LENH XOA BI MAT KET NOI !", 'error');
        });
    }
}

// 5. ĐỔ DỮ LIỆU CŨ LÊN FORM ĐỂ HIỆU CHỈNH (UPDATE MODE)
function editLibrarian(id) {
    fetch(`${apiUrl}?key=${id}`)
        .then(res => res.json())
        .then(list => {
            if(!list || list.length === 0) return;
            // Tìm chính xác bản ghi khớp với id khóa chính
            const staff = list.find(item => item.librarianid === id) || list[0];
            
            document.getElementById('action_mode').value = 'update';
            document.getElementById('librarianid').value = staff.librarianid;
            document.getElementById('librarianid').disabled = true;
            document.getElementById('librarianid').style.background = '#220000';
            document.getElementById('librarianid').style.borderColor = '#FF00FF';
            document.getElementById('librarianid').style.cursor = 'not-allowed';
            
            document.getElementById('librarianname').value = staff.librarianname;
            document.getElementById('email').value = staff.email;
            document.getElementById('phone').value = staff.phone;
            document.getElementById('username').value = staff.username;
            document.getElementById('password').value = staff.password;
            document.getElementById('role').value = staff.role;
            document.getElementById('status').value = staff.status;
            document.getElementById('address').value = staff.address || '';

            // Đổi tiêu đề form trạng thái hiệu chỉnh hệ thống
            document.getElementById('form-title').innerHTML = `<i class="fa fa-user-edit" style="color:#FFFF00;"></i> HIEU CHINH HOSOTX: ${staff.librarianid}`;
            const submitBtn = document.getElementById('btn-submit');
            submitBtn.innerHTML = `<i class="fa fa-save"></i> LUU THAY DOI`;
            document.getElementById('btn-cancel').style.display = 'inline-block';
        });
}

// HÀM RESET KHÔI PHỤC FORM TRẠNG THÁI THÊM MỚI BAN ĐẦU
function resetForm() {
    document.getElementById('librarianForm').reset();
    document.getElementById('action_mode').value = 'insert';
    document.getElementById('librarianid').disabled = false;
    document.getElementById('librarianid').style.background = '#000000';
    document.getElementById('librarianid').style.borderColor = '#00FF00';
    document.getElementById('librarianid').style.cursor = 'text';
    document.getElementById('form-title').innerHTML = `<i class="fa fa-user-plus"></i> THEM THU THU MOI`;
    document.getElementById('btn-submit').innerHTML = `<i class="fa fa-save"></i> XAC NHAN THEM MOI`;
    document.getElementById('btn-cancel').style.display = 'none';
}

document.getElementById('btn-cancel').addEventListener('click', resetForm);

// HÀM HIỂN THỊ THÔNG BÁO ALERT BOX TERMINAL PANEL
function showAlert(text, type) {
    const alertBox = document.getElementById('alert-msg');
    alertBox.innerHTML = text;
    alertBox.className = `msg ${type}`;
    alertBox.style.display = 'block';
    setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
}
</script>
</body>
</html>