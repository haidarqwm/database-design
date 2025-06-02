<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses checkout
    $idcustomer = $_SESSION['idcustomer'];
    $namacustomer = $_SESSION['namacustomer'];
    $idkaryawan = $_SESSION['idkaryawan'] ?? null;
    $nama_karyawan = $_SESSION['nama_karyawan'] ?? null;
    $total_harga = 0;

    foreach ($_SESSION['keranjang'] as $item) {
        $total_harga += $item['jumlah'] * $item['harga'];
    }

    // Insert ke tabel order
    $query = "INSERT INTO `order` (idcustomer, namacustomer, idkaryawan, nama_karyawan, total_harga) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssd", $idcustomer, $namacustomer, $idkaryawan, $nama_karyawan, $total_harga);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit;
    }
    
    $id_order = $stmt->insert_id;

    // Insert ke tabel transaksi_detail
    $queryDetail = "INSERT INTO transaksi_detail (idtransaksi, idjenis, idbarang, nama_barang, jumlah, harga_satuan, subtotal) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($queryDetail);

    foreach ($_SESSION['keranjang'] as $item) {
        $subtotal = $item['jumlah'] * $item['harga'];
        $stmtDetail->bind_param("isssidd", $id_order, $item['jenis'], $item['id'], $item['nama'], $item['jumlah'], $item['harga'], $subtotal);
        
        if (!$stmtDetail->execute()) {
            echo "Error: " . $stmtDetail->error;
            exit;
        }

        // Update stok
        $update_query = "UPDATE " . ($item['jenis'] == 'J001' ? 'datalistrik' : 'dataatk') . 
                        " SET jumlah = jumlah - ? WHERE " . 
                        ($item['jenis'] == 'J001' ? 'idlistrik' : 'idatk') . " = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("is", $item['jumlah'], $item['id']);
        
        if (!$update_stmt->execute()) {
            echo "Error updating stock: " . $update_stmt->error;
            exit;
        }
    }

    // Hapus keranjang
    unset($_SESSION['keranjang']);
    header('Location: cetak_nota.php?id_order=' . $id_order);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <form action="" method="post">
            <button type="submit">Checkout</button>
        </form>
    </div>
</body>
</html>