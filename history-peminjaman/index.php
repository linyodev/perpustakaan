<?php

/*
|--------------------------------------------------------------------------
| Halaman Riwayat Peminjaman Pengguna
|--------------------------------------------------------------------------
|
| Halaman ini menampilkan semua transaksi peminjaman yang pernah
| dilakukan oleh pengguna yang sedang login. Ini mencakup buku yang
| sedang dipinjam, permintaan yang sedang menunggu, dan buku yang
| sudah dikembalikan.
|
*/

// Panggil file-file yang dibutuhkan.
// Header akan memulai session dan memastikan pengguna sudah login.
require_once '../includes/db_config.php';
include('../templates/header.php');

// Siapkan variabel untuk template header.
$pageTitle = "History Peminjaman";
$cssFile = "/perpustakaan/assets/css/history_peminjaman.css";

// Siapkan variabel untuk menampung data dan pesan error.
$loan_history = [];
$error = null;

// Pastikan ID pengguna ada di session sebelum melanjutkan.
// Ini adalah lapisan keamanan kedua setelah yang ada di header.
if (!isset($_SESSION['user_id'])) {
    die("Sesi pengguna tidak valid. Silakan login kembali.");
}
$id_pemustaka = $_SESSION['user_id'];

// --- Ambil Data Riwayat dari Database ---
try {
    $conn = get_db_connection();
    
    // Query ini mengambil data dari tabel 'peminjaman' dan menggabungkannya
    // dengan tabel 'buku' untuk mendapatkan judul buku.
    // Hasilnya difilter berdasarkan ID pengguna yang sedang login.
    $sql = "SELECT p.id_peminjaman, b.judul, p.tanggal_pinjam, p.tanggal_kembali, p.status 
            FROM peminjaman p 
            JOIN buku b ON p.id_buku = b.id_buku 
            WHERE p.id_pemustaka = :id_pemustaka
            ORDER BY p.tanggal_pinjam DESC, p.id_peminjaman DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id_pemustaka", $id_pemustaka, PDO::PARAM_INT);
    $stmt->execute();
    $loan_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika terjadi masalah saat mengambil data.
    $error = "Gagal mengambil riwayat peminjaman: " . $e->getMessage();
    error_log($error);
}

?>

<h2 class="page-title">Riwayat Peminjaman Buku Anda</h2>
<p>Berikut adalah daftar buku yang sedang Anda pinjam dan yang sudah dikembalikan.</p>

<!-- Tampilkan pesan error jika ada. -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Tabel untuk menampilkan riwayat peminjaman. -->
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
        <?php if (!empty($loan_history)): ?>
            <!-- Lakukan looping untuk setiap baris data riwayat. -->
            <?php foreach($loan_history as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul'] ?? 'Buku Telah Dihapus'); ?></td>
                    <td><?php echo htmlspecialchars(date('d F Y', strtotime($row['tanggal_pinjam']))); ?></td>
                    <td><?php echo $row['tanggal_kembali'] ? htmlspecialchars(date('d F Y', strtotime($row['tanggal_kembali']))) : '<strong>-</strong>'; ?></td>
                    <td>
                        <!-- Badge status dengan class dinamis (misal: 'status-dipinjam') untuk pewarnaan. -->
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td class="action-cell">
                        <!-- Tombol cetak invoice hanya muncul jika buku sudah dikembalikan. -->
                        <?php if ($row['status'] == 'dikembalikan'): ?>
                            <a href="../invoice/cetak_invoice.php?id=<?php echo $row['id_peminjaman']; ?>" target="_blank" class="btn btn-invoice">Cetak Invoice</a>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Tampilkan pesan ini jika pengguna belum pernah meminjam buku. -->
            <tr>
                <td colspan="5" style="text-align: center;">Anda belum memiliki riwayat peminjaman.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>
