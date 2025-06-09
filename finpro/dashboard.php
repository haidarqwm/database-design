<?php
session_start();
// Cek apakah user sudah login
$login_as_karyawan = $_SESSION['login_as_karyawan'] ?? false;
$login_as_owner = $_SESSION['login_as_owner'] ?? false;

// Jika tidak login sebagai karyawan atau owner, arahkan ke halaman utama
if (!$login_as_karyawan && !$login_as_owner) {
    header("Location: index.php");
    exit;
}

// Koneksi ke database
include 'config.php';

// Ambil data penjualan dari tabel transaksi
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // Default to the first day of the current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Default to today

// Query untuk total penjualan
$query_total = "SELECT SUM(total_harga) as total_penjualan, 
                       SUM(jumlah) as total_produk_terjual 
                FROM transaksi
                WHERE tanggal_transaksi BETWEEN ? AND ?";
$stmt_total = $conn->prepare($query_total);
$stmt_total->bind_param("ss", $start_date, $end_date);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();

$total_penjualan = (float)$row_total['total_penjualan'];
$total_produk_terjual = (int)$row_total['total_produk_terjual'];

$stmt_total->close();

// Query untuk mendapatkan data penjualan per hari dalam bulan ini
$query_chart = "SELECT DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total 
                FROM transaksi 
                WHERE tanggal_transaksi BETWEEN ? AND ? 
                GROUP BY DATE(tanggal_transaksi) 
                ORDER BY tanggal";
$stmt_chart = $conn->prepare($query_chart);
$stmt_chart->bind_param("ss", $start_date, $end_date);
$stmt_chart->execute();
$result_chart = $stmt_chart->get_result();

$data = [];
while ($row = $result_chart->fetch_assoc()) {
    $data[$row['tanggal']] = (float)$row['total'];
}
$stmt_chart->close();

// Query untuk mendapatkan 5 barang yang paling banyak dibeli
$query_top_items = "SELECT nama_barang, SUM(jumlah) as total_terjual 
                   FROM transaksi_detail
                   GROUP BY nama_barang 
                   ORDER BY total_terjual DESC 
                   LIMIT 5";
$stmt_top_items = $conn->prepare($query_top_items);
$stmt_top_items->execute();
$result_top_items = $stmt_top_items->get_result();

$top_items = [];
foreach ($result_top_items as $row) {
    // Filter nama barang yang mengandung 'kabel meteran' (case insensitive)
    if (stripos($row['nama_barang'], 'kabel meteran') !== false) continue;
    $top_items[] = $row;
    if (count($top_items) >= 5) break;
}

$stmt_top_items->close();

// Cek apakah ini adalah permintaan AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    // Ambil data penjualan untuk grafik
    $query = "SELECT DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total FROM transaksi WHERE tanggal_transaksi BETWEEN ? AND ? GROUP BY DATE(tanggal_transaksi) ORDER BY tanggal";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    $total_penjualan = 0;
    while ($row = $result->fetch_assoc()) {
        $data[$row['tanggal']] = (float)$row['total'];
        $total_penjualan += (float)$row['total'];
    }
    $stmt->close();

    // Ambil total produk terjual sesuai filter
    $query_produk = "SELECT SUM(jumlah) as total_produk_terjual FROM transaksi WHERE tanggal_transaksi BETWEEN ? AND ?";
    $stmt_produk = $conn->prepare($query_produk);
    $stmt_produk->bind_param("ss", $start_date, $end_date);
    $stmt_produk->execute();
    $result_produk = $stmt_produk->get_result();
    $row_produk = $result_produk->fetch_assoc();
    $total_produk_terjual = (int)$row_produk['total_produk_terjual'];
    $stmt_produk->close();

    $conn->close();

    echo json_encode([
        'dates' => array_keys($data),
        'totals' => array_values($data),
        'total_penjualan' => $total_penjualan,
        'total_produk_terjual' => $total_produk_terjual
    ]);
    exit;
}

