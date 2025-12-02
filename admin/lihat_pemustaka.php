<?php

/*
|--------------------------------------------------------------------------
| Halaman Manajemen Pengguna
|--------------------------------------------------------------------------
|
| Halaman ini digunakan oleh admin untuk melihat daftar semua pengguna
| (pemustaka) yang telah terdaftar di dalam sistem.
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

// Siapkan variabel untuk daftar pengguna dan pesan error.
$pemustaka_list = [];
$error = null;

// --- Pengambilan Data Pengguna ---
try {
    // Dapatkan koneksi database.
    $conn = get_db_connection();
    // Query untuk mengambil data yang relevan dari tabel 'pemustaka'.
    $stmt = $conn->query("SELECT id_pemustaka, nama, email, alamat, no_hp FROM pemustaka ORDER BY id_pemustaka DESC");
    $pemustaka_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Jika gagal, simpan dan catat pesannya.
    $error = "Gagal mengambil data pemustaka: " . $e->getMessage();
    error_log($error);
}

// Siapkan variabel untuk template header.
$pageTitle = "Manajemen Pengguna";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_pengguna.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Area konten utama untuk manajemen pengguna. -->
<div class="content-area">
    <h2 class="page-title">Daftar Pemustaka (Pengguna)</h2>

    <!-- Tampilkan pesan error jika terjadi. -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Tabel untuk menampilkan data pengguna. -->
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pemustaka_list)): ?>
                <!-- Lakukan looping untuk setiap pengguna yang ditemukan. -->
                <?php foreach($pemustaka_list as $pemustaka): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pemustaka['id_pemustaka']); ?></td>
                        <td><?php echo htmlspecialchars($pemustaka['nama']); ?></td>
                        <td><?php echo htmlspecialchars($pemustaka['email']); ?></td>
                        <!-- Gunakan '??' untuk memberikan nilai default jika data alamat/no_hp kosong (null). -->
                        <td><?php echo !empty($pemustaka['alamat']) ? htmlspecialchars($pemustaka['alamat']) : '<em>Tidak diisi</em>'; ?></td>
                        <td><?php echo !empty($pemustaka['no_hp']) ? htmlspecialchars($pemustaka['no_hp']) : '<em>Tidak diisi</em>'; ?></td>
                        <td class="action-links">
                            <!-- 
                              Saat ini tombol aksi dinonaktifkan karena fiturnya belum dibuat.
                              Ini adalah contoh bagaimana kita bisa menandai fitur masa depan.
                            -->
                            <a href="#" class="btn-edit-disabled" title="Fitur belum tersedia">Edit</a>
                            <a href="#" class="btn-delete-disabled" onclick="alert('Fitur hapus pengguna belum diimplementasikan.'); return false;" title="Fitur belum tersedia">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Tampilkan pesan ini jika tabel pemustaka kosong. -->
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada pemustaka yang terdaftar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 
  Style sementara untuk tombol yang non-aktif.
  Ini bisa dipindahkan ke file CSS utama.
-->
<style>
.btn-edit-disabled, .btn-delete-disabled {
    background-color: #ccc;
    color: #666;
    cursor: not-allowed;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9em;
    display: inline-block;
}
</style>


<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>