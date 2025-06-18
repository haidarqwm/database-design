<?php
session_start();
// Hapus semua data session ketika kembali ke menu awal
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Wahyu Listrik - Halaman Utama</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #ecf0f1;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('IMG_4283.jpg');
            background-size: cover;
            background-position: center top; /* Menggeser gambar ke atas */
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .container {
            background-color: rgba(81, 88, 94, 0.5); /* Mengubah alpha menjadi 0.5 untuk 50% transparansi */
            backdrop-filter: blur(10px); /* Meningkatkan blur untuk kompensasi transparansi */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            margin: auto;
        }
        h1 {
            color: hsl(0, 0%, 100%);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .menu-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .menu-button {
            background-color: rgba(41, 128, 185, 0.9);
            border: none;
            border-radius: 4px;
            color: #ecf0f1;
            cursor: pointer;
            font-size: 1rem;
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 400;
            backdrop-filter: blur(5px);
        }
        .menu-button:hover {
            background-color: rgba(52, 152, 219, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
        }
        .karyawan { background-color: rgba(226, 158, 32, 0.9); }
        .karyawan:hover { background-color: rgba(222, 200, 125, 0.9); }
        .owner { background-color: rgba(226, 158, 32, 0.9); }
        .owner:hover { background-color: rgba(222, 200, 125, 0.9); }
        .login-form {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(44, 62, 80, 0.95);
            border-radius: 4px;
            backdrop-filter: blur(10px);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ecf0f1;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(127, 140, 141, 0.5);
            border-radius: 4px;
            font-size: 1rem;
            background-color: rgba(52, 73, 94, 0.8);
            color: #ecf0f1;
            backdrop-filter: blur(5px);
        }
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        .submit-btn {
            background-color: rgba(231, 76, 60, 0.9);
            color: white;
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: 600;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: rgba(192, 57, 43, 0.9);
        }
        /* Styling untuk notifikasi */
        .notification {
            position: relative;
            margin-top: 15px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
        }
        .notification.error {
            background-color:rgba(226, 158, 32, 0.9);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
    <script>
        function toggleLoginForm(type) {
            var formKaryawan = document.getElementById("loginForm");
            var formOwner = document.getElementById("loginFormOwner");
            
            if (type === 'owner') {
                formOwner.style.display = formOwner.style.display === "none" ? "block" : "none";
                formKaryawan.style.display = "none"; // Sembunyikan form karyawan
            } else {
                formKaryawan.style.display = formKaryawan.style.display === "none" ? "block" : "none";
                formOwner.style.display = "none"; // Sembunyikan form owner
            }
        }

        function showNotification(message, type = 'error') {
            // Hapus notifikasi lama jika ada
            const oldNotification = document.querySelector('.notification');
            if (oldNotification) {
                oldNotification.remove();
            }

            // Buat notifikasi baru
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Tambahkan notifikasi ke form yang aktif
            const activeForm = document.querySelector('.login-form[style*="display: block"]');
            if (activeForm) {
                activeForm.appendChild(notification);
            }

            // Hilangkan notifikasi setelah 3 detik
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        function handleLogin(event, form) {
            event.preventDefault();
            const formData = new FormData(form);

            fetch('login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Server response:', data);
                if (data.includes('dashboard')) {
                    window.location.href = data.trim();
                } else {
                    showNotification(data);
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                showNotification('Terjadi kesalahan saat login');
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang di Toko Wahyu Listrik</h1>
        <div class="menu-container">
            <button onclick="toggleLoginForm('karyawan')" class="menu-button karyawan">Masuk sebagai Karyawan</button>
            <button onclick="toggleLoginForm('owner')" class="menu-button owner">Masuk sebagai Owner</button>
        </div>

        <div id="loginForm" class="login-form">
            <form onsubmit="handleLogin(event, this)">
                <input type="hidden" name="userType" value="karyawan">
                <div class="form-group">
                    <label for="nama">Nama Karyawan:</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir (DDMMYYYY):</label>
                    <input type="text" id="tanggal_lahir" name="tanggal_lahir" required 
                           pattern="\d{8}" minlength="8" maxlength="8">
                </div>
                <button type="submit" class="submit-btn">Masuk</button>
            </form>
        </div>

        <div id="loginFormOwner" class="login-form" style="display: none;">
            <form onsubmit="handleLogin(event, this)">
                <input type="hidden" name="userType" value="owner">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>