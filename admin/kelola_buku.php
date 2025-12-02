<?php

/*
|--------------------------------------------------------------------------
| Halaman Manajemen Buku
|--------------------------------------------------------------------------
|
| Ini adalah halaman untuk admin melihat, menambah, mengedit, dan
| menghapus data buku yang ada di perpustakaan. Halaman ini menampilkan
| semua buku dalam format tabel.
|
*/

// Mulai session dan lakukan otorisasi admin.
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Panggil file konfigurasi database.
require_once('../includes/db_config.php');

// Siapkan variabel untuk menampung daftar buku dan pesan error.
$buku_list = [];
$error = null;

// --- Pengambilan Data Buku dari Database ---
try {
    // Dapatkan koneksi database.
    $conn = get_db_connection();
    // Buat query untuk mengambil semua data dari tabel 'buku', diurutkan dari yang paling baru.
    $stmt = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC");
    // Ambil semua hasilnya ke dalam variabel $buku_list.
    $buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Jika terjadi error saat mengambil data, simpan pesannya.
    $error = "Gagal mengambil data buku: " . $e->getMessage();
    // Catat error teknisnya di log server untuk investigasi.
    error_log($error);
}

// Siapkan variabel untuk template header.
$pageTitle = "Kelola Buku";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_buku.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Area konten utama untuk manajemen buku. -->
<div class="content-area">
    <h2 class="page-title">Manajemen Buku</h2>

    <!-- Tampilkan pesan sukses dari session (misalnya setelah berhasil menambah buku). -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']);
            // Hapus pesan agar tidak muncul lagi.
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Tampilkan pesan error jika pengambilan data dari database gagal. -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Tombol untuk menuju halaman tambah buku. -->
    <div class="action-bar">
        <a href="tambah_buku.php" class="btn">+ Tambah Buku Baru</a>
    </div>
    
    <!-- Tabel untuk menampilkan semua data buku. -->
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sampul</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($buku_list)): ?>
                <!-- Looping untuk setiap buku yang ada di variabel $buku_list. -->
                <?php foreach($buku_list as $buku): ?>
                    <tr>
                        <!-- Tampilkan setiap data buku di dalam sel tabel. -->
                        <td><?php echo htmlspecialchars($buku['id_buku']); ?></td>
                        <td>
                            <?php if (!empty($buku['sampul'])): ?>
                                <!-- Jika ada sampul, tampilkan gambarnya. -->
                                <img src="../assets/uploads/<?php echo htmlspecialchars($buku['sampul']); ?>" alt="Sampul <?php echo htmlspecialchars($buku['judul']); ?>" class="book-cover">
                            <?php else: ?>
                                <!-- Jika tidak, tampilkan gambar placeholder. -->
                                <img src="https://via.placeholder.com/50x70.png?text=N/A" alt="Tidak ada sampul" class="book-cover">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($buku['judul']); ?></td>
                        <td><?php echo htmlspecialchars($buku['penulis'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($buku['penerbit'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($buku['tahun_terbit'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($buku['jumlah']); ?></td>
                        <td class="action-links">
                            <!-- Link untuk mengedit dan menghapus buku, mengirimkan ID buku melalui URL. -->
                            <a href="edit_buku.php?id=<?php echo $buku['id_buku']; ?>" class="btn-edit">Edit</a>
                            <a href="hapus_buku.php?id=<?php echo $buku['id_buku']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Tampilkan pesan ini jika tidak ada buku sama sekali di database. -->
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada data buku yang ditambahkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>