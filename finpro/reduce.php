<?php
include 'config.php'; // Include koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];

    // Pilih tabel yang sesuai berdasarkan jenis barang
    if ($jenis == 'listrik') {
        $table = 'datalistrik';
    } elseif ($jenis == 'atk') {
        $table = 'dataatk';
    } else {
        echo "Jenis barang tidak valid.";
        exit;
    }

    // Query untuk mendapatkan stok saat ini
    $sql = "SELECT jumlah FROM $table WHERE nama = '$nama'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stokSekarang = $row['jumlah'];

        // Periksa apakah jumlah yang akan dikurangi valid
        if ($stokSekarang >= $jumlah) {
            // Update stok barang
            $stokBaru = $stokSekarang - $jumlah;
            $updateSql = "UPDATE $table SET jumlah = $stokBaru WHERE nama = '$nama'";
            if ($conn->query($updateSql) === TRUE) {
                echo "Stok barang berhasil dikurangi.<br>";
            } else {
                echo "Error: " . $updateSql . "<br>" . $conn->error;
            }
        } else {
            echo "Jumlah yang diminta melebihi stok yang ada.";
        }
    } else {
        echo "Barang tidak ditemukan.";
    }

    $conn->close();
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
