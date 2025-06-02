<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root"; // Username default MySQL di XAMPP adalah 'root'
$password = ""; // Password default biasanya kosong
$dbname = "wahyulistrik"; // Pastikan nama database sudah benar

// Membuat koneksi ke database yang sudah dipilih
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>