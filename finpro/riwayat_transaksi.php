<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(81, 88, 94, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            color: white;
            margin-bottom: 40px;
        }

        h1 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 0;
        }

        .filter-form {
            margin-bottom: 0px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            justify-content: space-between;
        }

        .filter-form label {
            color: #ffffff;
            margin-right: 5px;
        }

        .filter-form input[type="date"],
        .filter-form select,
        .filter-form input[type="text"] {
            padding: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }

        .filter-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .filter-form button {
            padding: 8px 20px;
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 6px 12px;
            font-size: 14px;
            text-align: center;
            color: white;
            background-color: #009688;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-buttons button:hover {
            background-color: #00796b;
        }

        .action-buttons .delete {
            background-color: #f44336;
        }

        .action-buttons .delete:hover {
            background-color: #c62828;
        }

        .back-button {
            padding: 8px 20px; /* Sesuaikan padding agar sama dengan tombol filter */
            background-color: #007BFF; /* Warna latar belakang sama dengan tombol filter */
            color: white; /* Warna teks */
            border: none; /* Menghilangkan border */
            border-radius: 4px; /* Sudut membulat */
            cursor: pointer; /* Menunjukkan bahwa ini adalah tombol yang dapat diklik */
        }

        .back-button:hover {
            background-color: #0056b3; /* Warna saat hover sama dengan tombol filter */
        }

        .omset-button {
            padding: 8px 20px; /* Sesuaikan padding agar sama dengan tombol filter */
            background-color: rgba(226, 158, 32, 0.9); /* Warna latar belakang sama dengan tombol filter */
            color: white; /* Warna teks */
            border: none; /* Menghilangkan border */
            border-radius: 4px; /* Sudut membulat */
            cursor: pointer; /* Menunjukkan bahwa ini adalah tombol yang dapat diklik */
            text-decoration: none;
        }

        .omset-button:hover {
            background-color: rgba(222, 200, 125, 0.9); /* Warna saat hover sama dengan tombol filter */
        }

        /* CSS untuk scrollable table */
        .table-container {
            max-height: 400px; /* Atur tinggi maksimum sesuai kebutuhan */
            overflow-y: auto; /* Mengizinkan scroll vertikal */
            margin-top: 20px; /* Jarak antara filter dan tabel */
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Riwayat Transaksi</h1>

        <!-- Filter Form -->
        <form class="filter-form" method="GET">
            <div class="filter-row">
                <div>
                    <label for="start_date">Dari Tanggal:</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                </div>
                <div>
                    <label for="end_date">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                </div>
            </div>
            <div class="filter-row">
                <div>
                    <label for="jenis">Jenis Barang:</label>
                    <select name="jenis" id="jenis">
                        <option value="">Semua</option>
                        <option value="J001" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'J001') ? 'selected' : ''; ?>>Listrik</option>
                        <option value="J002" <?php echo (isset($_GET['jenis']) && $_GET['jenis'] == 'J002') ? 'selected' : ''; ?>>ATK</option>
                    </select>
                </div>
                <div>
                    <label for="transaction_id">ID Transaksi:</label>
                    <input type="text" name="transaction_id" id="transaction_id" 
                        value="<?php echo isset($_GET['transaction_id']) ? $_GET['transaction_id'] : ''; ?>" placeholder="Masukkan ID Transaksi">
                </div>
            </div>
            <div class="filter-row" style="justify-content: flex-start;">
                <button type="submit">Filter</button>
                <button type="button" onclick="window.location.href='riwayat_transaksi.php'">Reset</button>
            </div>
        </form>
        <!-- End Filter Form -->

        <div class="table-container">
            <?php
            include 'config.php';

            // Build query with filters
            $query = "SELECT t.idtransaksi, t.tanggal_transaksi, t.total_harga, t.nama_karyawan,
                      GROUP_CONCAT(td.nama_barang, ' (', td.jumlah, ')' SEPARATOR ', ') as items
                      FROM transaksi t 
                      LEFT JOIN transaksi_detail td ON t.idtransaksi = td.idtransaksi 
                      WHERE 1=1";
            
            if (isset($_GET['transaction_id']) && !empty($_GET['transaction_id'])) {
                $transaction_id = $_GET['transaction_id'];
                $query .= " AND t.idtransaksi = '$transaction_id'";
            }
            
            if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                $start_date = $_GET['start_date'];
                $query .= " AND t.tanggal_transaksi >= '$start_date'";
            }
            
            if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                $end_date = $_GET['end_date'];
                $query .= " AND t.tanggal_transaksi <= '$end_date'";
            }
            
            if (isset($_GET['jenis']) && !empty($_GET['jenis'])) {
                $jenis = $_GET['jenis'];
                $query .= " AND td.idjenis = '$jenis'";
            }

            $query .= " GROUP BY t.idtransaksi ORDER BY t.tanggal_transaksi DESC, t.idtransaksi DESC";

            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                echo "<table class='transaction-table'>";
                echo "<tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total Harga</th>
                        <th>Kasir</th>
                        <th>Aksi</th>
                      </tr>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['idtransaksi'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_transaksi'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['items'] ?? '') . "</td>";
                    echo "<td>Rp " . number_format($row['total_harga'] ?? 0, 0, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_karyawan'] ?? 'Owner') . "</td>";
                    echo "<td>
                        <div class='action-buttons'>
                            <button onclick=\"window.open('cetak_nota.php?idtransaksi=" . urlencode($row['idtransaksi'] ?? '') . "', '_blank');\">Lihat Nota</button>
                        </div>
                    </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Tidak ada transaksi yang ditemukan.</p>";
            }
            ?>
        </div>

        <a href="dashboard.php" class="back-button">Kembali ke dashboard</a> 
        <a href="lihat_omset.php" class="omset-button">Lihat Omset & Profit</a>
    </div>
</body>
</html>