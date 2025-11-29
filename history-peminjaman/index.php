<?php
require_once '../includes/db_config.php';

$pageTitle = "History Peminjaman";
$cssFile = "/perpustakaan/assets/css/history_peminjaman.css";
include('../templates/header.php');

$conn = get_db_connection();
$id_pemustaka = $_SESSION['user_id'];


$sql = "SELECT p.id_peminjaman, b.judul, p.tanggal_pinjam, p.tanggal_kembali, p.status 
        FROM peminjaman p 
        JOIN buku b ON p.id_buku = b.id_buku 
        WHERE p.id_pemustaka = :id
        ORDER BY p.tanggal_pinjam DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $id_pemustaka,PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="page-title">History Peminjaman Buku</h2>
<p>Berikut adalah daftar buku yang sedang Anda pinjam dan yang sudah dikembalikan.</p>

<table class="data-table">
    <thead>
        <tr>
            <th>Judul Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php foreach($result as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo date('d F Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo $row['tanggal_kembali'] ? date('d F Y', strtotime($row['tanggal_kembali'])) : 'Belum dikembalikan'; ?></td>
                    <td>
                        <span  class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'dikembalikan'): ?>
                            <a href="../invoice/cetak_invoice.php?id=<?php echo $row['id_peminjaman']; ?>" target="_blank" class="btn" style="background: #17a2b8; padding: 6px 12px; font-size: 12px;">Cetak Invoice</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Anda belum memiliki riwayat peminjaman.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
include('../templates/footer.php'); 
?>
