<?php
session_start();
include 'config.php';

// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah karyawan atau owner sudah login
if (!isset($_SESSION['login_as_karyawan']) && !isset($_SESSION['login_as_owner'])) {
    echo "<script>
            alert('Anda harus masuk sebagai karyawan atau owner untuk melakukan pembelian.');
            window.location.href='index.php';
          </script>";
}

// Cek apakah sudah ada transaksi berjalan
if (!isset($_SESSION['idcustomer']) || !isset($_SESSION['transaksi_berjalan'])) {
    // Generate ID Customer baru
    $query = "SELECT MAX(CAST(SUBSTRING(idcustomer, 5) AS UNSIGNED)) as max_id 
              FROM customer WHERE idcustomer LIKE 'CST-%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = $row['max_id'] + 1;

    $_SESSION['idcustomer'] = sprintf("CST-%03d", $next_id);
    $_SESSION['transaksi_berjalan'] = true; // Tandai bahwa transaksi sedang berjalan
}

header("Location: pembelian.php");
exit();

$error_message = '';

// Proses form jika metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate ID Customer baru
    $query = "SELECT MAX(CAST(SUBSTRING(idcustomer, 5) AS UNSIGNED)) as max_id FROM customer WHERE idcustomer LIKE 'CST-%'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error_message = "Error in query: " . mysqli_error($conn);
    } else {
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['max_id'];
        $next_id = $last_id + 1;
        $idcustomer = sprintf("CST-%03d", $next_id);

        // Insert customer baru tanpa nama customer
        $insert_query = "INSERT INTO customer (idcustomer) VALUES (?)"; // Hapus namacustomer dari query
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        
        if ($insert_stmt) {
            mysqli_stmt_bind_param($insert_stmt, "s", $idcustomer);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                // Simpan ID customer ke session
                $_SESSION['idcustomer'] = $idcustomer;

                // Redirect ke halaman pembelian
                header("Location: pembelian.php");
                exit;
            } else {
                $error_message = "Error: Gagal menambahkan customer! " . mysqli_stmt_error($insert_stmt);
            }
        } else {
            $error_message = "Error in preparing statement: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Input Data Customer</h1>
        <?php
        if (!empty($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
        <form id="customerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <button type="submit" class="submit-button">Lanjut ke Pembelian</button>
        </form>
        
        <a class="back-button" href="<?php echo isset($_SESSION['login_as_owner']) ? 'dashboard.php' : 'dashboard_karyawan.php'; ?>">Kembali ke Dashboard</a>
    </div>
</body>
</html>