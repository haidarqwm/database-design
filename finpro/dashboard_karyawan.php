<?php
session_start();

// Cek apakah user sudah login sebagai karyawan
if (!isset($_SESSION['login_as_karyawan']) || $_SESSION['login_as_karyawan'] !== true) {
    header("Location: index.php"); // Arahkan ke halaman utama jika tidak login
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - Toko Wahyu Listrik</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            overflow: hidden;
        }
        .container {
            background-color: rgba(81, 88, 94, 0.5); /* Mengubah alpha menjadi 0.5 untuk 50% transparansi */
            backdrop-filter: blur(10px); /* Meningkatkan blur untuk kompensasi transparansi */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            width: 100%;
            max-width: 350px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: white;
        }
        .menu {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        .menu-button {
            padding: 15px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .menu-button:hover {
            background-color: #2980b9;
        }
        .inputbarang{ background-color: rgba(226, 158, 32, 0.9); }
        .inputbarang:hover { background-color: rgba(222, 200, 125, 0.9); }
        .beli { background-color: rgba(226, 158, 32, 0.9); }
        .beli:hover { background-color: rgba(222, 200, 125, 0.9); }
        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Karyawan</h1>
        <div class="menu">
            <a href="pembelian.php" class="menu-button beli">Lakukan Pembelian</a> <!-- Menu untuk melakukan pembelian -->
        </div>
        <a href="index.php" class="back-button">Kembali ke Menu Utama</a>
    </div>
</body>
</html>