<?php

/*
|--------------------------------------------------------------------------
| Halaman Utama (Homepage)
|--------------------------------------------------------------------------
|
| Ini adalah halaman pertama yang dilihat oleh pengunjung. Halaman ini
| menampilkan sambutan hangat, beberapa buku unggulan untuk menarik
| perhatian, serta ajakan untuk mendaftar atau melihat koleksi buku.
|
*/

// Variabel-variabel ini akan digunakan di dalam file 'header.php'
// untuk mengatur judul tab browser dan file CSS spesifik untuk halaman ini.
$pageTitle = "Selamat Datang di Perpustakaan Umum";
$cssFile = "/perpustakaan/assets/css/home.css"; 

// Memanggil header.php untuk menampilkan bagian atas halaman (navigasi, dll).
include('templates/header.php'); 

// Memanggil file konfigurasi database agar kita bisa terhubung ke DB.
require_once 'includes/db_config.php';

// Siapkan sebuah array kosong untuk menampung data buku unggulan.
// Ini untuk memastikan variabelnya selalu ada, bahkan jika query gagal.
$featured_books = [];

try {
    // Coba dapatkan koneksi ke database.
    $conn = get_db_connection();
    
    // Jalankan query untuk mengambil 5 buku yang paling baru ditambahkan.
    $stmt = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC LIMIT 5");

    // Ambil hasilnya dan simpan ke dalam variabel yang sudah disiapkan.
    $featured_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Jika koneksi atau query gagal, proses akan lanjut tanpa menampilkan
    // buku unggulan. Kita bisa tambahkan pencatatan error di sini jika perlu.
    error_log("Gagal mengambil buku unggulan: " . $e->getMessage());
}
?>

<!-- 
  Ini adalah bagian utama dari konten halaman.
  Wrapper ini membantu mengatur layout agar tetap rapi.
-->
<div class="home-wrapper">

    <!-- Bagian "Hero" adalah spanduk besar di bagian atas halaman. -->
    <section class="home-hero">
        <div class="hero-content">
            <h1>Temukan Dunia dalam Halaman</h1>
            <p>Jelajahi ribuan judul buku, dari fiksi hingga non-fiksi. Pengetahuan menanti Anda.</p>
            <a href="/perpustakaan/koleksi-buku/" class="btn btn-primary">Jelajahi Koleksi</a>
        </div>
    </section>

    <!-- Bagian "Call to Action" (CTA) untuk mengajak pengguna mendaftar. -->
    <section class="home-cta">
        <div class="cta-content">
            <h2>Siap untuk Membaca?</h2>
            <p>Buat akun gratis untuk mulai meminjam buku dan menyimpan riwayat bacaan Anda.</p>
            <a href="/perpustakaan/register/" class="btn btn-secondary">Daftar Sekarang</a>
        </div>
    </section>

    <!-- 
      Di sini kita bisa tambahkan bagian untuk menampilkan buku-buku unggulan
      yang sudah kita ambil dari database tadi.
    -->

</div>

<?php 
// Memanggil footer.php untuk menampilkan bagian bawah halaman.
include('templates/footer.php'); 
?>
