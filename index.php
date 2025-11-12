<?php
$pageTitle = "Selamat Datang";
$cssFile = "/perpustakaan/assets/css/home.css"; 
include('templates/header.php'); 
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Selamat Datang di Perpustakaan Umum</h1>
        <p>Temukan dunia pengetahuan di ujung jari Anda. Jelajahi koleksi kami sekarang.</p>
        <a href="/perpustakaan/koleksi-buku/" class="btn btn-primary">Lihat Koleksi Buku</a>
        <a href="/perpustakaan/login/" class="btn btn-secondary">Login Anggota</a>
    </div>
</div>

<div class="features-section">
    <h2>Fitur Kami</h2>
    <div class="features-grid">
        <div class="feature-item">
            <h3>📖 Koleksi Lengkap</h3>
            <p>Ribuan buku dari berbagai genre siap untuk Anda pinjam.</p>
        </div>
        <div class="feature-item">
            <h3>💻 Peminjaman Online</h3>
            <p>Lihat status dan riwayat peminjaman buku Anda kapan saja.</p>
        </div>
        <div class="feature-item">
            <h3>👤 Manajemen Profil</h3>
            <p>Perbarui data diri dan informasi akun Anda dengan mudah.</p>
        </div>
    </div>
</div>

<?php 

include('templates/footer.php'); 
?>