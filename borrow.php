<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUAN LY MUON TRA </title>
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
            padding: 25px; 
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
            max-width: 1200px; 
            margin: 0 auto; 
            background: #000000; 
            padding: 25px;
            border: 4px double #00FF00; 
            box-shadow: 6px 6px 0px #003300;
        }
        
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

        h1 { font-size: 38px; font-weight: bold; color: #FFFF00; margin-bottom: 25px; text-shadow: 2px 2px #FF0000; }
        
        .form-flex { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        @media (max-width: 768px) {
            .form-flex { flex-direction: column; }
        }

        .card { 
            background: #000000; 
            padding: 20px; 
            border: 3px solid #00FF00; 
            flex: 1; 
            box-shadow: 4px 4px 0px #002200;
        }
        .card-return {
            border-color: #00FFFF; 
            box-shadow: 4px 4px 0px #002222;
        }
        
        .section-title {
            font-size: 24px;
            color: #FFFF00;
            padding: 5px 10px;
            background: #001100;
            border: 2px solid #00FF00;
            margin-bottom: 20px;
            display: inline-block;
        }
        .title-green { 
            border-color: #00FFFF; 
            color: #FFFF00;
            background: #001111;
        }

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 18px; color: #00FFFF; margin-bottom: 5px; }
        .card-return .input-group label { color: #FF00FF; } 
        
        input { 
            width: 100%; 
            background: #000000; 
            border: 2px solid #00FF00; 
            padding: 8px 12px; 
            color: #00FF00; 
            outline: none;
            font-size: 22px;
        }
        input:focus { border-color: #FFFF00; background: #001100; }
        .card-return input { border-color: #00FFFF; color: #00FFFF; }
        .card-return input:focus { border-color: #FFFF00; background: #001111; }

        .btn { 
            padding: 10px; 
            border: 2px dashed #000000; 
            font-weight: bold; 
            font-size: 20px;
            cursor: pointer; 
            width: 100%; 
            text-transform: uppercase;
        }
        .btn-blue { background: #00FF00; color: #000000; }
        .btn-blue:hover { background: #FFFF00; }
        .btn-green { background: #00FFFF; color: #000000; }
        .btn-green:hover { background: #FFFF00; }

        .search-container { margin-bottom: 25px; position: relative; max-width: 400px; }
        .search-container input { padding-left: 35px; background: #000000; border-color: #00FF00;}
        .search-container i { position: absolute; left: 12px; top: 14px; color: #00FF00; font-size: 16px; }

        .table-wrap { overflow-x: auto; border: 3px solid #00FF00; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; min-width: 950px; background: #000000; }
        th { 
            text-align: left; 
            padding: 12px 10px; 
            background: #002200; 
            font-size: 18px; 
            color: #FFFF00; 
            font-weight: bold;
            border-bottom: 3px solid #00FF00;
        }
        td { padding: 12px 10px; border-bottom: 1px dashed #004400; font-size: 20px; color: #00FF00; }
        tr:hover { background: #001100; } 
        
        .badge { padding: 3px 8px; border: 1px solid transparent; font-size: 16px; font-weight: bold; display: inline-block; }
        .status-brw { background: #000000; color: #FFFF00; border-color: #FFFF00; text-shadow: 0 0 3px #FFFF00; } 
        .status-ret { background: #002200; color: #00FF00; border-color: #00FF00; } 
        
        .msg { padding: 12px; margin-bottom: 20px; text-align: center; font-weight: bold; display: none; text-transform: uppercase; }
        .success { background: #000000; color: #00FF00; border: 2px dashed #00FF00; }
        .error { background: #000000; color: #FF00FF; border: 2px dashed #FF00FF; }

        .txt-id { color: #00FFFF; font-weight: bold; }
        .txt-sub { font-size: 16px; color: #008800; margin-top: 2px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-bar">
        <a href="index.php" class="btn-back"><i class="fa fa-arrow-left"></i> [ QUAY VE TRANG CHU ]</a>
        <div style="color: #00FFFF">
            <i class="fa fa-user-circle"></i> THU THU: <strong style="color: #FFFF00"><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></strong>
        </div>
    </div>

    <h1><i class="fa fa-exchange-alt" style="color: #FFFF00;"></i> QUAN LY MUON - TRA SACH</h1>

    <div id="alert-msg" class="msg"></div>

    <div class="form-flex">
        <div class="card">
            <div class="section-title"><i class="fa fa-plus-circle"></i> CHO MƯỢN SÁCH</div>
            <form id="borrowForm">
                <div class="input-group">
                    <label>> MÃ LƯỢT MƯỢN SÁCH:</label>
                    <input type="text" id="borrowid" placeholder="Ví dụ: M001, M002..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> MÃ SINH VIÊN:</label>
                    <input type="text" id="studentid" placeholder="Nhập mã số SV..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> MÃ SÁCH:</label>
                    <input type="text" id="bookid" placeholder="Nhập mã định danh sách..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> NGÀY MƯỢN ĐẦU KỲ:</label>
                    <input type="date" id="date_borrowed" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn btn-blue">XÁC NHẬN CHO MƯỢN</button>
            </form>
        </div>

        <div class="card card-return">
            <div class="section-title title-green"><i class="fa fa-undo"></i> NHẬN TRẢ SÁCH</div>
            <form id="returnForm">
                <div class="input-group">
                    <label>> MÃ LƯỢT CẦN TRẢ:</label>
                    <input type="text" id="borrow_id_return" placeholder="Nhập mã lượt mượn để trả sách..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> NGÀY TRẢ THỰC TẾ:</label>
                    <input type="date" id="date_return" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div style="height: 78px;" class="desktop-spacer"></div>
                <button type="submit" class="btn btn-green">XÁC NHẬN NHẬN TRẢ</button>
            </form>
        </div>
    </div>

    <div class="section-title"><i class="fa fa-list"></i> NHẬT KÝ MƯỢN TRẢ HỆ THỐNG</div>
    
    <div class="search-container">
        <i class="fa fa-search"></i>
        <input type="text" id="search" placeholder="Tim theo ten sinh vien, sach hoac ma muon..." autocomplete="off">
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>MÃ MƯỢN</th>
                    <th>SINH VIÊN</th>
                    <th>TÊN SÁCH</th>
                    <th>NGÀY MƯỢN</th>
                    <th>NGÀY TRẢ</th>
                    <th>TRẠNG THÁI</th>
                    <th style="text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>
</div>

<script>
const apiUrl = 'borrow_api.php';
let localBorrowData = [];

function loadBorrows(searchKey = '') {
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
                localBorrowData = [];
                tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #FF00FF;">! KHONG TIM THAY LICH SU MUON TRA NAO PHU HOP !</td></tr>`;
                return;
            }

            localBorrowData = data;

            data.forEach(row => {
                const dB = new Date(row.date_borrowed);
                const dateBorrowedStr = `${String(dB.getDate()).padStart(2, '0')}/${String(dB.getMonth()+1).padStart(2, '0')}/${dB.getFullYear()}`;
                
                let dateReturnStr = "---";
                if(row.date_return && row.date_return !== '0000-00-00') {
                    const dR = new Date(row.date_return);
                    dateReturnStr = `${String(dR.getDate()).padStart(2, '0')}/${String(dR.getMonth()+1).padStart(2, '0')}/${dR.getFullYear()}`;
                }

                const isBorrowing = row.status === 'BORROWING';
                const badgeClass = isBorrowing ? 'status-brw' : 'status-ret';
                const badgeText = isBorrowing ? 'ĐANG MƯỢN' : 'ĐÃ TRẢ';

                const actionBtnReturn = isBorrowing ? 
                    `<a href="#" onclick="quickReturn('${row.borrowid}')" style="color: #FFFF00; margin-right: 15px;" title="Trả nhanh"><i class="fa fa-share-square"></i></a>` : '';

                tbody.innerHTML += `
                    <tr>
                        <td><span class="txt-id">${row.borrowid}</span></td>
                        <td>
                            <div style="color: #00FF00; font-weight: bold;">${row.studentname}</div>
                            <div class="txt-sub">Mã SV: ${row.studentid}</div>
                        </td>
                        <td><strong style="color: #FFFF00;">${row.bookname}</strong><br><span class="txt-sub">Mã sách: ${row.bookid}</span></td>
                        <td>${dateBorrowedStr}</td>
                        <td>${dateReturnStr}</td>
                        <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                        <td style="text-align: center;">
                            ${actionBtnReturn}
                            <a href="#" onclick="deleteBorrow('${row.borrowid}')" style="color: #FF00FF;" title="Xóa bản ghi"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                `;
            });
        });
}

window.onload = () => loadBorrows();

document.getElementById('search').addEventListener('input', (e) => {
    loadBorrows(e.target.value);
});

document.getElementById('borrowForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const borrowData = {
        borrowid: document.getElementById('borrowid').value,
        studentid: document.getElementById('studentid').value,
        bookid: document.getElementById('bookid').value,
        date_borrowed: document.getElementById('date_borrowed').value
    };

    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(borrowData)
    })
    .then(res => res.json())
    .then(resData => {
        if(resData.error) {
            showAlert("! LOI: " + resData.error, 'error');
        } else {
            showAlert(">> SUCCESS: THUC HIEN CHO MUON THANH CONG!", 'success');
            document.getElementById('borrowForm').reset();
            document.getElementById('date_borrowed').value = new Date().toISOString().split('T')[0];
            loadBorrows();
        }
    });
});

document.getElementById('returnForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const currentBorrowId = document.getElementById('borrow_id_return').value;
    const inputReturnDate = document.getElementById('date_return').value;

    const matchedRecord = localBorrowData.find(item => item.borrowid === currentBorrowId);

    if (matchedRecord) {
        const borrowDate = matchedRecord.date_borrowed; 
        
        if (inputReturnDate < borrowDate) {
            const d = new Date(borrowDate);
            const formattedBorrowDate = `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth()+1).padStart(2, '0')}/${d.getFullYear()}`;
            showAlert(`! LOI LOGIC: NGAY TRA PHAI LON HON HOAC BANG NGAY MUON (${formattedBorrowDate}) !`, 'error');
            return; 
        }
    }

    const returnData = {
        borrowid: currentBorrowId,
        date_return: inputReturnDate
    };

    fetch(apiUrl, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(returnData)
    })
    .then(res => res.json())
    .then(resData => {
        if(resData.error) {
            showAlert("! LOI: " + resData.error, 'error');
        } else {
            showAlert(">> SUCCESS: HE THONG DA NHAN LAI SACH THANH CONG!", 'success');
            document.getElementById('returnForm').reset();
            document.getElementById('date_return').value = new Date().toISOString().split('T')[0];
            loadBorrows();
        }
    });
});

function quickReturn(id) {
    document.getElementById('borrow_id_return').value = id;
    document.getElementById('borrow_id_return').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function deleteBorrow(id) {
    if(confirm('CANH BAO: XOA DON NAY SE HOI LAI SO LUONG SACH TRONG KHO NEU TRANG THAI LA CHUA TRA. BAN CHAC CHU?')) {
        fetch(`${apiUrl}?borrowid=${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) {
                showAlert("! LOI: " + resData.error, 'error');
            } else {
                showAlert(">> DELETED: XOA BAN GHI MUON TRA THANH CONG!", 'success');
                loadBorrows();
            }
        });
    }
}

function showAlert(text, type) {
    const alertBox = document.getElementById('alert-msg');
    alertBox.innerHTML = text;
    alertBox.className = `msg ${type}`;
    alertBox.style.display = 'block';
    setTimeout(() => { alertBox.style.display = 'none'; }, 4500);
}
</script>
</body>
</html>


<!--Manh dua file borrow-->
