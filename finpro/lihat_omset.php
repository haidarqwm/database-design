<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Omset</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-color: #333; /* Warna gelap untuk latar belakang */
            color: #ffffff; /* Warna teks putih untuk kontras */
            overflow: hidden;
        }

        .container {
            max-width: 1200px;
            margin-top: 0px;
            padding: 20px;
            background-color: rgba(81, 88, 94, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            color: white;
            margin-bottom: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .filter-form {
            margin-bottom: 0px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-end;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 5px;
            width: 100%;
            margin-top: 5px;
        }

        .filter-row label {
            margin-right: 5px;
        }

        .filter-row input[type="date"] {
            margin-right: 15px;
        }

        .filter-form input[type="date"],
        .filter-form select {
            padding: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filter-form button {
            padding: 8px 15px;
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filter-form button:hover {
            background-color: rgba(222, 200, 125, 0.9);
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .transaction-table th,
        .transaction-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            color: #ffffff;
        }

        .transaction-table th {
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
        }

        .transaction-table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .transaction-table tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .print-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #45a049;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            color: white;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.9);
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.9);
        }

        .total-omset {
            font-size: 18px;
            color: #ffffff;
            background-color: rgba(226, 158, 32, 0.9);
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        /* CSS untuk scrollable table */
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }

    </style>

</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0px;">
            <div style="flex:1;">
                <?php
                // --- LOGIKA OMSET & PROFIT ---
                include 'config.php';
                $where = "WHERE 1=1";
                if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                    $start_date = $_GET['start_date'];
                    $where .= " AND t.tanggal_transaksi >= '$start_date'";
                }
                if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                    $end_date = $_GET['end_date'];
                    $where .= " AND t.tanggal_transaksi <= '$end_date'";
                }
                // Omset
                $qOmset = "SELECT SUM(t.total_harga) as omset
                            FROM transaksi t $where";
                $rOmset = $conn->query($qOmset);
                $omset = 0;
                if ($rOmset && $rowOmset = $rOmset->fetch_assoc()) {
                    $omset = $rowOmset['omset'] ?? 0;
                }
                // Profit
                $qProfit = "SELECT td.idjenis, td.idbarang, td.jumlah, td.harga_satuan, td.subtotal,
                                CASE WHEN td.idjenis='J001' THEN dl.harga_beli ELSE da.harga_beli END as harga_beli
                            FROM transaksi t
                            JOIN transaksi_detail td ON t.idtransaksi = td.idtransaksi
                            LEFT JOIN datalistrik dl ON td.idjenis='J001' AND td.idbarang=dl.idlistrik
                            LEFT JOIN dataatk da ON td.idjenis='J002' AND td.idbarang=da.idatk
                            $where";
                $rProfit = $conn->query($qProfit);
                $total_beli = 0;
                if ($rProfit) {
                    while ($row = $rProfit->fetch_assoc()) {
                        $total_beli += ($row['harga_beli'] ?? 0) * $row['jumlah'];
                    }
                }
                $profit = $omset - $total_beli;
                ?>
                <div style="background-color: rgba(81, 88, 94, 0.5); backdrop-filter: blur(10px); padding: 15px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); margin-top: 10px; max-width: 400px;">
                    <div style="font-size:2rem; font-weight:bold; margin-bottom:10px;">Omset: Rp <?php echo number_format($omset,0,',','.'); ?></div>
                    <div style="font-size:2rem; font-weight:bold; color:#4CAF50;">Profit: Rp <?php echo number_format($profit,0,',','.'); ?></div>
                </div>
            </div>
            <div style="flex:1 text-align:right; max-width:900px; margin-left: 20px; margin-top: 10px;">
                <!-- Form Filter -->
                <form class="filter-form" method="GET" style="display:inline-block; min-width:250px;">
                    <div class="filter-row">
                        <label for="start_date">Dari Tanggal    :</label>
                        <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                        <label for="end_date">Sampai Tanggal    :</label>
                        <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    </div>
                    <div class="filter-row" style="justify-content: flex-start; gap:10px;">
                        <button type="submit">Filter</button>
                        <button type="button" onclick="window.location.href='lihat_omset.php'">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $message = $_GET['message'] ?? '';
            $alertClass = ($status === 'success') ? 'alert-success' : 'alert-error';
            
            echo "<div class='alert {$alertClass}'>{$message}</div>";
        }
        ?>
        
        <div class="table-container">
            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            include 'config.php';

            // Build query with filters
            $query = "SELECT t.idtransaksi, t.tanggal_transaksi, t.total_harga,
                    GROUP_CONCAT(td.nama_barang, ' (', td.jumlah, ')' SEPARATOR ', ') as items
                    FROM transaksi t 
                    LEFT JOIN transaksi_detail td ON t.idtransaksi = td.idtransaksi 
                    WHERE 1=1";

            // Tambahkan filter
            if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                $start_date = $_GET['start_date'];
                $query .= " AND t.tanggal_transaksi >= '$start_date'";
            }

            if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                $end_date = $_GET['end_date'];
                $query .= " AND t.tanggal_transaksi <= '$end_date'";
            }

            $query .= " GROUP BY t.idtransaksi ORDER BY t.tanggal_transaksi DESC, t.idtransaksi DESC";

            $result = mysqli_query($conn, $query);

            // Cek kesalahan query
            if (!$result) {
                die('Query Error: ' . mysqli_error($conn));
            }

            // Calculate total omset
            $totalOmset = 0;

            if ($result && mysqli_num_rows($result) > 0) {
                echo "<table class='transaction-table'>";
                echo "<tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total Harga</th>
                      </tr>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['idtransaksi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_transaksi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['items']) . "</td>";
                    echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                    echo "</tr>";

                    $totalOmset += $row['total_harga'];  // Add to total omset
                }

                echo "</table>";
            } else {
                echo "<p>Tidak ada omset yang ditemukan.</p>";
            }
            ?>
        </div>

        <div class="total-omset">
            <strong>Total Omset: </strong>Rp <?php echo number_format($totalOmset, 0, ',', '.'); ?>
        </div>

        <div>
            <a href="cetak_omset.php?start_date=<?php echo $_GET['start_date'] ?? ''; ?>&end_date=<?php echo $_GET['end_date'] ?? ''; ?>" class="print-button" target="_blank">Cetak Laporan Omset</a>
        </div>

        <a href="riwayat_transaksi.php" class="back-button">Kembali ke riwayat transaksi</a>
    </div>
</body>
</html>