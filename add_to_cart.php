<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $jumlah = (int)$_POST['jumlah'];
    $barcode = $_POST['barcode'] ?? null;

    // Cek stok dan harga dari database
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';
    if ($barcode) {
        $query = "SELECT * FROM $table WHERE barcode = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $barcode);
    } else {
        $query = "SELECT * FROM $table WHERE nama = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nama);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Cek apakah barang dengan barcode sama sudah ada di keranjang
        $found = false;
        if (isset($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $key => $item) {
                if (isset($item['barcode']) && $item['barcode'] === $barcode) {
                    $_SESSION['keranjang'][$key]['jumlah'] += $jumlah;
                    $found = true;
                    break;
                }
            }
        }
        // Jika belum ada, tambahkan ke keranjang
        if (!$found) {
            $_SESSION['keranjang'][] = [
                'id' => $jenis == 'listrik' ? $row['idlistrik'] : $row['idatk'],
                'jenis' => $jenis,
                'nama' => $nama,
                'jumlah' => $jumlah,
                'harga' => $row['harga'],
                'stok_tersedia' => $row['jumlah'],
                'barcode' => $barcode
            ];
        }
        header("Location: pembelian.php");
        exit();
    } else {
        echo "<script>
                alert('Barang tidak ditemukan');
                window.location.href='pembelian.php';
              </script>";
    }
}
?>