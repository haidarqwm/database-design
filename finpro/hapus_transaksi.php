<?php
include 'config.php';

if (isset($_GET['idtransaksi'])) {
    $idtransaksi = $_GET['idtransaksi'];
    
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Hapus detail transaksi terlebih dahulu
        $query_detail = "DELETE FROM transaksi_detail WHERE idtransaksi = ?";
        $stmt_detail = $conn->prepare($query_detail);
        $stmt_detail->bind_param("s", $idtransaksi);
        $stmt_detail->execute();

        // Kemudian hapus transaksi utama
        $query_transaksi = "DELETE FROM transaksi WHERE idtransaksi = ?";
        $stmt_transaksi = $conn->prepare($query_transaksi);
        $stmt_transaksi->bind_param("s", $idtransaksi);
        $stmt_transaksi->execute();

        // Commit transaksi
        $conn->commit();

        // Redirect kembali ke halaman riwayat transaksi
        header("Location: riwayat_transaksi.php?status=success&message=Transaksi berhasil dihapus");
        exit();

    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        header("Location: riwayat_transaksi.php?status=error&message=Gagal menghapus transaksi");
        exit();
    }
} else {
    header("Location: riwayat_transaksi.php?status=error&message=ID Transaksi tidak ditemukan");
    exit();
}

$conn->close();
?>