<?php

/*
|--------------------------------------------------------------------------
| Halaman Status Generik
|--------------------------------------------------------------------------
|
| Halaman ini berfungsi untuk menampilkan pesan status sederhana,
| baik itu pesan sukses maupun error.
|
| CATATAN PENTING:
| Halaman ini sudah TIDAK LAGI DIGUNAKAN (usang/obsolete) dalam alur
| peminjaman buku. Logika baru di `process.php` langsung menampilkan
| pesan di halaman koleksi buku. File ini hanya disimpan untuk
| referensi atau jika ada bagian lama sistem yang masih merujuk ke sini.
|
*/

// Mulai session dan otorisasi.
session_start();
require_once('../includes/authorization.php');

// Ambil detail pesan dari parameter URL.
$type = $_GET['type'] ?? 'error';
$title = $_GET['title'] ?? 'Terjadi Kesalahan';
$message = $_GET['message'] ?? 'Tidak ada pesan yang diberikan.';

// Siapkan variabel untuk template header.
$pageTitle = htmlspecialchars($title);
$cssFile = "/perpustakaan/assets/css/status_page.css";

// Panggil template header.
include('../templates/header.php');

// Tentukan kelas CSS untuk kotak pesan berdasarkan tipe (sukses atau error).
$boxClass = ($type === 'success') ? 'success' : 'error';
?>

<!-- Kontainer untuk menampilkan kotak status di tengah halaman. -->
<div class="status-container">
    <div class="status-box <?php echo $boxClass; ?>">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php" class="btn">Kembali ke Koleksi Buku</a>
    </div>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>
