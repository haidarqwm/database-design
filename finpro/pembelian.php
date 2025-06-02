<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah karyawan atau owner sudah login
if (!isset($_SESSION['login_as_karyawan']) && !isset($_SESSION['login_as_owner'])) {
    echo "<script>
            alert('Anda harus masuk sebagai karyawan atau owner untuk melakukan pembelian.');
            window.location.href='index.php';
          </script>";
    exit();
}

// Ambil ID Karyawan dan Nama Karyawan dari session
$idkaryawan = $_SESSION['idkaryawan'] ?? null; // Ambil ID Karyawan dari session
$nama_karyawan = $_SESSION['nama_karyawan'] ?? null; // Ambil Nama Karyawan dari session

// Ambil ID Owner dan Nama Owner dari session
$idowner = $_SESSION['idowner'] ?? null; // Ambil ID Owner dari session
$nama_owner = $_SESSION['nama_owner'] ?? null; // Ambil Nama Owner dari session

// Pastikan ID Karyawan dan Nama Karyawan tidak kosong
if (empty($idkaryawan) && empty($idowner)) {
    echo "Karyawan atau Owner tidak terdaftar. Silakan login kembali.";
    exit;
}

// Cek apakah customer sudah diinput
if (!isset($_SESSION['idcustomer'])) {
    header("Location: get_next_customer_id.php");
    exit();
}

// Inisialisasi keranjang belanja jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

include 'config.php';

// Ambil data produk dari database
$listrikProducts = [];
$atkProducts = [];

// Ambil produk listrik
$sqlListrik = "SELECT idlistrik, nama, harga FROM datalistrik";
$resultListrik = $conn->query($sqlListrik);
if ($resultListrik->num_rows > 0) {
    while ($row = $resultListrik->fetch_assoc()) {
        $listrikProducts[] = $row;
    }
}