// Tambahkan total penjualan pada tanggal akhir ke dalam array totals
$total_penjualan_hari_ini = 0;
$query_total_hari_ini = "SELECT SUM(total_harga) as total_penjualan_hari_ini 
                         FROM transaksi 
                         WHERE tanggal_transaksi = ?";
$stmt_total_hari_ini = $conn->prepare($query_total_hari_ini);
$stmt_total_hari_ini->bind_param("s", $end_date);
$stmt_total_hari_ini->execute();
$result_total_hari_ini = $stmt_total_hari_ini->get_result();
$row_total_hari_ini = $result_total_hari_ini->fetch_assoc();
$total_penjualan_hari_ini = (float)$row_total_hari_ini['total_penjualan_hari_ini'];

// Tambahkan tanggal akhir dan total penjualan hari ini ke dalam array
$data[$end_date] = $total_penjualan_hari_ini;

// Siapkan array untuk grafik
$dates = array_keys($data); // Ambil semua tanggal
$totals = array_values($data); // Ambil semua total penjualan

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Wahyu Listrik</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #2c3e50;
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
    }

    .sidebar {
        width: 220px;
        background-color: #1a1d23;
        padding-top: 20px;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        color: #ecf0f1;
    }

    .sidebar h2 {
        font-size: 25px;
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        margin-bottom: 30px;
        color: #ff9900;
    }

    .sidebar a {
        padding: 15px;
        text-decoration: none;
        color: #ecf0f1;
        font-size: 16px;
        display: block;
        margin-bottom: 10px;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #2c3e50;
    }

    .main-content {
        margin-left: 240px;
        padding: 20px;
        flex: 1;
    }

    .header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        background-color: #1a1d23;
        padding: 10px 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .header h1 {
        font-size: 30px;
        margin: 0;
        color: #ecf0f1;
    }

    .header .user-info {
        display: flex;
        align-items: center;
    }

    .header .user-info img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .stats {
        display: flex;
        justify-content: space-between;
        margin-top: 10px; /* Menambahkan jarak antara header dan stats */
        margin-bottom: 10px;
    }

    .card {
        background: #1a1d23;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        flex: 1;
        margin: 0 5px;
        max-width: 1000px;
        height: 90px;
    }

    .card h3 {
        margin: 15px 0 0 0;
        font-size: 22px;
        color: #ff9900;
    }

    .card p {
        color: #ecf0f1;
        font-size: 20px;
        margin-top: 10px;
    }

    .chart-container {
        background: #1a1d23;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        max-width: 70%; /* Ubah lebar agar bisa berdampingan dengan barang terlaris */
        height: 350px;
        display: inline-block; /* Tambahkan display inline-block */
        vertical-align: top; /* Vertikal sejajar dengan barang terlaris */
    }

    .menu-container {
        background-color: #1a1d23;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
    }

    .menu-button {
        background-color: rgba(226, 158, 32, 0.9);
        color: #ffffff;
        border: none;
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        font-size: 16px;
        font-weight: 550;
    }

    .menu-button:hover {
        background-color: rgba(222, 200, 125, 0.9);
    }

    #myChart {
        width: 100%;
        height: 100%;
    }

    .barang-terlaris {
        color: #ffffff;
        background: #1a1d23;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 36.5%;
        margin-left: 1%;
        display: inline-block;
        vertical-align: top;
        padding: 20px;
    }

    .barang-terlaris h3 {
        text-align: center;
        margin: 10px 0 0 0;
        font-size: 30px;
        color: #ff9900;
    }
    .barang-terlaris p {
        text-align: left;
        color: #ecf0f1;
        font-size: 26px;
    }
