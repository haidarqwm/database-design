<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';
    
    // Cek stok saat ini
    $stmt = $conn->prepare("SELECT jumlah FROM $table WHERE nama = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['jumlah'] >= $jumlah) {
        // Update stok
        $stmt = $conn->prepare("UPDATE $table SET jumlah = jumlah - ? WHERE nama = ?");
        $stmt->bind_param("is", $jumlah, $nama);
        
        if ($stmt->execute()) {
            header("Location: view.php?jenis=$jenis&status=reduce_success");
        } else {
            header("Location: view.php?jenis=$jenis&status=reduce_error");
        }
    } else {
        header("Location: view.php?jenis=$jenis&status=reduce_error");
    }
    exit();
} else {
    echo "Request method salah.";
}

// Tambahkan tombol untuk kembali ke halaman view.php dengan jenis barang yang sesuai
if ($jenis == 'listrik') {
    echo '<br><a href="view.php?jenis=listrik"><button>Kembali ke Stok Barang Listrik</button></a>';
} elseif ($jenis == 'atk') {
    echo '<br><a href="view.php?jenis=atk"><button>Kembali ke Stok Barang ATK</button></a>';
}
?>
