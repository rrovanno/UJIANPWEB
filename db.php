<?php
include 'config.php'; // Pastikan config.php berisi detail koneksi yang benar

function getConnection() {
    global $host, $db, $user, $pass; // Memanggil variabel dari config.php

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=wedding_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection successful!";
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    
}
?>