</style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2 style="text-align: center">Wahyu Listrik</h2>
        <a href="index.php">Halaman Utama</a>
        <a href="input.php">Tambah Barang Baru</a>
        <a href="form_pembelian.php">Pembelian</a>
        <a href="view.php?jenis=listrik">Stok Listrik</a>
        <a href="view.php?jenis=atk">Stok ATK</a>
        <a href="riwayat_transaksi.php">Riwayat Transaksi</a>
        <!-- <a href="supplier.php">Supplier</a>
        <a href="restock.php">Restok Barang Gudang</a> -->
        <a href="input_karyawan.php">Karyawan</a>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Owner</h1>
            <div class="user-info">
                <img src="IMG_4283.jpg" alt="User  ">
                <p style="color: orange;">OWNER</p>
            </div>
        </div>

        <div class="stats">
            <div class="card">
                <h3 id="judulTotalPenjualan">
                    <?php
                    $is_default = ($start_date == date('Y-m-01') && $end_date == date('Y-m-d'));
                    echo $is_default ? 'Total Penjualan Bulan Ini' : 'Total Penjualan';
                    ?>
                </h3>
                <p id="totalPenjualan">Rp <?php echo number_format($total_penjualan, 2); ?></p>
            </div>
            <div class="card">
                <h3 id="judulProdukTerjual">
                    <?php echo $is_default ? 'Produk Terjual Bulan Ini' : 'Produk Terjual'; ?>
                </h3>
                <p id="totalProdukTerjual"><?php echo $total_produk_terjual; ?></p>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>

        <div class="barang-terlaris">
            <h3>Barang Terlaris</h3>
            <ul>
                <?php $i = 1; foreach ($top_items as $item): ?>
                    <p><?php echo $i . ". " . $item['nama_barang'] . " - " . $item['total_terjual'] . " terjual"; ?></p>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="menu-container">
            <input type="date" id="start_date" class="date-picker" value="<?php echo $start_date; ?>">
            <input type="date" id="end_date" class="date-picker" value="<?php echo $end_date; ?>">
            <button id="filterButton" class="menu-button">Tampilkan Grafik</button>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart;

        function updateChart(dates, totals) {
            if (myChart) {
                myChart.destroy();
            }
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: totals,
                        backgroundColor: 'rgba(52, 152, 219, 0.2)', // Area di bawah garis
                        borderColor: 'rgba(52, 152, 219, 1)', // Warna garis
                        borderWidth: 2, // Ketebalan garis
                        pointBackgroundColor: 'rgba(52, 152, 219, 1)', // Warna titik
                        pointBorderColor: '#fff', // Warna border titik
                        pointBorderWidth: 2, // Ketebalan border titik
                        pointRadius: 5, // Ukuran titik
                        fill: false // Tidak mengisi area di bawah garis
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)', // Warna garis grid
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)', // Warna garis grid
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                }
            });
        }
        
        document.getElementById('filterButton').addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            fetch(`dashboard.php?start_date=${startDate}&end_date=${endDate}&ajax=true`)
                .then(response => response.json())
                .then(data => {
                    updateChart(data.dates, data.totals);
                    document.getElementById('totalPenjualan').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.total_penjualan);
                    document.getElementById('totalProdukTerjual').textContent = data.total_produk_terjual;
                    // Ubah judul card sesuai filter
                    const today = new Date();
                    const defaultStart = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2, '0') + '-01';
                    const defaultEnd = today.toISOString().slice(0,10);
                    if (startDate === defaultStart && endDate === defaultEnd) {
                        document.getElementById('judulTotalPenjualan').textContent = 'Total Penjualan Bulan Ini';
                        document.getElementById('judulProdukTerjual').textContent = 'Produk Terjual Bulan Ini';
                    } else {
                        document.getElementById('judulTotalPenjualan').textContent = 'Total Penjualan';
                        document.getElementById('judulProdukTerjual').textContent = 'Produk Terjual';
                    }
                });
        });

        // Inisialisasi chart pertama kali
        updateChart(<?php echo json_encode($dates); ?>, <?php echo json_encode($totals); ?>);
    </script>
</body>
</html>
