<?php

/*
|--------------------------------------------------------------------------
| Halaman Manajemen Peminjaman
|--------------------------------------------------------------------------
|
| Ini adalah halaman kompleks yang menangani dua hal:
| 1. Menampilkan semua data peminjaman buku dalam sebuah tabel.
| 2. Memproses perubahan status peminjaman yang dilakukan oleh admin.
|    Misalnya, mengubah status dari 'menunggu' menjadi 'dipinjam'.
|
| Perubahan status ini juga secara otomatis akan memperbarui
| jumlah stok buku yang tersedia.
|
*/

// Mulai session dan otorisasi admin.
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Panggil file konfigurasi database.
require_once('../includes/db_config.php');

// --- LOGIKA PEMROSESAN FORM (METHOD POST) ---
// Cek apakah ada form yang disubmit untuk mengupdate status.
if (isset($_POST['update_status'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $status_baru = $_POST['status'];
    $conn = get_db_connection();

    try {
        // Gunakan transaksi database. Ini sangat penting. Jika salah satu query
        // gagal (misal: gagal update stok), semua perubahan akan dibatalkan (rollback).
        // Ini menjaga data tetap konsisten.
        $conn->beginTransaction();

        // Ambil dulu status peminjaman saat ini dari database.
        $stmt = $conn->prepare("SELECT id_buku, status FROM peminjaman WHERE id_peminjaman = :id");
        $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
        $stmt->execute();
        $peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($peminjaman) {
            $id_buku = $peminjaman['id_buku'];
            $status_lama = $peminjaman['status'];

            // Hanya proses jika statusnya benar-benar berubah.
            if ($status_lama !== $status_baru) {
                // --- Logika Penyesuaian Stok Buku ---
                
                // Jika status diubah dari 'menunggu' menjadi 'dipinjam', maka stok buku berkurang 1.
                if ($status_lama == 'menunggu persetujuan' && $status_baru == 'dipinjam') {
                    $stmt_buku = $conn->prepare("UPDATE buku SET jumlah = jumlah - 1 WHERE id_buku = :id_buku AND jumlah > 0");
                    $stmt_buku->execute([':id_buku' => $id_buku]);
                }
                // Jika status diubah dari 'dipinjam' menjadi 'dikembalikan', maka stok buku bertambah 1.
                elseif ($status_lama == 'dipinjam' && $status_baru == 'dikembalikan') {
                    $stmt_buku = $conn->prepare("UPDATE buku SET jumlah = jumlah + 1 WHERE id_buku = :id_buku");
                    $stmt_buku->execute([':id_buku' => $id_buku]);
                }
                // Jika admin salah klik, misal dari 'dipinjam' diubah kembali jadi 'menunggu',
                // maka stok harus dikembalikan (bertambah 1).
                elseif ($status_lama == 'dipinjam' && ($status_baru == 'menunggu persetujuan' || $status_baru == 'ditolak')) {
                     $stmt_buku = $conn->prepare("UPDATE buku SET jumlah = jumlah + 1 WHERE id_buku = :id_buku");
                    $stmt_buku->execute([':id_buku' => $id_buku]);
                }

                // Setelah stok disesuaikan, update status peminjaman itu sendiri.
                $stmt_update = $conn->prepare("UPDATE peminjaman SET status = :status WHERE id_peminjaman = :id");
                $stmt_update->execute([':status' => $status_baru, ':id' => $id_peminjaman]);
                
                $_SESSION['success_message'] = "Status peminjaman untuk ID #$id_peminjaman berhasil diubah.";
            }
        } else {
            $_SESSION['error_message'] = "Peminjaman tidak ditemukan.";
        }

        // Jika semua query di atas berhasil, simpan perubahan secara permanen.
        $conn->commit();

    } catch(PDOException $e) {
        // Jika ada satu saja query yang gagal, batalkan semua perubahan.
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error_message'] = "Gagal mengupdate status: " . $e->getMessage();
        error_log("Gagal update peminjaman: " . $e->getMessage());
    }
    
    // Arahkan kembali ke halaman ini untuk mencegah form disubmit ulang jika di-refresh.
    header("Location: kelola_peminjaman.php");
    exit();
}

// --- LOGIKA PENGAMBILAN DATA (METHOD GET) ---
// Bagian ini dieksekusi saat halaman dimuat pertama kali.
$peminjaman_list = [];
$error = null;
try {
    $conn = get_db_connection();
    // Query ini mengambil semua data peminjaman dan menggabungkannya (JOIN) dengan
    // tabel pemustaka (untuk dapat nama) dan tabel buku (untuk dapat judul).
    $query = "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, p.status, 
                     pm.nama AS nama_pemustaka, b.judul AS judul_buku
              FROM peminjaman p 
              JOIN pemustaka pm ON p.id_pemustaka = pm.id_pemustaka 
              JOIN buku b ON p.id_buku = b.id_buku 
              ORDER BY p.tanggal_pinjam DESC, p.id_peminjaman DESC";
    $stmt = $conn->query($query);
    $peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Gagal mengambil data peminjaman: " . $e->getMessage();
    error_log($error);
}

// Siapkan variabel untuk template header.
$pageTitle = "Kelola Peminjaman";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_pinjaman.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Area konten utama untuk manajemen peminjaman. -->
<div class="content-area">
    <h2 class="page-title">Manajemen Peminjaman</h2>

    <!-- Tampilkan pesan sukses atau error dari session. -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Tabel data peminjaman. -->
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pemustaka</th>
                <th>Judul Buku</th>
                <th>Tgl. Pinjam</th>
                <th>Tgl. Kembali</th>
                <th>Status</th>
                <th width="25%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($peminjaman_list)): ?>
                <?php foreach($peminjaman_list as $pinjam): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pinjam['id_peminjaman']); ?></td>
                        <td><?php echo htmlspecialchars($pinjam['nama_pemustaka'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($pinjam['judul_buku'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(date("d-m-Y", strtotime($pinjam['tanggal_pinjam']))); ?></td>
                        <td><?php echo $pinjam['tanggal_kembali'] ? htmlspecialchars(date("d-m-Y", strtotime($pinjam['tanggal_kembali']))) : '-'; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo str_replace(' ', '-', strtolower($pinjam['status'])); ?>">
                                <?php echo htmlspecialchars($pinjam['status']); ?>
                            </span>
                        </td>
                        <td class="action-cell">
                            <!-- Mini-form di setiap baris untuk update status. -->
                            <form action="kelola_peminjaman.php" method="POST" class="form-status-update">
                                <input type="hidden" name="id_peminjaman" value="<?php echo $pinjam['id_peminjaman']; ?>">
                                <select name="status" class="select-status">
                                    <option value="menunggu persetujuan" <?php if($pinjam['status'] == 'menunggu persetujuan') echo 'selected'; ?>>Menunggu</option>
                                    <option value="dipinjam" <?php if($pinjam['status'] == 'dipinjam') echo 'selected'; ?>>Dipinjam</option>
                                    <option value="dikembalikan" <?php if($pinjam['status'] == 'dikembalikan') echo 'selected'; ?>>Dikembalikan</option>
                                    <option value="ditolak" <?php if($pinjam['status'] == 'ditolak') echo 'selected'; ?>>Ditolak</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-small">Update</button>
                            </form>
                            <!-- Tombol cetak invoice hanya muncul jika statusnya sudah 'dikembalikan'. -->
                            <?php if ($pinjam['status'] == 'dikembalikan'): ?>
                                <a href="../invoice/cetak_invoice.php?id=<?php echo $pinjam['id_peminjaman']; ?>" target="_blank" class="btn btn-small btn-invoice">Cetak Invoice</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada data peminjaman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>
