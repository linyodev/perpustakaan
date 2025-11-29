<?php
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";
include('../templates/header.php');


$search_query = isset($_GET['search_query']) ? sanitizeInput($_GET['search_query']) : '';

try {
    $conn = get_db_connection();
    
    if (!empty($search_query)) {
        
        $sql = "SELECT * FROM buku WHERE judul LIKE :query OR penulis LIKE :query";
        $stmt = $conn->prepare($sql);
        $search_param = "%" . $search_query . "%";
        $stmt->bindParam(':query', $search_param, PDO::PARAM_STR);
    } else {
        
        $sql = "SELECT * FROM buku";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();   
    $buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    
    
    die("Error: Gagal mengambil data buku.");
}
?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<div class="search-bar">
    <form action="index.php" method="GET">
        <input type="search" name="search_query" placeholder="Cari berdasarkan judul atau penulis..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn">Cari</button>
    </form>
</div>

<div class="book-grid">
    <?php if (count($buku_list) > 0): ?>
        <?php foreach($buku_list as $b)  :?>
        <div class="book-card">
            <h3><?= htmlspecialchars($b['judul']) ?></h3>
            <p class="author"><?= htmlspecialchars($b['penulis'])?></p>
            <p class="status <?= ($b['jumlah'] > 0) ? 'tersedia' : 'tidak-tersedia' ?>">
                Status: <?= ($b['jumlah'] > 0) ? "Tersedia" : "Tidak Tersedia" ?> (<?= htmlspecialchars($b['jumlah']) ?>)
            </p>
                <a href="process.php?id=<?= $b['id_buku'] ?>" class="btn">Pinjam</a>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-results">Tidak ada buku yang cocok dengan kriteria pencarian Anda.</p>
    <?php endif; ?>
</div>

<?php include('../templates/footer.php'); ?>