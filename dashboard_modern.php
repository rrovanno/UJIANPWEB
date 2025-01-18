<?php 
session_start();
require_once __DIR__ . "/../Config/db.php";
$connection = getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Redirect ke halaman login jika admin belum login
}
$_SESSION['is_admin'] = true;
// if(isset($_SESSION['valid_admin'])){
//     header("Location: dashboard_admin.php");
// }
// Reset valid_code setelah digunakan (opsional)
// dibawah error
// $_SESSION['valid_admin']==true;
// error diatas

// Mengambil informasi admin berdasarkan user_id
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// Menghapus semua tamu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_all_guests'])) {
    $sqlDelete = "DELETE FROM guests";
    $resetId = "ALTER TABLE guests AUTO_INCREMENT = 1";
    $connection->exec($sqlDelete);
    $connection->exec($resetId);
    $message = "Semua tamu berhasil dihapus!";
    header("Location: /daftartamupernikahan/Admin/dashboard_modern.php");
}

// Ekspor daftar tamu lengkap
if (isset($_POST['export_guests'])) {
    try {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="guests.csv"');

        // Query untuk mendapatkan data lengkap tamu
        $stmt = $connection->query("SELECT name, email, phone, guests_count, notes, is_present, created_at FROM guests ORDER BY created_at ASC");
        $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output header CSV
        $output = fopen("php://output", "w");
        fputcsv($output, ["No", "Nama", "Email", "No Telpon", "Jumlah Tamu", "Catatan", "Kehadiran", "Tanggal Ditambahkan"]); // Header CSV

        // Output baris tamu dengan nomor urut
        $no = 1;
        foreach ($guests as $guest) {
            fputcsv($output, [
                $no++,
                $guest['name'],
                $guest['email'],
                $guest['phone'],
                $guest['guests_count'],
                $guest['notes'],
                $guest['is_present'],
                $guest['created_at']
            ]);
        }
        fclose($output);
        exit();
    } catch (PDOException $e) {
        // Menangani kesalahan jika query gagal
        echo "Error: " . $e->getMessage();
    }
}

// Impor daftar tamu
if (isset($_POST['import_guests'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileExt != 'csv') {
            $message = "File yang diunggah harus berformat CSV.";
        } else {
            if (($handle = fopen($fileTmpName, "r")) !== FALSE) {
                fgetcsv($handle); // Lewati header CSV

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Ekstraksi data
                    $name = $data[1] ?? '';
                    $email = $data[2] ?? '';
                    $phone = $data[3] ?? '';
                    $guests_count = isset($data[4]) ? $data[4] : 0;
                    $notes = $data[5] ?? '';
                    $is_present = $data[6] ?? 0;

                    // Validasi dan format tanggal dari dd/mm/yyyy hh:mm:ss ke yyyy-mm-dd hh:mm:ss
                    $dateTime = !empty($data[7]) ? explode(' ', $data[7]) : null;
                    if ($dateTime) {
                        $date = $dateTime[0]; // Bagian tanggal
                        $time = $dateTime[1] ?? '00:00:00'; // Bagian waktu (atau default ke 00:00:00)
                        $dateParts = explode('/', $date); // Pecah tanggal berdasarkan '/'
                        
                        // Pastikan bagian tanggal ada
                        if (count($dateParts) === 3) {
                            // Konversi dari dd/mm/yyyy ke yyyy-mm-dd
                            $created_at = sprintf('%s-%s-%s %s', $dateParts[2], $dateParts[1], $dateParts[0], $time);
                        } else {
                            $created_at = date('Y-m-d H:i:s'); 
                        }
                    } else {
                        $created_at = date('Y-m-d H:i:s');
                    }

                    // Memasukkan data ke database
                    if (!empty($name) && !empty($email) && !empty($phone)) {
                        $sql = "INSERT INTO guests (name, email, phone, guests_count, notes, is_present, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $connection->prepare($sql);
                        $stmt->execute([$name, $email, $phone, $guests_count, $notes, $is_present, $created_at]);
                    }
                }
                fclose($handle);
                // Redirect after success
                $message = "Tamu berhasil diimpor dari file!";
                header("Location: dashboard_modern.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Gagal membuka file.";
            }
        }
    } else {
        $message = "Silakan unggah file CSV.";
    }
}

// Mengambil daftar tamu
$sql = "SELECT * FROM guests ORDER BY created_at ASC";
$stmt = $connection->query($sql);
$guests = $stmt->fetchAll();
$guestsCount = count($guests); 

