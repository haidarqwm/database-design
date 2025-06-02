<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';

    // Validasi input
    if (empty($jenis) || empty($nama)) {
        die("Error: Jenis barang dan nama harus diisi.");
    }

    // Pilih tabel yang sesuai berdasarkan jenis barang
    if ($jenis == 'listrik') {
        $table = 'datalistrik';
        $id_field = 'idlistrik';
    } elseif ($jenis == 'atk') {
        $table = 'dataatk';
        $id_field = 'idatk';
    } else {
        die("Error: Jenis barang tidak valid.");
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Dapatkan ID barang berdasarkan nama
        $stmt = $conn->prepare("SELECT $id_field FROM $table WHERE nama = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Barang tidak ditemukan.");
        }

        $row = $result->fetch_assoc();
        $id_barang = $row[$id_field];

        // Hapus dari transaksi_detail terlebih dahulu
        $stmt = $conn->prepare("DELETE FROM transaksi_detail WHERE idbarang = ?");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();

        // Hapus transaksi terkait jika tidak ada detail transaksi yang tersisa
        $stmt = $conn->prepare("DELETE FROM transaksi WHERE NOT EXISTS (SELECT * FROM transaksi_detail WHERE idtransaksi = transaksi.idtransaksi)");
        $stmt->execute();

        // Hapus barang
        $stmt = $conn->prepare("DELETE FROM $table WHERE $id_field = ?");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Barang dan transaksi terkait berhasil dihapus.<br>";
        } else {
            echo "Tidak ada barang yang dihapus.<br>";
        }

        // Commit transaksi
        $conn->commit();
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        echo "Error: " . $e->getMessage() . "<br>";
    } finally {
        // Tutup statement
        $stmt->close();
    }
} else {
    echo "Request method salah.";
}

$conn->close();

// Tambahkan tombol untuk kembali ke halaman view.php dengan jenis barang yang sesuai
if ($jenis == 'listrik' || $jenis == 'atk') {
    echo '<br><a href="view.php?jenis=' . htmlspecialchars($jenis) . '"><button>Kembali ke Stok Barang ' . ucfirst($jenis) . '</button></a>';
}
?>