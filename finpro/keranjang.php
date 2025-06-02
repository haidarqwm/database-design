<?php
session_start();
include 'config.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $idbarang = $_POST['idbarang'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    // Tambahkan item ke keranjang
    $_SESSION['keranjang'][] = array(
        'jenis' => $jenis,
        'id' => $idbarang, // Ganti 'idbarang' menjadi 'id' untuk konsistensi
        'nama' => $nama,
        'jumlah' => $jumlah,
        'harga' => $harga
    );
}

// Tampilkan keranjang
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Keranjang Belanja</h1>
        <table>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
            <?php
            $total = 0;
            foreach ($_SESSION['keranjang'] as $item) {
                $subtotal = $item['jumlah'] * $item['harga'];
                $total += $subtotal;
                echo "<tr>
                        <td>{$item['nama']}</td>
                        <td>{$item['jumlah']}</td>
                        <td>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>
                        <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                      </tr>";
            }
            ?>
            <tr>
                <td colspan="3">Total</td>
                <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
            </tr>
        </table>
        <a href="pembelian.php" class="button">Tambah Barang Lain</a>
        <a href="checkout.php" class="button">Checkout</a>
    </div>
</body>
</html>