<?php
$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";
include('../templates/header.php');
?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<div class="search-bar">
    <input type="search" placeholder="Cari berdasarkan judul atau penulis...">
    <button class="btn">Cari</button>
</div>

<div class="book-grid">
    <div class="book-card">
        <img src="https://via.placeholder.com/150x220.png?text=Cover+Buku" alt="Cover Buku">
        <h3>Laskar Pelangi</h3>
        <p class="author">Andrea Hirata</p>
        <p class="status tersedia">Status: Tersedia</p>
        <a href="#" class="btn">Pinjam</a>
    </div>

    <div class="book-card">
        <img src="https://via.placeholder.com/150x220.png?text=Cover+Buku" alt="Cover Buku">
        <h3>Bumi Manusia</h3>
        <p class="author">Pramoedya Ananta Toer</p>
        <p class="status dipinjam">Status: Dipinjam</p>
        <a href="#" class="btn" disabled>Pinjam</a>
    </div>

    <div class="book-card">
        <img src="https://via.placeholder.com/150x220.png?text=Cover+Buku" alt="Cover Buku">
        <h3>Negeri 5 Menara</h3>
        <p class="author">Ahmad Fuadi</p>
        <p class="status tersedia">Status: Tersedia</p>
        <a href="#" class="btn">Pinjam</a>
    </div>
</div>

<?php include('../templates/footer.php'); ?>