<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah user sudah login
$login_as_karyawan = $_SESSION['login_as_karyawan'] ?? false;
$login_as_owner = $_SESSION['login_as_owner'] ?? false;

// Jika tidak login sebagai karyawan atau owner, arahkan ke halaman utama
if (!$login_as_karyawan && !$login_as_owner) {
    header("Location: index.php");
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'wahyulistrik');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Proses untuk restock barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restock_barang'])) {
    $supplier_name = $_POST['supplier_name'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $harga_modal = $_POST['harga_modal'];
    $jenis_barang = $_POST['jenis_barang']; // Menyimpan jenis barang

    // Cek apakah supplier sudah ada
    $sql = "SELECT * FROM supplier WHERE nama_supplier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $supplier_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Supplier sudah ada, ambil id_supplier
        $supplier = $result->fetch_assoc();
        $id_supplier = $supplier['id_supplier'];

        // Simpan data ke tabel barangsuplier
        $insert_query = "INSERT INTO barangsuplier (id_supplier, nama_barang, jumlah, harga_modal, jenis) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isids", $id_supplier, $item_name, $quantity, $harga_modal, $jenis_barang);
        
        if ($insert_stmt->execute()) {
            $message = "<div class='message success'>Restock barang berhasil dilakukan.</div>";
        } else {
            $message = "<div class='message error'>Gagal menyimpan data ke tabel barangsuplier: " . $insert_stmt->error . "</div>";
        }
        $insert_stmt->close();
    } else {
        $message = "<div class='message error'>Supplier tidak ditemukan. Silakan tambahkan supplier terlebih dahulu.</div>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock Barang</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .judul {
            color: #ffffff;
        }
        .container {
            position: relative;
            z-index: 1;
            background-color: rgba(81, 88, 94, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            width: 100%;
            max-width: 550px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 0rem;
        }
        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .back-button {
            display: inline-block;
            margin-top: 1rem;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #007BFF;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 1rem;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="judul">Restock Barang</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="supplier_name">Nama Supplier:</label>
                <input type="text" id="supplier_name" name="supplier_name" required>
            </div>
            <div class="form-group">
                <label for="item_name">Nama Barang:</label>
                <input type="text" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="quantity">Jumlah:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="harga_modal">Harga Modal:</label>
                <input type="number" id="harga_modal" name="harga_modal" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="jenis_barang">Jenis Barang:</label>
                <select id="jenis_barang" name="jenis_barang" required>
                    <option value="listrik">Listrik</option>
                    <option value="atk">ATK</option>
                </select>
            </div>
            <button type="submit" name="restock_barang" class="submit-button">Restock</button>
        </form>
        <a href="dashboard.php" class="back-button">Kembali</a>
    </div>
</body>
</html>