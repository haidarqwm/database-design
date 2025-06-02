<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = $_POST['jenis'];
    $idlistrik = $_POST['idbarang']; // Ganti idbarang menjadi idlistrik
    $jumlah_baru = $_POST['jumlah_baru']; // Jumlah stok baru yang dimasukkan

    // Tentukan tabel berdasarkan jenis barang
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';

    // Update stok barang
    $sql = "UPDATE $table SET jumlah = ? WHERE idlistrik = ?"; // Ganti idbarang menjadi idlistrik
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $jumlah_baru, $idlistrik); // Menggunakan parameter untuk mencegah SQL injection

    if ($stmt->execute()) {
        echo "Stok barang berhasil diperbarui.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: View.php?jenis=$jenis"); // Kembali ke halaman daftar barang
}
?>