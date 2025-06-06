<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'];
    $idlistrik = $_POST['idlistrik'];
    $jumlah = $_POST['jumlah'];
    
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';
    $id_field = ($jenis == 'listrik') ? 'idlistrik' : 'idatk';
    
    $stmt = $conn->prepare("UPDATE $table SET jumlah = jumlah + ? WHERE $id_field = ?");
    $stmt->bind_param("is", $jumlah, $idlistrik);
    
    if ($stmt->execute()) {
        header("Location: view.php?jenis=$jenis&status=add_success");
    } else {
        header("Location: view.php?jenis=$jenis&status=add_error");
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