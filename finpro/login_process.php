<?php
session_start();
include 'config.php'; // Pastikan ini mengarah ke file koneksi database

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
            if ($password === $row['password']) { // Pastikan ini sesuai dengan cara Anda menyimpan password
                $_SESSION['idowner'] = $row['idowner'];
                $_SESSION['nama_owner'] = $row['nama'];
                $_SESSION['login_as_owner'] = true; // Set session untuk owner

                header("Location: dashboard.php"); // Arahkan ke dashboard owner
                exit();
            } else {
                echo "Login gagal! Username atau Password tidak cocok.";
            }
        } else {
            echo "Login gagal! Username atau Password tidak cocok.";
        }
    } elseif ($userType === 'karyawan') {
        // Ambil data dari form untuk karyawan
        $idkaryawan = $_POST['idkaryawan'] ?? null;
        $nama_karyawan = $_POST['nama'] ?? null;

        // Validasi input
        if (empty($idkaryawan) || empty($nama_karyawan)) {
            echo "ID Karyawan dan Nama harus diisi!";
            exit;
        }

        // Query untuk memeriksa ID Karyawan dan Nama
        $query = "SELECT * FROM karyawan WHERE idkaryawan = ? AND nama_karyawan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $idkaryawan, $nama_karyawan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Set session untuk karyawan
            $_SESSION['idkaryawan'] = $row['idkaryawan'];
            $_SESSION['nama_karyawan'] = $row['nama_karyawan'];
            $_SESSION['login_as_karyawan'] = true; // Set session untuk karyawan

            header("Location: dashboard_karyawan.php"); // Arahkan ke dashboard karyawan
            exit();
        } else {
            echo "Login gagal! ID Karyawan atau Nama tidak cocok.";
        }
    }
} else {
    echo "Metode request tidak valid!";
}
?>