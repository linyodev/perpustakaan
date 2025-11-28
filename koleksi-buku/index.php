<?php
$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";
include('../templates/header.php');

require_once('../includes/db_config.php');
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM buku");
    $stmt->execute();   
    $buku = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$buku) {
        header("Location: kelola_buku.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error: Gagal mengambil data");
}

?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<div class="search-bar">
    <input type="search" placeholder="Cari berdasarkan judul atau penulis...">
    <button class="btn">Cari</button>
</div>

<div class="book-grid">
    <?php foreach($buku as $b)  {?>
    <div class="book-card">
        <h3><?= $b['judul'] ?></h3>
        <p class="author"><?= $b['penulis']?></p>
        <p class="status <?= ($b['jumlah'] > 0) ? 'tersedia' : '' ?>">Status: <?= ($b['jumlah'] > 0) ? "Tersedia" : "Tidak Tersedia" ?></p>
        <a href="process.php?id=<?= $b['id_buku'] ?>" class="btn">Pinjam</a>
    </div>
    <?php } ?>
   
</div>

<?php include('../templates/footer.php'); ?>