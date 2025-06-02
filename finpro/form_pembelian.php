<?php
session_start();

// Cek apakah karyawan atau owner sudah login
// if (!isset($_SESSION['login_as_karyawan']) || $_SESSION['login_as_karyawan'] !== true) {
    if (!isset($_SESSION['login_as_owner']) || $_SESSION['login_as_owner'] !== true) {
        echo "<script>
                alert('Anda harus masuk sebagai karyawan atau owner untuk melakukan pembelian.');
                window.location.href='index.php';
              </script>";
        exit();
    }


// Jika sudah login sebagai karyawan atau owner, redirect ke process_customer.php
header("Location: process_customer.php");
exit();
?>