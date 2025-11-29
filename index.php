<?php
$pageTitle = "Selamat Datang di Perpustakaan Umum";
$cssFile = "/perpustakaan/assets/css/home.css"; 
include('templates/header.php'); 

require_once 'includes/db_config.php';

try {
    $conn = get_db_connection();
    
    $stmt = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC LIMIT 5");
    $featured_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    
    
    $featured_books = [];
}
?>

<div class="home-wrapper">
    <!-- Hero Section -->
    <section class="home-hero">
        <div class="hero-content">
            <h1>Temukan Dunia dalam Halaman</h1>
            <p>Jelajahi ribuan judul buku, dari fiksi hingga non-fiksi. Pengetahuan menanti Anda.</p>
            <a href="/perpustakaan/koleksi-buku/" class="btn btn-primary">Jelajahi Koleksi</a>
        </div>
    </section>


    <section class="home-cta">
        <div class="cta-content">
            <h2>Siap untuk Membaca?</h2>
            <p>Buat akun gratis untuk mulai meminjam buku dan menyimpan riwayat bacaan Anda.</p>
            <a href="/perpustakaan/register/" class="btn btn-secondary">Daftar Sekarang</a>
        </div>
    </section>
</div>

<?php include('templates/footer.php'); ?>