<?php
include 'config.php';

// Aktifkan pelaporan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan data dikirim melalui POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $idkaryawan = isset($_POST['idkaryawan']) ? $_POST['idkaryawan'] : '';
    $nama_karyawan = isset($_POST['nama_karyawan']) ? $_POST['nama_karyawan'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';

    // Validasi data
    if (empty($idkaryawan) || empty($nama_karyawan) || empty($gender)) {
        echo "Error: Semua field harus diisi!";
        exit;
    }

    // Validasi gender
    if ($gender !== 'L' && $gender !== 'P') {
        echo "Error: Gender tidak valid!";
        exit;
    }

    // Gunakan prepared statement untuk menghindari SQL injection
    $query = "INSERT INTO karyawan (idkaryawan, nama_karyawan, gender) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $idkaryawan, $nama_karyawan, $gender);
        
        if (mysqli_stmt_execute($stmt)) {
            // Menyimpan data karyawan ke dalam session setelah berhasil ditambahkan
            session_start(); // Pastikan session dimulai
            $_SESSION['id_karyawan'] = $idkaryawan; // Simpan ID Karyawan
            $_SESSION['nama_karyawan'] = $nama_karyawan; // Simpan Nama Karyawan

            echo "Karyawan berhasil ditambahkan!";
        } else {
            echo "Error saat mengeksekusi statement: " . mysqli_stmt_error($stmt);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Error dalam menyiapkan statement: " . mysqli_error($conn);
    }
} else {
    echo "Error: Method tidak valid!";
}

mysqli_close($conn);
?>