if($guestsCount ==0){
    $resetId = "ALTER TABLE guests AUTO_INCREMENT = 1";
    $connection->exec($resetId);
}

$sqlPresent = "SELECT * FROM guests WHERE is_present = 1 ORDER BY created_at ASC";
$stmt = $connection->query($sqlPresent);
$guestsPresent = $stmt->fetchAll();
$guestsPresentCount = count($guestsPresent);

// Menghitung jumlah tamu
$guestsCount = count($guests);


// Mendapatkan nilai pencarian dari input
$search = isset($_GET['search']) ? $_GET['search'] : '';
// PAGINATION
// Jumlah data per halaman
$limit = 4;
// Halaman aktif saat ini (default halaman 1 jika tidak ada di URL)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Hitung OFFSET untuk query SQL
$offset = ($page - 1) * $limit;

// Query untuk menghitung total tamu berdasarkan hasil pencarian pada beberapa kolom
$sqlCount = "SELECT COUNT(*) AS total FROM guests WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR is_present LIKE ?";
$stmtCount = $connection->prepare($sqlCount);
$searchTerm = '%' . $search . '%';
$stmtCount->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
$totalGuests = $stmtCount->fetchColumn();
$totalPages = ceil($totalGuests / $limit);

// Query untuk mendapatkan data tamu berdasarkan pencarian pada beberapa kolom dan halaman
$sql = "SELECT * FROM guests WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR is_present LIKE ? ORDER BY created_at ASC LIMIT ? OFFSET ?";
$stmt = $connection->prepare($sql);
$stmt->bindValue(1, $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(2, $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(3, $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(4, $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(5, $limit, PDO::PARAM_INT);
$stmt->bindValue(6, $offset, PDO::PARAM_INT);
$stmt->execute();
$guests = $stmt->fetchAll();

// PAGINATION

// SECTION COMMENT PAGINATION
// $commentsPerPage = 5;
// $pageComments = isset($_GET['page_comments']) ? (int)$_GET['page_comments'] : 1;
// $offsetComments = ($pageComments - 1) * $commentsPerPage;

// Mengambil data komentar dari database
// $stmt = $connection->prepare("SELECT * FROM comments ORDER BY created_at DESC LIMIT :offset, :commentsPerPage");
// $stmt->bindValue(':offset', $offsetComments, PDO::PARAM_INT);
// $stmt->bindValue(':commentsPerPage', $commentsPerPage, PDO::PARAM_INT);
// $stmt->execute();
// $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mendapatkan total komentar untuk pagination
// $totalComments = $connection->query("SELECT COUNT(*) FROM comments")->fetchColumn();
// $totalPagesComments = ceil($totalComments / $commentsPerPage);
// PAGINATION

// SECTION COMMENT PAGINATION GUESTS
$commentsPerPage = 5;
$pageComments = isset($_GET['page_comments']) ? (int)$_GET['page_comments'] : 1;
$offsetComments = ($pageComments - 1) * $commentsPerPage;

// Mengambil data komentar dari database
$stmt = $connection->prepare("SELECT * FROM guests ORDER BY created_at DESC LIMIT :offset, :commentsPerPage");
$stmt->bindValue(':offset', $offsetComments, PDO::PARAM_INT);
$stmt->bindValue(':commentsPerPage', $commentsPerPage, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mendapatkan total komentar untuk pagination
$totalComments = $connection->query("SELECT COUNT(*) FROM guests")->fetchColumn();
$totalPagesComments = ceil($totalComments / $commentsPerPage);

// Menghapus komentar
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    // $deleteStmt = $connection->prepare("DELETE FROM guests WHERE id = :id");
    $deleteStmt = $connection->prepare("UPDATE guests SET notes = NULL WHERE id= :id");
    $deleteStmt->bindValue(':id', $deleteId, PDO::PARAM_INT);
    $deleteStmt->execute();
    header("Location: dashboard_modern.php");
    exit();
}
// END SECTION COMMENT PAGINATION

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="./assets/style/dashboard-addGuest.css">
    <link rel="stylesheet" href="./assets/style/dashboard-importFile.css">
    <link rel="stylesheet" href="./assets/style/comment.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img alt="Logo"
                    src="https://storage.googleapis.com/a1aa/image/wKCqZGXRdV7wGN6NZuLBqea0KQ02hWyuUwuJPg5z2s4qGy3JA.jpg"
                    width="40" height="40" />
                <span>Admin Dashboard</span>
            </div>
            <ul class="menu">
                <li><a class="active" href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-user-plus"></i> Add New Guest</a></li>
                <li><a href="#"><i class="fas fa-comments"></i> Comments</a></li>
            </ul>
            <button class="btn green" id="logout-button">Logout</button>
        </div>
        <div class="main-content">
            <div class="header">
                <div class="header-item">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search here..." />
                    </div>
                    <div class="profile">
                        <h3><?= htmlspecialchars($admin['name']) ?></h3>
                        <img src="assets/img/profile.png" alt="Profile Picture" width="40" height="40">
                    </div>
                </div>
            </div>
            <main class="main">
                <section class="main-dashboard">
                <div class="dashboard">
                    <div class="card AllGuests">
                        <i class="fas fa-user-friends"></i>
                        <h3>Total Tamu</h3>
                        <h2><?= $guestsCount ?></h2>
                    </div>
                    <div class="card presentGuest">
                        <i class="fas fa-thumbs-up"></i>
                        <h3>Total Tamu Hadir</h3>
                        <h2><?= $guestsPresentCount ?></h2>
                    </div>
                    <div class="card comments">
                        <i class="fas fa-comment"></i>
                        <h3>Comments</h3>
                        <h2><?= $totalComments ?></h2>
                    </div>
                </div>
                <div class="recent-activity">
                    <div class="topbar">
                        <div class="top">
                            <h3>Recent Activity</h3>
                            <form method="GET" action="">
                            <div class="search-data">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Search data..." onkeypress="if(event.keyCode === 13) { this.form.submit(); }"/>
                            </div>
                            </form>
                        </div>
                        <!-- Modal Import -->
                        <div id="modal-import" class="modal">
                            <div class="modal-content">
                                <span id="close-import-modal" style="cursor:pointer; float:right;">&times;</span>
                                <h2>Import File</h2>
                                <p id="import-message"></p>

                                <div class="import">
                                    <form method="POST" enctype="multipart/form-data" class="import-file">
                                        <input type="file" name="file" accept=".csv" required class="form input">
                                        <div class="import-wrapping">
                                            <button type="submit" name="import_guests" value="Import" class="btn" id="input-button">Ya, Proses Data</button>
                                            <button class="btn" id="cancel-button">Tidak, Batalkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Import -->
                        <div class="button">
                            <!-- Bagian form impor CSV -->
                                <button class="btn greenLight" id="button-import">Import</button>
                            <!-- Mengubah form untuk ekspor tamu -->
                            <?php if ($guestsCount > 0): ?>
                                <form method="POST" class="form">
                                    <button class="btn greenLight" type="submit" name="export_guests" value="Ekspor menjadi CSV">Export</button>
                                </form>
                            <?php else: ?>
                                <form method="#" class="form">
                                    <button class="btn greenLight" type="submit" name="export_guests" value="Ekspor menjadi CSV" disabled>Export</button>
                                </form>
                            <?php endif; ?>
                            <?php if($guestsCount > 0): ?>
                            <form method="POST" class="form">
                            <button class="btn greenLight" type="submit" name="delete_all_guests" value="Hapus Semua Tamu" onclick="return confirm('Apakah Anda yakin ingin menghapus semua tamu?');">
                                    Delete All</button>
                            </form>
                            <?php else: ?>
                            <form method="POST" class="form">
                            <button class="btn green" type="submit" name="delete_all_guests" value="Hapus Semua Tamu" onclick="return confirm('Apakah Anda yakin ingin menghapus semua tamu?');" disabled>
                            Delete All</button>
                            </form>
                            <?php endif?>
                        </div>
                    </div>
                    <?php if($guestsCount>0):?>
                    <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No Telpon</th>
                            <th>Jumlah Tamu</th>
                            <th>Kehadiran</th>
                            <th>Tanggal Ditambahkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($guests as $guest): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($guest['id']); ?></td>
                            <td><?php echo htmlspecialchars($guest['name']); ?></td>
                            <td><?php echo htmlspecialchars($guest['email']); ?></td>
                            <td><?php echo htmlspecialchars($guest['phone']); ?></td>
                            <td><?php echo htmlspecialchars($guest['guests_count']); ?></td>
                            <td><?php echo htmlspecialchars($guest['is_present'] == 0 ? "belum hadir" : "Hadir"); ?></td>
                            <td><?php echo htmlspecialchars($guest['created_at']); ?></td> <!-- Menampilkan tanggal/waktu -->
                            <td>
                                <a href="edit_guest.php?id=<?php echo $guest['id']; ?>" class="btn edit">Edit</a>
                                <a href="delete_guest.php?id=<?php echo $guest['id']; ?>" class="btn delete" onclick="return confirm('Apakah Anda yakin ingin menghapus tamu ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else:?>
                        <h1>No Data</h1>
                    <?php endif;?>
                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn prev">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="btn <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn next">Next</a>
                    <?php endif; ?>
                </div>
                <!-- Pagination -->
                </div>
                </section>
                <!-- SECTION ADD NEW GUESTS -->
                <section class="main-addNewGuests">
                    <div class="formulir-rsvp d-none">
                        <h1>Pendaftaran Tamu</h1>
                        <form id="registration-form" action="../Model/add_guests_admin.php" method="POST" onsubmit="confirmSubmission(event);">
                            <input type="text" name="name" placeholder="Nama Lengkap" required maxlength="50">
                            <input type="email" name="email" placeholder="Alamat Email" required maxlength="50">
                            <input type="text" name="phone" placeholder="Nomor Telepon" maxlength="15" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <input type="number" name="guests_count" placeholder="Jumlah Tamu yang Dibawa" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <textarea name="notes" placeholder="Ucapan Selamat"></textarea>
                            <button type="submit">Kirim</button>
                        </form>
                <!-- Modal Konfirmasi -->
                        <div id="confirmation-modal" class="modal">
                            <div class="modal-content">
                                <span id="close-modal" style="cursor:pointer; float:right;">&times;</span>
                                <h2>Konfirmasi Data</h2>
                                <p id="confirmation-message"></p>
                                <div class="confirm-wrapping">
                                    <button id="confirm-button">Ya, Proses Data</button>
                                    <button id="cancel-button">Tidak, Batalkan</button>
                                </div>
                            </div>
                        </div>
                <!-- Modal Konfirmasi -->
                    </div>
                 </section>
                <!-- SECTION ADD NEW GUESTS -->
                <!-- SECTION COMMENT -->
                <section id="comment" class="d-none">
                    <div class="comment-wrapping">
                        <h1>Komentar</h1>
                        <?php if($totalComments > 0 ): ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php if($comment['notes'] != null): ?>
                                <div class="comment">
                                    <span class="comment-name"><?php echo htmlspecialchars($comment['name']); ?></span>
                                    <div class="comment-wrap-item">
                                        <div class="text-time">
                                            <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['notes'])); ?></p>
                                            <p class="comment-time"><?php echo nl2br(htmlspecialchars($comment['created_at'])); ?></p>
                                        </div>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $comment['id']; ?>">
                                            <span class="delete-comment" onclick="this.closest('form').submit();">Hapus</span>
                                        </form>
                                    </div>
                                </div>
                            <? else: ?>
                            <?php endif;?>
                        <?php endforeach; ?>

                        <div class="pagination-comment">
                            <?php if ($pageComments > 1): ?>
                                <a href="?page_comments=<?php echo $pageComments - 1; ?>">Prev</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPagesComments; $i++): ?>
                                <a href="?page_comments=<?php echo $i; ?>" style="<?php echo $i === $pageComments ? 'font-weight: bold;' : ''; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>

                            <?php if ($pageComments < $totalPagesComments): ?>
                                <a href="?page_comments=<?php echo $pageComments + 1; ?>">Next</a>
                            <?php endif; ?>
                            <?php else: ?>
                            <h3>Belum ada Komentar tersedia.</h3>
                            <?php endif;?>
                        </div>
                    </div>
                </section>
                <!-- SECTION COMMENT -->
            </main>
            <!-- Modal Konfirmasi -->
            <div id="logout-modal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span id="close-logout-modal" style="cursor:pointer; float:right;">&times;</span>
                    <h2>Konfirmasi Logout</h2>
                    <p>Apakah Anda yakin ingin keluar?</p>
                    <div class="confirm-wrapping">
                        <button onclick="logout()">Ya, Keluar</button>
                        <button id="cancel-logout-button">Tidak, Kembali</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <script src="assets/javascript/dashboard-comment.js"></script> -->
    <script src="assets/javascript/dashboard.js"></script>
    <script src="assets/javascript/dashboard-modal-import.js"></script>
    <script src="assets/javascript/dashboard-addGuest.js"></script>
    <script src="assets/javascript/dashboard-router.js"></script>
    <script src="./assets/javascript/registration-view.js"></script>
</body>

</html>