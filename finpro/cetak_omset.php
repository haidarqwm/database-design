<?php
// Koneksi database
include 'config.php';

// Ambil filter dari GET
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$jenis = $_GET['jenis'] ?? '';

// Membuat query dinamis untuk mengambil data
$query = "SELECT t.idtransaksi, t.tanggal_transaksi, td.nama_barang, td.jumlah, td.subtotal
          FROM transaksi t
          JOIN transaksi_detail td ON t.idtransaksi = td.idtransaksi
          WHERE 1=1";

// Tambahkan filter untuk tanggal jika ada
if ($start_date) {
    $query .= " AND t.tanggal_transaksi >= '$start_date'";
}

if ($end_date) {
    $query .= " AND t.tanggal_transaksi <= '$end_date'";
}

// Tambahkan filter untuk jenis barang jika ada
if ($jenis) {
    $query .= " AND td.jenis_barang = '$jenis'";
}

$query .= " ORDER BY t.idtransaksi";

// Eksekusi query
$result = $conn->query($query);
$data = [];
$total_item_all = 0;
$total_harga_all = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id_transaksi = $row['idtransaksi'];
        
        // Jika ID transaksi belum ada dalam data, inisialisasi
        if (!isset($data[$id_transaksi])) {
            $data[$id_transaksi] = [
                'tanggal' => $row['tanggal_transaksi'],
                'items' => [],
                'total_harga' => 0
            ];
        }

        // Tambahkan item ke transaksi
        $data[$id_transaksi]['items'][] = [
            'nama_barang' => $row['nama_barang'],
            'jumlah' => $row['jumlah'],
            'subtotal' => $row['subtotal']
        ];

        // Tambahkan subtotal ke total harga
        $data[$id_transaksi]['total_harga'] += $row['subtotal'];
        $total_item_all += $row['jumlah'];
        $total_harga_all += $row['subtotal'];
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Omset</title>
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
    <div class="nota-container ">
        <div class="nota-header">
            <h1>LAPORAN OMSET TOKO WAHYU LISTRIK</h1>
            <p>Bulukerto, Bumiaji, Batu City, East Java 65334</p>
            <p>Telp: (021) 123-4567</p>
        </div>

        <div class="transaction-info">
            <p><strong>Periode:</strong> <?php echo htmlspecialchars($start_date) . ' s/d ' . htmlspecialchars($end_date); ?></p>
            <p><strong>Total Item:</strong> <?php echo $total_item_all; ?></p>
            <p><strong>Total Omset:</strong> Rp <?php echo number_format($total_harga_all, 0, ',', '.'); ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $id_transaksi => $transaksi): ?>
                    <tr>
                        <td rowspan="<?php echo count($transaksi['items']); ?>"><?php echo htmlspecialchars($id_transaksi); ?></td>
                        <td rowspan="<?php echo count($transaksi['items']); ?>"><?php echo htmlspecialchars($transaksi['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($transaksi['items'][0]['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($transaksi['items'][0]['jumlah']); ?></td>
                        <td>Rp <?php echo number_format($transaksi['items'][0]['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php for ($i = 1; $i < count($transaksi['items']); $i++): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaksi['items'][$i]['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($transaksi['items'][$i]['jumlah']); ?></td>
                        <td>Rp <?php echo number_format($transaksi['items'][$i]['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="total-section">Total Omset Keseluruhan: Rp <?php echo number_format($total_harga_all, 0, ',', '.'); ?></h3>

        <div class="print-button no-print">
            <button onclick="window.print()">Cetak Laporan</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>