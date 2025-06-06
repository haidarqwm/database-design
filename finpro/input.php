<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

$message = "";

// Cek apakah user sudah login
$login_as_karyawan = $_SESSION['login_as_karyawan'] ?? false;
$login_as_owner = $_SESSION['login_as_owner'] ?? false;

// Jika tidak login sebagai karyawan atau owner, arahkan ke halaman utama
if (!$login_as_karyawan && !$login_as_owner) {
    header("Location: index.php");
    exit;
}

// Proses form jika metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $harga_beli = $_POST['harga_beli'];
    $jumlah = $_POST['jumlah'];

    // Koneksi ke database
    $conn = new mysqli('localhost', 'root', '', 'wahyulistrik');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Generate ID barang berdasarkan jenis
    $id_column = ($jenis == 'listrik') ? 'idlistrik' : 'idatk';
    $table = ($jenis == 'listrik') ? 'datalistrik' : 'dataatk';
    
    // Cek ID terakhir
    $query = "SELECT MAX(CAST($id_column AS UNSIGNED)) as max_id FROM $table";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $last_id = $row['max_id'] ?? 0;
    $next_id = $last_id + 1;
    
    // Format ID dengan padding 2 digit
    $idbarang = str_pad($next_id, 2, '0', STR_PAD_LEFT);

    // Generate barcode otomatis dengan format lebih pendek
    $barcode = substr(uniqid(), -8); // Mengambil 8 karakter terakhir dari uniqid

    // Tambahkan barang ke database
    $sql = "INSERT INTO $table ($id_column, nama, harga, harga_beli, jumlah, barcode) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddis", $idbarang, $nama, $harga, $harga_beli, $jumlah, $barcode);
    
    if ($stmt->execute()) {
        // Buat barcode HTML dengan angka di bawahnya
        $generatorHTML = new BarcodeGeneratorHTML();
        $barcodeHTML = $generatorHTML->getBarcode($barcode, $generatorHTML::TYPE_CODE_128);
        $barcodeHTML .= "<div style='text-align: center; font-size: 14px; margin-top: 5px;'>$barcode</div>";

        // Buat barcode PNG
        $generatorPNG = new BarcodeGeneratorPNG();
        $barcodePNG = $generatorPNG->getBarcode($barcode, $generatorPNG::TYPE_CODE_128);

        // Pastikan direktori barcodes ada
        $directory = 'barcodes';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Simpan barcode PNG
        $filename = $directory . '/' . $barcode . '.png';
        file_put_contents($filename, $barcodePNG);

        // Tambahkan kode barcode di bawah gambar
        $image = imagecreatefrompng($filename);
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Buat gambar baru dengan tinggi tambahan untuk teks dan padding
        $padding = 20;
        $newWidth = $width + ($padding * 2);
        $newHeight = $height + 30 + ($padding * 2);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Isi background putih
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);
        
        // Copy barcode ke gambar baru dengan padding
        imagecopy($newImage, $image, $padding, $padding, 0, 0, $width, $height);
        
        // Tambahkan teks barcode
        $black = imagecolorallocate($newImage, 0, 0, 0);
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($barcode);
        $textX = ($newWidth - $textWidth) / 2;
        imagestring($newImage, $font, $textX, $height + $padding + 5, $barcode, $black);
        
        // Simpan gambar baru
        imagepng($newImage, $filename);
        imagedestroy($image);
        imagedestroy($newImage);

        // Redirect dengan status sukses
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
    } else {
        // Redirect dengan status error
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=error");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Barang</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .judul {
            color: #ffffff; /* Ganti dengan warna yang diinginkan */
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
            margin: auto; /* Center the container */
        }
        .form-group {
            margin-bottom: 0rem;
        }
        .submit-button {
            background-color: #4CAF50; /* Green */
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
            padding:  10px 15px;
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
        /* Styling untuk notifikasi */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .notification.success {
            background-color: #4CAF50;
        }

        .notification.error {
            background-color: #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="judul">Input Barang</h1>
        <?php 
        // Tampilkan notifikasi jika ada
        if (isset($_GET['status'])) {
            $message = '';
            $type = '';
            
            switch ($_GET['status']) {
                case 'success':
                    $message = 'Barang berhasil ditambahkan!';
                    $type = 'success';
                    break;
                case 'error':
                    $message = 'Gagal menambahkan barang!';
                    $type = 'error';
                    break;
            }
            
            if ($message) {
                echo "<div class='notification $type' id='notification'>$message</div>";
                echo "<script>
                    setTimeout(function() {
                        var notification = document.getElementById('notification');
                        notification.style.animation = 'fadeOut 0.5s ease-out forwards';
                        setTimeout(function() {
                            notification.remove();
                        }, 500);
                    }, 3000);
                </script>";
            }
        }
        ?>
        <?php echo $barcodeHTML ?? ''; ?>
        <form id="barangForm" action="" method="post">
            <div class="form-group">
                <label for="jenis">Jenis Barang:</label>
                <select id="jenis" name="jenis" required>
                    <option value="listrik">Listrik</option>
                    <option value="atk">ATK</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Barang:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            
            <div class="form-group">
                <label for="harga">Harga Jual Barang:</label>
                <input type="number" id="harga" name="harga" required>
            </div>
            
            <div class="form-group">
                <label for="harga_beli">Harga Beli Barang:</label>
                <input type="number" id="harga_beli" name="harga_beli" required>
            </div>
            
            <div class="form-group">
                <label for="jumlah">Jumlah Barang:</label>
                <input type="number" id="jumlah" name="jumlah" required>
            </div>
            
            <button type="submit" class="submit-button">Tambah Barang</button>
        </form>
        <a class="back-button" href="<?php echo isset($_SESSION['login_as_karyawan']) ? 'dashboard_karyawan.php' : 'dashboard.php'; ?>">Kembali ke Menu Dashboard</a>
    </div>
</body>
</html>