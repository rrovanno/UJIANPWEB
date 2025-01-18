<?php
session_start();
$guest = null;

if (isset($_SESSION['guest_data'])) {
    $guest = $_SESSION['guest_data'];

    // Hapus session setelah data ditampilkan (jika diinginkan)
    // unset($_SESSION['guest_data']);
} else {
    // Jika data tidak ditemukan, redirect atau tampilkan pesan error
    header("Location: home.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/confirmation-view.css">
    <link rel="stylesheet" href="./style/confirmation-mobile.css">
    <title>Konfirmasi Pendaftaran</title>
</head>
<body>
    <div class="confirmation-wrapper">
        <h1>Terima Kasih!</h1>
        <p>Pendaftaran Anda berhasil. Berikut adalah informasi Anda:</p>
        <div class="guest-info">
            <p><strong>Nama:</strong> <?= htmlspecialchars($guest['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($guest['email']) ?></p>
            <p><strong>Telepon:</strong> <?= htmlspecialchars($guest['phone']) ?></p>
            <p><strong>Jumlah Tamu:</strong> <?= htmlspecialchars($guest['guests_count']) ?></p>
            <p><strong>Catatan:</strong> <?= htmlspecialchars($guest['notes']) ?></p>
            <p><strong>Information:</strong> Data anda sudah tersimpan. saat di resepsionis tinggal beritahukan nama lengkap anda</p>
        </div>
        <button onclick="window.location.href='home.php';">Kembali ke Beranda</button>
    </div>
</body>
</html>
