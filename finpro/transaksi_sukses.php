<?php
session_start();
include 'config.php';

if (!isset($_GET['idtransaksi'])) {
    header("Location: error_page.php");
    exit();
}

$idtransaksi = $_GET['idtransaksi'];

// Ambil data transaksi dari database
$query = "SELECT t.*, k.nama_karyawan 
          FROM transaksi t 
          LEFT JOIN karyawan k ON t.idkaryawan = k.idkaryawan 
          WHERE t.idtransaksi = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $idtransaksi);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

// Debug: Tampilkan nilai yang didapat
echo "<!-- Debug Info:";
echo "ID Karyawan: " . ($transaksi['idkaryawan'] ?? 'kosong') . "<br>";
echo "Nama Karyawan: " . ($transaksi['nama_karyawan'] ?? 'kosong') . "<br>";
echo "-->";

// Ambil detail transaksi
$query_detail = "SELECT * FROM transaksi_detail WHERE idtransaksi = ?";
$stmt_detail = $conn->prepare($query_detail);
$stmt_detail->bind_param("s", $idtransaksi);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$details = $result_detail->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil - <?php echo htmlspecialchars($idtransaksi); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .transaction-details {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaksi Berhasil</h1>
        <div class="success-message">
            Transaksi dengan ID <?php echo htmlspecialchars($idtransaksi); ?> telah berhasil diproses.
        </div>
        <div class="transaction-details">
            <h2>Detail Transaksi</h2>
            <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($transaksi['tanggal_transaksi']); ?></p>
           
            <p><strong>Kasir:</strong> 
            <?php 
            // Debug information
            echo "<!-- Debug Info:";
            echo "ID Karyawan from DB: " . ($transaksi['idkaryawan'] ?? 'kosong') . "<br>";
            echo "Nama Karyawan from DB: " . ($transaksi['nama_karyawan'] ?? 'kosong') . "<br>";
            echo "Session ID Karyawan: " . ($_SESSION['idkaryawan'] ?? 'kosong') . "<br>";
            echo "Session Nama Karyawan: " . ($_SESSION['nama_karyawan'] ?? 'kosong') . "<br>";
            echo "-->";

            // Tampilkan nama karyawan jika ada, jika tidak tampilkan "owner"
            if (!empty($transaksi['idkaryawan']) && !empty($transaksi['nama_karyawan'])) {
                echo htmlspecialchars($transaksi['nama_karyawan']);
            } else if (!empty($_SESSION['idkaryawan']) && !empty($_SESSION['nama_karyawan'])) {
                echo htmlspecialchars($_SESSION['nama_karyawan']);
            } else {
                echo "Owner"; // Jika tidak ada karyawan yang menangani, tampilkan "owner"
            }
            ?>
            </p>
            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($transaksi['metode_pembayaran'] ?? '-'); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Item </th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $detail): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($detail['jumlah']); ?></td>
                        <td>Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="total">Total:</td>
                        <td>Rp <?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="button-container">
            <a href="pembelian.php" class="button">Kembali ke Pembelian</a>
            <a href="cetak_nota.php?idtransaksi=<?php echo urlencode($idtransaksi); ?>" target="_blank" class="button">Cetak Nota</a>
        </div>
    </div>
</body>
</html>