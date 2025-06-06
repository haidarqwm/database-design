<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';
    
    $stmt = $conn->prepare("DELETE FROM $table WHERE nama = ?");
    $stmt->bind_param("s", $nama);
    
    if ($stmt->execute()) {
        header("Location: view.php?jenis=$jenis&status=delete_success");
    } else {
        header("Location: view.php?jenis=$jenis&status=delete_error");
    }
    exit();
} else {
    echo "Request method salah.";
}

$conn->close();

// Tambahkan tombol untuk kembali ke halaman view.php dengan jenis barang yang sesuai
if ($jenis == 'listrik' || $jenis == 'atk') {
    echo '<br><a href="view.php?jenis=' . htmlspecialchars($jenis) . '"><button>Kembali ke Stok Barang ' . ucfirst($jenis) . '</button></a>';
}
?>