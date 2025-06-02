<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "080425";
$dbname = "wahyulistrik";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$jenis = $_POST['jenis'];
$nama = $_POST['nama'];
$harga = $_POST['harga'];
$jumlah = $_POST['jumlah'];

// Pilih tabel berdasarkan jenis barang
$table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';

// Query untuk menambahkan barang
$sql = "INSERT INTO $table (nama, harga, jumlah) VALUES ('$nama', '$harga', '$jumlah')";

if ($conn->query($sql) === TRUE) {
    echo "$nama berhasil ditambahkan!"; // Menampilkan nama barang yang ditambahkan
    echo '<a href="input.php" class="button">Kembali ke Proses Input</a>';
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>