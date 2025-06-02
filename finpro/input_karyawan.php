<?php
include 'config.php';

// Handle tambah/edit karyawan
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idkaryawan = $_POST['idkaryawan'] ?? '';
    $nama_karyawan = $_POST['nama_karyawan'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $edit_mode = isset($_POST['edit_mode']) && $_POST['edit_mode'] === '1';
    $old_idkaryawan = $_POST['old_idkaryawan'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';

    // Validasi ID Karyawan harus 6 digit angka
    if (!preg_match('/^\d{6}$/', $idkaryawan)) {
        $message = "<div style='color:red;'>ID Karyawan harus 6 digit angka!</div>";
    } else if (!preg_match('/^\d{12,}$/', $no_telp)) {
        $message = "<div style='color:red;'>No. Telepon minimal 12 digit angka!</div>";
    } else {
        if ($edit_mode) {
            // Update karyawan
            $stmt = $conn->prepare("UPDATE karyawan SET idkaryawan=?, nama_karyawan=?, gender=?, alamat=?, no_telp=? WHERE idkaryawan=?");
            $stmt->bind_param("ssssss", $idkaryawan, $nama_karyawan, $gender, $alamat, $no_telp, $old_idkaryawan);
            if ($stmt->execute()) {
                $message = "<div style='color:green;'>Data karyawan berhasil diupdate!</div>";
            } else {
                $message = "<div style='color:red;'>Gagal update: {$stmt->error}</div>";
            }
            $stmt->close();
        } else {
            // Tambah karyawan baru
            $stmt = $conn->prepare("INSERT INTO karyawan (idkaryawan, nama_karyawan, gender, alamat, no_telp) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $idkaryawan, $nama_karyawan, $gender, $alamat, $no_telp);
            if ($stmt->execute()) {
                $message = "<div style='color:green;'>Karyawan berhasil ditambahkan!</div>";
            } else {
                $message = "<div style='color:red;'>Gagal tambah: {$stmt->error}</div>";
            }
            $stmt->close();
        }
        // Redirect untuk mencegah resubmit
        header("Location: input_karyawan.php?msg=".urlencode(strip_tags($message)));
        exit();
    }
}

// Handle hapus karyawan
if (isset($_GET['delete'])) {
    $idkaryawan = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM karyawan WHERE idkaryawan=?");
    $stmt->bind_param("s", $idkaryawan);
    if ($stmt->execute()) {
        $message = "<div style='color:green;'>Karyawan berhasil dihapus!</div>";
    } else {
        $message = "<div style='color:red;'>Gagal hapus: {$stmt->error}</div>";
    }
    $stmt->close();
    header("Location: input_karyawan.php?msg=".urlencode(strip_tags($message)));
    exit();
}

// Ambil pesan dari redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// Handle edit: ambil data karyawan untuk form
$edit_mode = false;
$edit_data = [
    'idkaryawan' => '',
    'nama_karyawan' => '',
    'gender' => '',
    'alamat' => '',
    'no_telp' => ''
];
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $idkaryawan = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM karyawan WHERE idkaryawan=?");
    $stmt->bind_param("s", $idkaryawan);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $edit_data = $row;
    }
    $stmt->close();
}

