<?php
session_start();
include 'config.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek apakah data dikirim melalui POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST['userType'] ?? null;

    if ($userType === 'owner') {
        // Ambil data dari form
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        // Validasi input
        if (empty($username) || empty($password)) {
            echo "Username dan Password harus diisi!";
            exit;
        }

        // Query untuk memeriksa username dan password
        $query = "SELECT * FROM owner WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verifikasi password
            if ($password === $row['password']) {
                $_SESSION['idowner'] = $row['idowner'];
                $_SESSION['nama_owner'] = $row['nama_owner'] ?? $row['username'];
                $_SESSION['login_as_owner'] = true;

                echo "dashboard.php";
                exit();
            } else {
                echo "Login gagal! Username atau Password tidak cocok.";
            }
        } else {
            echo "Login gagal! Username atau Password tidak cocok.";
        }
    } elseif ($userType === 'karyawan') {
        // Ambil data dari form untuk karyawan
        $nama_karyawan = $_POST['nama'] ?? null;
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;

        // Validasi input
        if (empty($nama_karyawan) || empty($tanggal_lahir)) {
            echo "Nama Karyawan dan Tanggal Lahir harus diisi!";
            exit;
        }

        // Validasi format tanggal lahir (DDMMYYYY)
        if (!preg_match('/^\d{8}$/', $tanggal_lahir)) {
            echo "Format Tanggal Lahir harus DDMMYYYY (contoh: 01012023 untuk 01 Januari 2023)!";
            exit;
        }

        // Konversi format tanggal lahir dari DDMMYYYY ke YYYY-MM-DD untuk query
        $day = substr($tanggal_lahir, 0, 2);
        $month = substr($tanggal_lahir, 2, 2);
        $year = substr($tanggal_lahir, 4, 4);
        $formatted_date = $year . '-' . $month . '-' . $day;

        // Query untuk memeriksa Nama Karyawan dan Tanggal Lahir
        $query = "SELECT * FROM karyawan WHERE nama_karyawan = ? AND DATE(tanggal_lahir) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nama_karyawan, $formatted_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Set session untuk karyawan
            $_SESSION['idkaryawan'] = $row['idkaryawan'];
            $_SESSION['nama_karyawan'] = $row['nama_karyawan'];
            $_SESSION['login_as_karyawan'] = true;

            echo "dashboard_karyawan.php";
            exit();
        } else {
            echo "Login gagal! Nama Karyawan atau Tanggal Lahir tidak cocok.";
        }
    }
} else {
    echo "Metode request tidak valid!";
}
?>