<?php
session_start();
include 'config.php';

// Jika form password sudah disubmit
if (isset($_POST['password_owner']) && isset($_POST['index'])) {
    $password = $_POST['password_owner'];
    $index = $_POST['index'];
    // Ambil password owner dari database
    $query = "SELECT password FROM owner LIMIT 1";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $password_owner_db = $row['password'];
    // Cek password (plain, jika sudah hash silakan sesuaikan)
    if ($password === $password_owner_db) {
        // Password benar, hapus barang
        $_POST['index'] = $index;
        include 'remove_from_cart.php';
        exit();
    } else {
        // Password salah
        $_SESSION['error_owner'] = 'Password owner salah!';
        header('Location: pembelian.php');
        exit();
    }
}
// Jika baru dari tombol hapus, tampilkan form password
if (isset($_POST['index'])) {
    $index = $_POST['index'];
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Verifikasi Owner</title>
        <style>
            body { font-family: Arial; background: #f5f5f5; }
            .modal { max-width: 400px; margin: 100px auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px #0002; }
            label { display: block; margin-bottom: 0.5rem; }
            input[type=password] { width: 100%; padding: 0.5rem; margin-bottom: 1rem; }
            button { padding: 0.5rem 1.5rem; background: #27ae60; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #219a52; }
        </style>
    </head>
    <body>
        <div class="modal">
            <form method="post">
                <input type="hidden" name="index" value="<?php echo htmlspecialchars($index); ?>">
                <label for="password_owner">Masukkan Password Owner untuk menghapus barang:</label>
                <input type="password" name="password_owner" id="password_owner" required autofocus>
                <button type="submit">Verifikasi & Hapus</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}
// Jika tidak ada index, kembali ke pembelian
header('Location: pembelian.php');
exit(); 