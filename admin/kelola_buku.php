<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once('../includes/db_config.php');
try {
    $conn = get_db_connection();
    $stmt = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC");
    $buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Gagal mengambil data buku";
}
$admin_username = htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Kelola Buku</h1>
            <div class="admin-info">
                <span><?php echo $admin_username; ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
        
        <nav class="admin-menu">
            <ul>
                <li><a href="kelola_buku.php" class="active">Kelola Buku</a></li>
                <li><a href="lihat_pemustaka.php">Lihat Pemustaka</a></li>
                <li><a href="kelola_peminjaman.php">Kelola Peminjaman</a></li>
            </ul>
        </nav>
        
        <main>
            <div class="content-area">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message">
                        <?php 
                        echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="action-bar">
                    <a href="tambah_buku.php" class="btn-primary">+ Tambah Buku Baru</a>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($buku_list)): ?>
                            <?php foreach($buku_list as $buku): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($buku['id_buku'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['penulis'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['penerbit'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['tahun'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['jumlah'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="action-cell">
                                        <a href="edit_buku.php?id=<?php echo $buku['id_buku']; ?>" class="btn-edit">Edit</a>
                                        <a href="hapus_buku.php?id=<?php echo $buku['id_buku']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data buku</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>