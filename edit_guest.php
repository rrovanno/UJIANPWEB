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

// Mengambil data tamu berdasarkan ID
$sql = "SELECT * FROM guests WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->execute([$id]);
$guest = $stmt->fetch();

if (!$guest) {
    header('Location: dashboard_modern.php');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $is_present = $_POST['is_present'];
    
    // Mengatur nilai is_present berdasarkan apakah checkbox dicentang
    $is_present = isset($_POST['is_present']) ? 1 : 0; // 1 jika dicentang, 0 jika tidak

    // Update data tamu
    $sql = "UPDATE guests SET name = ?, email = ?, phone = ?, is_present = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);

    if ($stmt->execute([$name, $email, $phone, $is_present, $id])) {
        $message = "Data tamu berhasil diperbarui!";
        // Redirect kembali setelah memperbarui
        header("Location: dashboard_modern.php");
        exit();
    } else {
        $message = "Gagal memperbarui data tamu.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tamu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Tamu</h1>
        <form method="POST" class="form">
            <input class="input-edit" type="text" name="name" placeholder="Nama" value="<?php echo htmlspecialchars($guest['name']); ?>" required>
            <input class="input-edit" type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($guest['email']); ?>" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <input class="input-edit" type="text" name="phone" placeholder="No Telpon" value="<?php echo htmlspecialchars($guest['phone']); ?>" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <input type="checkbox" name="is_present" value="1" <?php echo $guest['is_present'] ? 'checked' : ''; ?>>
            <label>Hadir</label>
            <input type="submit" value="Simpan Perubahan">
            <p class="message"><?php echo $message; ?></p>
        </form>
        <a href="dashboard_modern.php" class="btn back">Kembali ke Dashboard</a>
    </div>
</body>
</html>
