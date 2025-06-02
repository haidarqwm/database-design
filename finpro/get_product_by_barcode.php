<?php
session_start();
include 'config.php'; // Pastikan Anda menyertakan file konfigurasi untuk koneksi database

// Ambil barcode dari parameter GET
$barcode = $_GET['barcode'] ?? '';

// Validasi input
if (empty($barcode)) {
    echo json_encode(['success' => false, 'message' => 'Barcode tidak boleh kosong.']);
    exit;
}

// Cek koneksi database
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $conn->connect_error]);
    exit;
}

// Cari produk di tabel datalistrik
$sqlListrik = "SELECT idlistrik AS id, nama, harga, 'listrik' AS jenis FROM datalistrik WHERE barcode = ?";
$stmtListrik = $conn->prepare($sqlListrik);
$stmtListrik->bind_param("s", $barcode);
$stmtListrik->execute();
$resultListrik = $stmtListrik->get_result();

if ($resultListrik->num_rows > 0) {
    $product = $resultListrik->fetch_assoc();
    echo json_encode(['success' => true, 'product' => $product]);
    exit;
}

// Cari produk di tabel dataatk
$sqlAtk = "SELECT idatk AS id, nama, harga, 'atk' AS jenis FROM dataatk WHERE barcode = ?";
$stmtAtk = $conn->prepare($sqlAtk);
$stmtAtk->bind_param("s", $barcode);
$stmtAtk->execute();
$resultAtk = $stmtAtk->get_result();

if ($resultAtk->num_rows > 0) {
    $product = $resultAtk->fetch_assoc();
    echo json_encode(['success' => true, 'product' => $product]);
    exit;
}

// Jika tidak ditemukan
echo json_encode(['success' => false, 'message' => 'Barcode tidak ditemukan.']);
exit;
?>