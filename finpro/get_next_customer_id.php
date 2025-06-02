<?php
include 'config.php';

// Cek apakah ada customer di database
$checkQuery = "SELECT COUNT(*) as total FROM customer";
$checkResult = $conn->query($checkQuery);
$checkRow = $checkResult->fetch_assoc();

if ($checkRow['total'] == 0) {
    // Jika belum ada customer, mulai dari 1
    $next_id = 1;
} else {
    // Ambil ID customer terakhir dari database
    $query = "SELECT MAX(CAST(SUBSTRING(idcustomer, 5) AS UNSIGNED)) as last_id 
              FROM customer 
              WHERE idcustomer LIKE 'CST-%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $last_id = $row['last_id'];
    $next_id = $last_id + 1;
}

// Format ID customer dengan CST- dan padding 4 digit
$idcustomer = 'CST-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

// Simpan ID customer ke session
session_start();
$_SESSION['idcustomer'] = $idcustomer;

// Redirect ke halaman pembelian
header("Location: pembelian.php");
exit();
?>