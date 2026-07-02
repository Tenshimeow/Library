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
    <title>QUAN LY SACH 123456</title>
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

        h1 { font-size: 38px; font-weight: bold; color: #FFFF00; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; text-shadow: 2px 2px #FF0000; }
        
        
        .section-title {
            font-size: 24px;
            color: #FFFF00;
            padding: 5px 10px;
            background: #001100;
            border: 2px solid #00FF00;
            margin-bottom: 20px;
            display: inline-block;
        }

    
        .form-container {
            background: #000000;
            border: 3px solid #00FF00;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 4px 4px 0px #002200;
        }

        .form-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 15px; 
            margin-bottom: 15px; 
        }
        .input-group label { 
            display: block; 
            font-size: 18px; 
            color: #00FFFF; 
            margin-bottom: 5px; 
        }
        
        
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
        
    
        .btn { 
            padding: 8px 20px; 
            border: 2px dashed #000000; 
            font-weight: bold; 
            font-size: 20px;
            cursor: pointer; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-transform: uppercase;
        }
        .btn-submit { background: #00FF00; color: #000000; }
        .btn-submit:hover { background: #FFFF00; }
        .btn-edit { background: #FF00FF; color: #000000; } 
        .btn-edit:hover { background: #FFFF00; }

        
        .search-container { margin-bottom: 25px; position: relative; max-width: 400px; }
        .search-container input { padding-left: 35px; background: #000000; }
        .search-container i { position: absolute; left: 12px; top: 14px; color: #00FF00; font-size: 16px; }

       
        .table-wrap { overflow-x: auto; border: 3px solid #00FF00; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; background: #000000; }
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
        
      
        .cate-badge { 
            background: #002200; 
            color: #00FFFF; 
            padding: 2px 6px; 
            border: 1px solid #00FFFF; 
            font-size: 16px; 
        }
        
        .badge-id { color: #FFFF00; font-weight: bold; }
        .txt-avail { color: #00FF00; font-weight: bold; text-shadow: 0 0 3px #00FF00; }

       
        .msg { padding: 12px; margin-bottom: 20px; text-align: center; font-weight: bold; display: none; text-transform: uppercase; }
        .success { background: #000000; color: #00FF00; border: 2px dashed #00FF00; }
        .error { background: #000000; color: #FF00FF; border: 2px dashed #FF00FF; }
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

    <h1><i class="fa fa-book-open" style="color: #FFFF00;"></i> QUAN LY KHO SACH</h1>

    <div id="alert-msg" class="msg"></div>

    <div class="form-container">
        <div class="section-title" id="form-title">
            <i class="fa fa-plus-circle"></i> NHẬP SÁCH MỚI VÀO KHO
        </div>
        
        <form id="bookForm">
            <input type="hidden" id="action_mode" value="insert">
            <div class="form-grid">
                <div class="input-group">
                    <label>> MÃ SÁCH (ID):</label>
                    <input type="text" id="bookid" placeholder="VD: 001, 002..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> TÊN ĐẦU SÁCH:</label>
                    <input type="text" id="bookname" placeholder="Lap trinh PHP co ban..." required autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> TÁC GIẢ:</label>
                    <input type="text" id="author" placeholder="Nguyen Van A" autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> NHÀ XUẤT BẢN:</label>
                    <input type="text" id="publisher" placeholder="NXB Giao duc..." autocomplete="off">
                </div>
                <div class="input-group">
                    <label>> PHÂN LOẠI:</label>
                    <select id="category">
                        <option value="" disabled selected>-- Chọn thể loại --</option>
                        <?php 
                        $opts = ["CNTT", "Kinh tế", "Ngoại ngữ", "Kỹ năng", "Văn học", "Marketing", "BigData", "CSDL phân tán", "Đại Cương", "Khác"];
                        foreach($opts as $o) echo "<option value='$o'>$o</option>";
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>> TỔNG SỐ LƯỢNG:</label>
                    <input type="number" id="quantity" placeholder="So luong nhap kho" required>
                </div>
                <div class="input-group" id="avail-group" style="display: none;">
                    <label>> SỐ LƯỢNG KHẢ DỤNG (SẴN CÓ):</label>
                    <input type="number" id="available" placeholder="So sach thuc te con lai" style="border-color: #00FFFF; color: #00FFFF;">
                </div>
            </div>

            <div style="text-align: right; padding-top: 15px;">
                <button type="button" id="btn-cancel" style="color: #FF00FF; margin-right: 20px; font-size: 20px; background:none; border:none; display:none; cursor:pointer; text-decoration: underline;">[ HUY BO ]</button>
                <button type="submit" id="btn-submit" class="btn btn-submit">XÁC NHẬN THÊM</button>
            </div>
        </form>
    </div>

    <div class="section-title"><i class="fa fa-list"></i> DANH MỤC SÁCH TRONG KHO HỆ THỐNG</div>
    
    <div class="search-container">
        <i class="fa fa-search"></i>
        <input type="text" id="search" placeholder="Tim theo ten sach hoac ma..." autocomplete="off">
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TÊN SÁCH</th>
                    <th>TÁC GIẢ</th>
                    <th>NHÀ XUẤT BẢN</th>
                    <th>LOẠI</th>
                    <th style="text-align: center;">KHO</th>
                    <th style="text-align: center;">SẴN CÓ</th>
                    <th style="text-align: center;">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody id="table-body">
                </tbody>
        </table>
    </div>
</div>

<script>
const apiUrl = 'book_api.php';

function loadBooks(searchKey = '') {
    let url = apiUrl;
    if(searchKey) {
        url += `?key=${encodeURIComponent(searchKey)}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('table-body');
            tbody.innerHTML = '';
            
            if(data.length === 0 || data.message) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 30px; color: #FF00FF;">! KHONG TIM THAY CUON SACH NAO PHU HOP !</td></tr>`;
                return;
            }

            data.forEach(b => {
                tbody.innerHTML += `
                    <tr>
                        <td><span class="badge-id">${b.bookid}</span></td>
                        <td><strong style="color: #00FF00;">${b.bookname}</strong></td>
                        <td>${b.author ? b.author : '<i style="color:#004400">Chua ro</i>'}</td>
                        <td>${b.publisher ? b.publisher : '<i style="color:#004400">Chua ro</i>'}</td>
                        <td><span class="cate-badge">${b.category || 'Khác'}</span></td>
                        <td style="text-align: center;">${b.quantity}</td>
                        <td style="text-align: center;" class="txt-avail">${b.available}</td>
                        <td style="text-align: center;">
                            <a href="#" onclick="editBook('${b.bookid}')" style="color: #FFFF00; margin-right: 15px;" title="Sửa"><i class="fa fa-edit"></i></a>
                            <a href="#" onclick="deleteBook('${b.bookid}')" style="color: #FF00FF;" title="Xóa"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                `;
            });
        });
}

window.onload = () => loadBooks();


document.getElementById('search').addEventListener('input', (e) => {
    loadBooks(e.target.value);
});


document.getElementById('bookForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const mode = document.getElementById('action_mode').value;
    const bookData = {
        bookid: document.getElementById('bookid').value,
        bookname: document.getElementById('bookname').value,
        author: document.getElementById('author').value,
        publisher: document.getElementById('publisher').value,
        category: document.getElementById('category').value,
        quantity: parseInt(document.getElementById('quantity').value)
    };

    if (mode === 'update') {
        bookData.available = parseInt(document.getElementById('available').value);
    }

    const method = (mode === 'insert') ? 'POST' : 'PUT';

    fetch(apiUrl, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bookData)
    })
    .then(res => res.json())
    .then(resData => {
        if(resData.error) {
            showAlert("! LOI: " + resData.error, 'error');
        } else {
            showAlert(">> SUCCESS: " + (resData.message || 'Thao tac thanh cong!'), 'success');
            resetForm();
            loadBooks();
        }
    });
});

function deleteBook(id) {
    if(confirm('XOA DAU SACH NAY KHOI KHO? THAO TAC KHONG THE HOAN TAC!')) {
        fetch(`${apiUrl}?bookid=${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(resData => {
            if(resData.error) {
                showAlert("! LOI: " + resData.error, 'error');
            } else {
                showAlert(">> DELETED: XOA DU LIEU SACH THANH CONG!", 'success');
                loadBooks();
            }
        });
    }
}

function editBook(id) {
    fetch(`${apiUrl}?key=${id}`)
        .then(res => res.json())
        .then(books => {
            if(!books || books.length === 0 || books.message) return;
            const b = books[0]; 
            
            document.getElementById('action_mode').value = 'update';
            document.getElementById('bookid').value = b.bookid;
            document.getElementById('bookid').disabled = true;
            document.getElementById('bookid').style.background = '#001100';
            document.getElementById('bookid').style.cursor = 'not-allowed';
            document.getElementById('bookid').style.borderColor = '#FF00FF';
            
            document.getElementById('bookname').value = b.bookname;
            document.getElementById('author').value = b.author || '';
            document.getElementById('publisher').value = b.publisher || '';
            document.getElementById('category').value = b.category || '';
            document.getElementById('quantity').value = b.quantity;
            
            document.getElementById('avail-group').style.display = 'block';
            document.getElementById('available').value = b.available;

            
            document.getElementById('form-title').innerHTML = `<i class="fa fa-edit"></i> CAP NHAT THONG TIN SACH: ${b.bookid}`;
            const submitBtn = document.getElementById('btn-submit');
            submitBtn.className = 'btn btn-edit';
            submitBtn.innerHTML = `<i class="fa fa-save"></i> LUU THAY DOI`;
            document.getElementById('btn-cancel').style.display = 'inline-block';
        });
}

function resetForm() {
    document.getElementById('bookForm').reset();
    document.getElementById('action_mode').value = 'insert';
    document.getElementById('bookid').disabled = false;
    document.getElementById('bookid').style.background = '#000000';
    document.getElementById('bookid').style.cursor = 'text';
    document.getElementById('bookid').style.borderColor = '#00FF00';
    document.getElementById('avail-group').style.display = 'none';
    
    document.getElementById('form-title').innerHTML = `<i class="fa fa-plus-circle"></i> NHẬP SÁCH MỚI VÀO KHO`;
    const submitBtn = document.getElementById('btn-submit');
    submitBtn.className = 'btn btn-submit';
    submitBtn.innerHTML = `XÁC NHẬN THÊM`;
    document.getElementById('btn-cancel').style.display = 'none';
}

document.getElementById('btn-cancel').addEventListener('click', resetForm);

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
HEAD


<!--Hoan da sua file book.php-->

