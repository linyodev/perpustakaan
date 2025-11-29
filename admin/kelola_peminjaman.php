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
    $conn = get_db_connection();

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT id_buku, status FROM peminjaman WHERE id_peminjaman = :id");
        $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
        $stmt->execute();
        $peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($peminjaman) {
            $id_buku = $peminjaman['id_buku'];
            $status_lama = $peminjaman['status'];
            $update_query = "UPDATE peminjaman SET status = :status";

            if ($status_baru === 'dipinjam' && $status_lama === 'menunggu persetujuan') {
                $loan_period = 7;
                $tanggal_kembali = date('Y-m-d', strtotime("+$loan_period days"));
                $update_query .= ", tanggal_kembali = :tanggal_kembali";

                $stmt_buku = $conn->prepare("UPDATE buku SET jumlah = jumlah - 1 WHERE id_buku = :id_buku AND jumlah > 0");
                $stmt_buku->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
                $stmt_buku->execute();

            } elseif ($status_baru === 'dikembalikan' && $status_lama === 'dipinjam') {
                $tanggal_kembali = date('Y-m-d');
                $update_query .= ", tanggal_kembali = :tanggal_kembali";
                
                $stmt_buku = $conn->prepare("UPDATE buku SET jumlah = jumlah + 1 WHERE id_buku = :id_buku");
                $stmt_buku->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
                $stmt_buku->execute();
            }

            $update_query .= " WHERE id_peminjaman = :id";
            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':status', $status_baru, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
            if (($status_baru === 'dipinjam' && $status_lama === 'menunggu persetujuan') || ($status_baru === 'dikembalikan' && $status_lama === 'dipinjam')) {
                $stmt->bindParam(':tanggal_kembali', $tanggal_kembali, PDO::PARAM_STR);
            }
            $stmt->execute();

            $conn->commit();
            $_SESSION['success_message'] = "Status peminjaman berhasil diupdate";
        } else {
            $_SESSION['error_message'] = "Peminjaman tidak ditemukan.";
        }

    } catch(PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error_message'] = "Gagal mengupdate status: " . $e->getMessage();
    }
    header("Location: kelola_peminjaman.php");
    exit();
}
try {
    $conn = get_db_connection();
    $query = "SELECT p.*, pm.nama, b.judul 
              FROM peminjaman p 
              LEFT JOIN pemustaka pm ON p.id_pemustaka = pm.id_pemustaka 
              LEFT JOIN buku b ON p.id_buku = b.id_buku 
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
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message">
                        <?php 
                        echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['error_message']);
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
                                                                            <span class="status-badge status-<?php echo str_replace(' ', '-', $pinjam['status']); ?>">
                                                                                <?php echo htmlspecialchars($pinjam['status'], ENT_QUOTES, 'UTF-8'); ?>
                                                                            </span>
                                                                        </td>                                    <td>
                                        <form action="kelola_peminjaman.php" method="POST" style="display: inline-block; margin-bottom: 5px;">
                                            <input type="hidden" name="id_peminjaman" value="<?php echo $pinjam['id_peminjaman']; ?>">
                                            <select name="status" class="select-status">
                                                <option value="menunggu persetujuan" <?php echo $pinjam['status'] == 'menunggu persetujuan' ? 'selected' : ''; ?>>Menunggu Persetujuan</option>
                                                <option value="dipinjam" <?php echo $pinjam['status'] == 'dipinjam' ? 'selected' : ''; ?>>Disetujui (Dipinjam)</option>
                                                <option value="dikembalikan" <?php echo $pinjam['status'] == 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                                                <option value="ditolak" <?php echo $pinjam['status'] == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-small">Update</button>
                                        </form>
                                        <?php if ($pinjam['status'] == 'dikembalikan'): ?>
                                            <a href="../invoice/cetak_invoice.php?id=<?php echo $pinjam['id_peminjaman']; ?>" target="_blank" class="btn-small" style="background: #17a2b8;">Cetak Invoice</a>
                                        <?php endif; ?>
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
