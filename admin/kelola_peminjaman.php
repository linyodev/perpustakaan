<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once('../includes/db_config.php');
if (isset($_POST['update_status'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $status_baru = $_POST['status'];
    
    try {
        $conn = get_db_connection();
        $stmt = $conn->prepare("UPDATE peminjaman SET status = :status WHERE id_peminjaman = :id");
        $stmt->bindParam(':status', $status_baru, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['success_message'] = "Status peminjaman berhasil diupdate";
    } catch(PDOException $e) {
        $_SESSION['success_message'] = "Gagal mengupdate status";
    }
}
try {
    $conn = get_db_connection();
    $query = "SELECT p.*, pm.nama, b.judul 
              FROM peminjaman p 
              JOIN pemustaka pm ON p.id_pemustaka = pm.id_pemustaka 
              JOIN buku b ON p.id_buku = b.id_buku 
              ORDER BY p.id_peminjaman DESC";
    $stmt = $conn->query($query);
    $peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Gagal mengambil data peminjaman";
}
$admin_username = htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Kelola Peminjaman</h1>
            <div class="admin-info">
                <span><?php echo $admin_username; ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
        
        <nav class="admin-menu">
            <ul>
                <li><a href="kelola_buku.php">Kelola Buku</a></li>
                <li><a href="lihat_pemustaka.php">Lihat Pemustaka</a></li>
                <li><a href="kelola_peminjaman.php" class="active">Kelola Peminjaman</a></li>
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
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pemustaka</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($peminjaman_list)): ?>
                            <?php foreach($peminjaman_list as $pinjam): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pinjam['id_peminjaman'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pinjam['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pinjam['judul'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pinjam['tanggal_pinjam'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($pinjam['tanggal_kembali'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $pinjam['status']; ?>">
                                            <?php echo htmlspecialchars($pinjam['status'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="kelola_peminjaman.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id_peminjaman" value="<?php echo $pinjam['id_peminjaman']; ?>">
                                            <select name="status" class="select-status">
                                                <option value="dipinjam" <?php echo $pinjam['status'] == 'dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                                                <option value="dikembalikan" <?php echo $pinjam['status'] == 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-small">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data peminjaman</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
