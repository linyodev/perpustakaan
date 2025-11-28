<?php
session_start();
require_once('../includes/authorization.php');

$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";
include('../templates/header.php');

require_once('../includes/db_config.php');
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM buku");
    $stmt->execute();   
    $buku = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: Gagal mengambil data buku.");
}

?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<div class="search-bar">
    <input type="search" placeholder="Cari berdasarkan judul atau penulis...">
    <button class="btn">Cari</button>
</div>

<div class="book-grid">
    <?php foreach($buku as $b)  :?>
    <div class="book-card">
        <h3><?= htmlspecialchars($b['judul']) ?></h3>
        <p class="author"><?= htmlspecialchars($b['penulis'])?></p>
        <p class="status <?= ($b['jumlah'] > 0) ? 'tersedia' : 'tidak-tersedia' ?>">
            Status: <?= ($b['jumlah'] > 0) ? "Tersedia" : "Tidak Tersedia" ?> (<?= htmlspecialchars($b['jumlah']) ?>)
        </p>
            <a href="process.php?id=<?= $b['id_buku'] ?>" class="btn">Pinjam</a>
        
    </div>
    <?php endforeach; ?>
   
</div>

<?php include('../templates/footer.php'); ?>
