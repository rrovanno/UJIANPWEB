<?php
session_start();
require_once __DIR__ . "/../Config/db.php";
$connection = getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Redirect ke halaman login jika admin belum login
}

$id = $_GET['id'] ?? null;
if ($id === null) {
    header('Location: dashboard_modern.php');
    exit();
}

// Hapus tamu berdasarkan ID
$sql = "DELETE FROM guests WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->execute([$id]);

header('Location: dashboard_modern.php'); // Redirect kembali ke dashboard setelah menghapus
exit();
