<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once('../includes/db_config.php');
try {
    $conn = get_db_connection();
    $stmt = $conn->query("SELECT * FROM pemustaka ORDER BY id_pemustaka DESC");
    $pemustaka_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Gagal mengambil data pemustaka";
}
$admin_username = htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pemustaka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Daftar Pemustaka</h1>
            <div class="admin-info">
                <span><?php echo $admin_username; ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
        
        <nav class="admin-menu">
            <ul>
                <li><a href="kelola_buku.php">Kelola Buku</a></li>
                <li><a href="lihat_pemustaka.php" class="active">Lihat Pemustaka</a></li>
                <li><a href="kelola_peminjaman.php">Kelola Peminjaman</a></li>
            </ul>
        </nav>
        
        <main>
            <div class="content-area">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>No HP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pemustaka_list)): ?>
                            <?php foreach($pemustaka_list as $pemustaka): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pemustaka['id_pemustaka'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pemustaka['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pemustaka['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pemustaka['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pemustaka['no_hp'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data pemustaka</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>