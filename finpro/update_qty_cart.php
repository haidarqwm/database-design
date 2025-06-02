<?php
session_start();
if (isset($_POST['index']) && isset($_POST['jumlah'])) {
    $index = (int)$_POST['index'];
    $jumlah = (int)$_POST['jumlah'];
    if (isset($_SESSION['keranjang'][$index]) && $jumlah > 0) {
        $_SESSION['keranjang'][$index]['jumlah'] = $jumlah;
    }
}
header('Location: pembelian.php');
exit(); 