// Ambil produk ATK
$sqlAtk = "SELECT idatk, nama, harga FROM dataatk";
$resultAtk = $conn->query($sqlAtk);
if ($resultAtk->num_rows > 0) {
    while ($row = $resultAtk->fetch_assoc()) {
        $atkProducts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Barang - Toko Wahyu Listrik</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <style>
        /* CSS Anda di sini */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #ecf0f1;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('foto1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 1rem;
            background-color: rgba(81, 88, 94, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        h1, h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        form {
            display: grid;
            gap: 1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 0rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 1rem;
        }

        input:focus, select:focus {
            outline: none;
            border-color: rgba(226, 158, 32, 0.9);
            box-shadow: 0 0 5px rgba(226, 158, 32, 0.5);
        }

        button {
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: rgba(222, 200, 125, 0.9);
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            font-weight: 500;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .back-button {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-align: center;
            margin-top: 2rem;
        }

        .back-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .checkout-button {
            background-color: #27ae60;
            width: 100%;
            max-width: 200px;
            margin: auto;
            display: block;
        }

        .checkout-button:hover {
            background-color: #219a52;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: #ffffff;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }

        #camera-container {
            margin-bottom: 0rem;
            text-align: center;
        }

        #camera-preview {
            width: 100%;
            max-width: 640px;
            height: auto;
            margin: 1rem 0;
            border: 2px solid #ccc;
            border-radius: 8px;
        }

        #start-camera {
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #start-camera:hover {
            background-color: rgba(222, 200, 125, 0.9);
            transform: translateY(-2px);
        }

        .form-group select {
            color: #000000;
            background-color: #ffffff;
        }

        .form-group select option {
            color: #000000;
            background-color: #ffffff;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Pembelian Barang</h1>
    <div class="customer-info">
        <p>ID Customer: <?php echo htmlspecialchars($_SESSION['idcustomer']); ?></p>
    </div>

   <!-- Form untuk menambah barang ke keranjang -->
   <form action="add_to_cart.php" method="post" id="cartForm">
        <input type="hidden" id="jenis" name="jenis">
        <input type="hidden" id="nama" name="nama">
        <input type="hidden" id="jumlah" name="jumlah" value="1">
        
        <div class="form-group">
            <label for="barcode">Scan Barcode:</label>
            <input type="text" id="barcode" name="barcode" autofocus>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('barcode');
            const cartForm = document.getElementById('cartForm');
            
            // Fokus otomatis ke input barcode
            barcodeInput.focus();
            
            // Menangani input dari scanner barcode
            barcodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const barcode = this.value;
                    
                    // Mengambil data produk dari server berdasarkan barcode
                    fetch('get_product_by_barcode.php?barcode=' + barcode)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Respon jaringan tidak baik");
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log("Respon dari server:", data);
                            if (data.success) {
                                // Mengisi hidden input dengan informasi produk
                                document.getElementById('nama').value = data.product.nama;
                                document.getElementById('jenis').value = data.product.jenis;
                                
                                // Submit form secara otomatis
                                cartForm.submit();
                                
                                // Reset input barcode dan fokus kembali
                                this.value = '';
                                this.focus();
                            } else {
                                alert(data.message); // Menampilkan pesan jika produk tidak ditemukan
                                this.value = '';
                                this.focus();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat memproses barcode.');
                            this.value = '';
                            this.focus();
                        });
                }
            });
        });
    </script>

    <!-- Tampilkan Keranjang Belanja -->
    <?php if (!empty($_SESSION['keranjang'])): ?>
        <?php if (isset($_SESSION['error_owner'])): ?>
            <div style="color:red;text-align:center;"><?php echo $_SESSION['error_owner']; unset($_SESSION['error_owner']); ?></div>
        <?php endif; ?>
        <h2>Keranjang Belanja</h2>
        <table>
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($_SESSION['keranjang'] as $key => $item): 
                    $subtotal = $item['harga'] * $item['jumlah'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['jenis']); ?></td>
                        <td><?php echo htmlspecialchars($item['nama']); ?></td>
                        <td>
                            <?php if (stripos($item['nama'], 'kabel meteran') !== false): ?>
                                <form action="update_qty_cart.php" method="post" style="display:inline;">
                                    <input type="hidden" name="index" value="<?php echo $key; ?>">
                                    <input type="number" name="jumlah" value="<?php echo htmlspecialchars($item['jumlah']); ?>" min="1" style="width:60px;">
                                    <button type="submit">Update</button>
                                </form>
                            <?php else: ?>
                                <?php echo htmlspecialchars($item['jumlah']); ?>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td>
                            <?php if (isset($_SESSION['login_as_karyawan'])): ?>
                                <form action="verify_owner.php" method="post">
                                    <input type="hidden" name="index" value="<?php echo $key; ?>">
                                    <button type="submit">Hapus</button>
                                </form>
                            <?php else: ?>
                                <form action="remove_from_cart.php" method="post">
                                    <input type="hidden" name="index" value="<?php echo $key; ?>">
                                    <button type="submit">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                    <td colspan="2"><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <form action="process_checkout.php" method="post">
            <input type="hidden" name="idkaryawan" value="<?php echo htmlspecialchars($idkaryawan); ?>">
            <input type="hidden" name="nama_karyawan" value="<?php echo htmlspecialchars($nama_karyawan); ?>">
            <div class="form-group">
                <label for="metode_pembayaran">Metode Pembayaran:</label>
                <select name="metode_pembayaran" id="metode_pembayaran" required>
                    <option value="Tunai">Tunai</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Transfer">Transfer</option>
                </select>
            </div>
            <button class="checkout-button" type="submit">Proses Checkout</button>
        </form>
    <?php else: ?>
        <p class="empty-cart">Keranjang belanja Anda kosong.</p>
    <?php endif; ?>
    
    <a class="back-button" href="<?php echo isset($_SESSION['login_as_karyawan']) ? 'dashboard_karyawan.php' : 'dashboard.php'; ?>">Kembali ke Dashboard</a>
</div>
</body>
</html>