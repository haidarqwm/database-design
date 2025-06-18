<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Query to get total transactions
$query = "SELECT COUNT(*) as total_transaksi FROM transaksi";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_transaksi = $row['total_transaksi'];

// Get total transactions for today
$today = date('Y-m-d');
$query_today = "SELECT COUNT(*) as total_hari_ini FROM transaksi WHERE DATE(tanggal) = '$today'";
$result_today = $conn->query($query_today);
$row_today = $result_today->fetch_assoc();
$total_hari_ini = $row_today['total_hari_ini'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Transaksi - Wahyu Listrik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Informasi Total Transaksi</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Semua Transaksi</h5>
                                        <h2 class="display-4 text-primary"><?php echo number_format($total_transaksi); ?></h2>
                                        <p class="text-muted">Transaksi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Transaksi Hari Ini</h5>
                                        <h2 class="display-4 text-success"><?php echo number_format($total_hari_ini); ?></h2>
                                        <p class="text-muted">Transaksi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 