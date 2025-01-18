<?php
$host = 'localhost';
$db = 'wedding_db';
$user = 'root'; // Ganti dengan username MySQL Anda
$pass = ''; // Ganti dengan password MySQL Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>