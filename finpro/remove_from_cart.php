<?php
session_start();

if (isset($_POST['index']) && isset($_SESSION['keranjang'])) {
    $index = $_POST['index'];
    if (isset($_SESSION['keranjang'][$index])) {
        unset($_SESSION['keranjang'][$index]);
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // Reindex array
    }
}

header("Location: pembelian.php");
exit();
?>