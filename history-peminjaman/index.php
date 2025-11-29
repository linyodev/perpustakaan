<?php
require_once '../includes/db_config.php';

$pageTitle = "History Peminjaman";
$cssFile = "/perpustakaan/assets/css/history_peminjaman.css";
include('../templates/header.php');

$conn = get_db_connection();
$id_pemustaka = $_SESSION['user_id'];


$sql = "SELECT b.judul, p.tanggal_pinjam, p.tanggal_kembali , p.status as status
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
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php foreach($result as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo date('d F Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo $row['tanggal_kembali'] ? date('d F Y', strtotime($row['tanggal_kembali'])) : 'Belum dikembalikan'; ?></td>
                    <td>
                        <span  class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">Anda belum memiliki riwayat peminjaman.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
include('../templates/footer.php'); 
?>