// Ambil semua karyawan
$karyawan = [];
$result = $conn->query("SELECT * FROM karyawan ORDER BY idkaryawan DESC");
while ($row = $result->fetch_assoc()) {
    $karyawan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('foto1.jpg') center top / cover no-repeat;
            background-color: #1a1818;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* overflow: hidden; */
        }
        .container {
            background-color: rgba(81, 88, 94, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            padding: 2.5rem 2.5rem;
            width: 100%;
            max-width: 1000px;
            margin-top: 100px;
            margin-bottom: 5px;
        }
        h1 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 40px;
        }
        @media (max-width: 500px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        .form-group label {
            color: #fffdfd;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: 2px solid #e29e20;
            background: rgba(255,255,255,0.2);
        }
        .submit-btn {
            background-color: rgba(226, 158, 32, 0.9);
            color: white;
            font-size: 1.1em;
            padding: 12px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .submit-btn:hover {
            background-color: rgba(222, 200, 125, 0.9);
            transform: scale(1.03);
        }
        .back-btn {
            display: inline-block;
            text-align: center;
            margin-top: 10px;
            font-size: 1.1em;
            color: #ffffff;
            text-decoration: none;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .action-btn {
            padding: 7px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 2px;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.2s;
        }
        .edit-btn {
            background-color: #ffc107;
            color: #333;
        }
        .edit-btn:hover {
            background-color: #e0a800;
            color: #fff;
            transform: scale(1.05);
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .delete-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px 8px;
            text-align: center;
            color: #fff;
        }
        th {
            background-color: #1a1d23;
            color: #e29e20;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: rgba(255,255,255,0.03);
        }
        tr:hover {
            background-color: rgba(226, 158, 32, 0.15);
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        th.col-alamat, td.col-alamat {
            min-width: 180px;
        }
        th.col-telp, td.col-telp {
            min-width: 130px;
        }
        th.col-aksi, td.col-aksi {
            min-width: 150px;
        }
        /* Agar option di select tetap putih dan background gelap */
        select, select option {
            color: #fff;
            background: #23272b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manajemen Karyawan</h1>
        <?php echo $message; ?>
        <form action="input_karyawan.php" method="post">
            <input type="hidden" name="edit_mode" value="<?php echo $edit_mode ? '1' : ''; ?>">
            <input type="hidden" name="old_idkaryawan" value="<?php echo htmlspecialchars($edit_data['idkaryawan'] ?? ''); ?>">
            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label for="idkaryawan">ID Karyawan:</label>
                        <input type="text" id="idkaryawan" name="idkaryawan" value="<?php echo htmlspecialchars($edit_data['idkaryawan'] ?? ''); ?>" required <?php echo $edit_mode ? 'readonly' : ''; ?>
                        pattern="\d{6}" minlength="6" maxlength="6" title="ID Karyawan harus 6 digit angka">
                    </div>
                    <div class="form-group">
                        <label for="nama_karyawan">Nama Karyawan:</label>
                        <input type="text" id="nama_karyawan" name="nama_karyawan" value="<?php echo htmlspecialchars($edit_data['nama_karyawan'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin:</label>
                        <select id="gender" name="gender" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" <?php if(($edit_data['gender'] ?? '')==='L') echo 'selected'; ?>>Laki-laki</option>
                            <option value="P" <?php if(($edit_data['gender'] ?? '')==='P') echo 'selected'; ?>>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($edit_data['alamat'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="no_telp">No. Telepon:</label>
                        <input type="text" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($edit_data['no_telp'] ?? ''); ?>" required pattern="\d{12,}" minlength="12" title="No. Telepon minimal 12 digit angka">
                    </div>
                </div>
            </div>
            <button type="submit" class="submit-btn"><?php echo $edit_mode ? 'Simpan' : 'Tambahkan Karyawan'; ?></button>
            <?php if($edit_mode): ?>
                <a href="input_karyawan.php" class="back-btn" style="background:#6c757d;">Batal Edit</a>
            <?php endif; ?>
        </form>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Karyawan</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Kelamin</th>
                        <th class="col-alamat">Alamat</th>
                        <th class="col-telp">No. Telepon</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($karyawan as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['idkaryawan']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                        <td><?php echo $row['gender'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td class="col-alamat"><?php echo htmlspecialchars($row['alamat'] ?? ''); ?></td>
                        <td class="col-telp"><?php echo htmlspecialchars($row['no_telp'] ?? ''); ?></td>
                        <td class="col-aksi">
                            <a href="input_karyawan.php?edit=<?php echo urlencode($row['idkaryawan']); ?>" class="action-btn edit-btn">Edit</a>
                            <a href="input_karyawan.php?delete=<?php echo urlencode($row['idkaryawan']); ?>" class="action-btn delete-btn" onclick="return confirm('Yakin ingin menghapus karyawan ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="dashboard.php" class="back-btn" style="margin-top:32px;">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>