<?php
$pageTitle = "Selamat Datang";
$cssFile = "/perpustakaan/assets/css/home.css"; 
include('templates/header.php'); 
?>

<div class="home-container">
    <div class="home-content">
        <h1>Selamat Datang di Perpustakaan Umum</h1>
        <p>Temukan dunia pengetahuan di ujung jari Anda. Jelajahi koleksi kami dan mulailah petualangan membaca Anda.</p>
        <div class="home-actions">
            <a href="/perpustakaan/koleksi-buku/" class="btn btn-primary">Lihat Koleksi Buku</a>
            <?php if (isset($_SESSION['user_login']) && $_SESSION['user_login'] === true): ?>
                <a href="/perpustakaan/history-peminjaman/" class="btn btn-secondary">Lihat History</a>
            <?php else: ?>
                <a href="/perpustakaan/login/" class="btn btn-secondary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>
