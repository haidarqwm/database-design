<?php
session_start();
if (isset($_SESSION['error_message'])) {
    echo "<p>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
}
?>