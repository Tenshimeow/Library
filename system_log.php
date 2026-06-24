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
    <title>Nhật ký hệ thống - Library System</title>
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
            background-color: #000000; 
            color: #00FF00; 
            padding: 20px; 
            font-size: 22px;
            position: relative;
            min-height: 100vh;
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

        .container { 
            max-width: 1140px; 
            margin: 0 auto; 
            background: #000000; 
            padding: 30px;
            border: 4px double #00FF00; 
            box-shadow: 6px 6px 0px #003300; 
        }
        
        .header { 
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

        h2 { font-size: 38px; font-weight: bold; color: #FFFF00; text-shadow: 2px 2px #FF0000; text-transform: uppercase;}

        .admin-msg-card { 
            background: #000000; 
            border: 3px solid #00FF00; 
            padding: 20px; 
            margin-bottom: 30px; 
            box-shadow: 4px 4px 0px #002200;
        }
        .section-title {
            font-size: 24px;
            color: #FFFF00;
            padding: 5px 10px;
            background: #001100;
            border: 2px solid #00FF00;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
        }
        
        .msg-item { 
            border-bottom: 1px dashed #004400; 
            padding: 12px 0; 
        }
        .msg-item:last-child { border: none; }
        .msg-meta { font-size: 18px; color: #00FFFF; margin-bottom: 4px; }
        
        input { 
            width: 100%; 
            background: #000000; 
            border: 2px solid #00FF00; 
            padding: 8px 12px; 
            color: #00FF00; 
            outline: none;
            font-size: 22px;
        }
        input:focus { 
            border-color: #FFFF00; 
            background: #001100;
        }

        .btn-send {
            background: #00FF00; 
            color: #000000; 
            border: none; 
            padding: 8px 20px; 
            cursor: pointer;
            font-weight: bold;
            font-size: 20px;
            text-transform: uppercase;
        }
        .btn-send:hover { background: #FFFF00; }

        .search-container { position: relative; max-width: 350px; }
        .search-container input { padding-left: 35px; }
        .search-container i { position: absolute; left: 12px; top: 14px; color: #00FF00; font-size: 16px; }

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
        
        .role-admin { color: #FF00FF; font-weight: bold; text-shadow: 0 0 3px #FF00FF; }
        .role-staff { color: #00FFFF; font-weight: bold; text-shadow: 0 0 3px #00FFFF; }
        
        .action-tag { 
            background: #000000; 
            color: #FFFF00;
            padding: 2px 8px; 
            font-size: 16px; 
            font-weight: bold;
            border: 1px dashed #FFFF00;
            text-transform: uppercase;
        }
        .btn-del { color: #888888; text-decoration: none; cursor: pointer; font-size: 20px; }
        .btn-del:hover { color: #FF0000; text-shadow: 0 0 5px #FF0000; }

        .msg { padding: 12px; margin-bottom: 20px; text-align: center; font-weight: bold; display: none; text-transform: uppercase; }
        .success { background: #000000; color: #00FF00; border: 2px dashed #00FF00; }
        .error { background: #000000; color: #FF00FF; border: 2px dashed #FF00FF; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar header">
        <h2><i class="fa fa-history" style="color: #FFFF00;"></i> Nhật ký hoạt động hệ thống</h2>
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> [ QUAY LAI TRANG CHU ]</a>
    </div>

    <div id="alert-msg" class="msg"></div>

    <div class="admin-msg-card">
        <div class="section-title"><i class="fa fa-bullhorn"></i> Thông báo nội bộ từ Ban quản trị</div>
        
        <input type="hidden" id="current_username" value="<?php echo htmlspecialchars($username); ?>">
        <input type="hidden" id="current_role" value="<?php echo htmlspecialchars($role); ?>">
        
        <?php if($role === "ADMIN"): ?>
        <form id="noteForm" style="display:flex; gap:10px; margin-bottom:20px;">
            <input type="text" id="note_content" placeholder="Nhập nội dung thông báo nội bộ mới..." required autocomplete="off">
            <button type="submit" class="btn-send">ĐĂNG TIN</button>
        </form>
        <?php endif; ?>

        <div id="note-list"></div>
    </div>

    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <span style="font-size: 20px; color: #00FFFF; font-weight: bold;">> DANH SACH 100 HOAT DONG GAN NHAT</span>
        
        <div class="search-container">
            <i class="fa fa-search"></i>
            <input type="text" id="searchLog" placeholder="Tìm tài khoản hoặc hành động..." autocomplete="off">
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Nhân sự thực hiện</th>
                    <th>Nghiệp vụ</th>
                    <th>Nội dung chi tiết hoạt động</th>
                    <?php if($role === "ADMIN"): ?><th style="text-align: center;"><i class="fa fa-trash"></i></th><?php endif; ?>
                </tr>
            </thead>
            <tbody id="log-table-body"></tbody>
        </table>
    </div>
</div>

<script>
const apiLogUrl = 'system_log_api.php';
const myUsername = document.getElementById('current_username').value;
const myRole = document.getElementById('current_role').value;

function initSystemPage(searchKey = '') {
    let fetchUrl = apiLogUrl;
    if(searchKey) {
        fetchUrl += `?search=${encodeURIComponent(searchKey)}`;
    }

    fetch(fetchUrl)
        .then(res => res.json())
        .then(data => {
            const noteContainer = document.getElementById('note-list');
            noteContainer.innerHTML = '';
            
            if(data.notes.length === 0) {
                noteContainer.innerHTML = `<div style="font-size:20px; color:#888888; text-align:center;">! Hien tai khong co thong bao noi bo nao !</div>`;
            } else {
                data.notes.forEach(n => {
                    const dN = new Date(n.created_at);
                    const timeStr = `${String(dN.getHours()).padStart(2, '0')}:${String(dN.getMinutes()).padStart(2, '0')} - ${String(dN.getDate()).padStart(2, '0')}/${String(dN.getMonth()+1).padStart(2, '0')}/${dN.getFullYear()}`;
                    
                    const delBtn = (myRole === 'ADMIN') ? 
                        `<a onclick="deleteNote(${n.id})" class="btn-del" style="float:right"><i class="fa fa-times"></i></a>` : '';

                    noteContainer.innerHTML += `
                        <div class="msg-item">
                            <div class="msg-meta">
                                <strong><i class="fa fa-user-shield"></i> Admin (${n.username})</strong> • ${timeStr}
                                ${delBtn}
                            </div>
                            <div style="font-size: 22px; color:#00FF00; margin-top:4px;">>> ${n.note_content}</div>
                        </div>
                    `;
                });
            }

            const tbody = document.getElementById('log-table-body');
            tbody.innerHTML = '';

            if(data.logs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${myRole === 'ADMIN' ? 5 : 4}" style="text-align: center; padding: 25px; color: #FF00FF;">! KHONG TIM THAY LICH SU NHAT KY TUONG UNG !</td></tr>`;
                return;
            }

            data.logs.forEach(l => {
                const dL = new Date(l.action_time);
                const logTimeStr = `${String(dL.getDate()).padStart(2, '0')}/${String(dL.getMonth()+1).padStart(2, '0')} ${String(dL.getHours()).padStart(2, '0')}:${String(dL.getMinutes()).padStart(2, '0')}`;

                const userClass = (l.user_role === 'ADMIN') ? 'role-admin' : 'role-staff';
                const delLogAction = (myRole === 'ADMIN') ? 
                    `<td style="text-align: center;"><a onclick="deleteLog(${l.id})" class="btn-del" title="Xóa dòng log"><i class="fa fa-trash"></i></a></td>` : '';

                tbody.innerHTML += `
                    <tr>
                        <td style="color:#FFFF00; font-family:monospace;">${logTimeStr}</td>
                        <td><span class="${userClass}">${l.username}</span></td>
                        <td><span class="action-tag">${l.action_type}</span></td>
                        <td style="color:#ffffff;">${l.action_detail}</td>
                        ${delLogAction}
                    </tr>
                `;
            });
        });
}

window.onload = () => initSystemPage();

const searchInput = document.getElementById('searchLog');
if(searchInput) {
    searchInput.addEventListener('input', (e) => {
        initSystemPage(e.target.value);
    });
}

const noteForm = document.getElementById('noteForm');
if(noteForm) {
    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const requestData = {
            action: 'add_note',
            username: myUsername,
            note_content: document.getElementById('note_content').value
        };

        fetch(apiLogUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) {
                showAlert("! LOI: " + resData.error, 'error');
            } else {
                showAlert(">> SUCCESS: " + resData.message, 'success');
                noteForm.reset();
                initSystemPage();
            }
        });
    });
}

function deleteNote(id) {
    if(confirm('BAN CO CHAC MUON GO BO DONG THONG BAO NOI BO NAY KHOI BANG TIN?')) {
        fetch(apiLogUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ target: 'note', id: id, username: myUsername })
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) showAlert("! LOI: " + resData.error, 'error');
            else {
                showAlert(">> DELETED: " + resData.message, 'success');
                initSystemPage();
            }
        });
    }
}

function deleteLog(id) {
    if(confirm('XOA VINH VIEN BAN GHI NHAT KY HOAT DONG NAY KHOI CORE?')) {
        fetch(apiLogUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ target: 'log', id: id })
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) showAlert("! LOI: " + resData.error, 'error');
            else {
                showAlert(">> FLUSHED: " + resData.message, 'success');
                initSystemPage();
            }
        });
    }
}

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


<!--Ngoc huy ghi log o day-->
