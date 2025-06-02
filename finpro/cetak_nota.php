<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';

if (!isset($_GET['idtransaksi'])) {
    die("ID Transaksi tidak ditemukan");
}

$idtransaksi = $_GET['idtransaksi'];

// Ambil data transaksi
$query = "SELECT * FROM transaksi WHERE idtransaksi = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $idtransaksi);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

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
    <title>Cetak Nota - <?php echo htmlspecialchars($idtransaksi); ?></title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .nota-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .nota-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .nota-header h1 {
            color: #333;
            margin: 0;
        }
        .transaction-info {
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .transaction-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .print-button {
            text-align: center;
            margin-top: 20px;
        }
        .print-button button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .print-button button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="nota-container">
        <div class="nota-header">
            <h1>TOKO WAHYU LISTRIK</h1>
            <p>Bulukerto, Bumiaji, Batu City, East Java 65334</p>
            <p>Telp: (021) 123-4567</p>
        </div>

        <div class="transaction-info">
            <p><strong>No. Transaksi:</strong> <?php echo htmlspecialchars($transaksi['idtransaksi']); ?></p>
            <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($transaksi['tanggal_transaksi']); ?></p>

            <p><strong>Kasir:</strong> 
                <?php 
                // Tampilkan nama karyawan jika ada, jika tidak tampilkan nama owner
                if (!empty($transaksi['nama_karyawan'])) {
                    echo htmlspecialchars($transaksi['nama_karyawan']);
                } elseif (!empty($transaksi['namaowner'])) { // Pastikan untuk memeriksa kunci ini
                    echo htmlspecialchars($transaksi['namaowner']);
                } else {
                    echo "hubungi owner";
                }
                ?>
            </p>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
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
        </table>
            <h3>Total: Rp <?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></h3>
        </div>

        <div class="print-button no-print">
            <button onclick="window.print()">Cetak Nota</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>