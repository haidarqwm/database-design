<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Wahyu Listrik - Halaman Utama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .menu-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        .menu-button {
            padding: 15px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: white;
        }

        .input-karyawan {
            background-color: #4CAF50;
        }

        .input-karyawan:hover {
            background-color: #45a049;
        }

        .login-karyawan {
            background-color: #2196F3;
        }

        .login-karyawan:hover {
            background-color: #1976D2;
        }

        .dashboard {
            background-color: #FF9800;
        }

        .dashboard:hover {
            background-color: #F57C00;
        }

        /* Styling untuk form login */
        .login-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-btn {
            background-color: #2196F3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang di Toko Wahyu Listrik</h1>
        
        <div class="menu-container">
            <a href="input_karyawan.php" class="menu-button input-karyawan">Input Karyawan Baru</a>
            
            <button onclick="toggleLoginForm()" class="menu-button login-karyawan">Masuk sebagai Karyawan</button>
            
            <!-- Form Login Karyawan -->
            <div id="loginForm" class="login-form">
                <form action="login_process.php" method="post">
                    <div class="form-group">
                        <label for="idkaryawan">ID Karyawan:</label>
                        <input type="text" id="idkaryawan" name="idkaryawan" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <button type="submit" class="submit-btn">Masuk</button>
                </form>
            </div>

            <a href="abcde.html" class="menu-button dashboard">Dashboard</a>
        </div>
    </div>

    <script>
        function toggleLoginForm() {
            var form = document.getElementById("loginForm");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
    </script>
</body>
</html>