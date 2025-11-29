<?php
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";
include('../templates/header.php');

$search_query = isset($_GET['search_query']) ? sanitizeInput($_GET['search_query']) : '';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

try {
    $conn = get_db_connection();


    $category_stmt = $conn->query("SELECT DISTINCT kategori FROM buku WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");
    $categories = $category_stmt->fetchAll(PDO::FETCH_COLUMN);

    $sql = "SELECT * FROM buku WHERE 1=1";
    $params = [];

    if (!empty($search_query)) {
        $sql .= " AND (judul LIKE :query OR penulis LIKE :query)";
        $params[':query'] = "%" . $search_query . "%";
    }

    if (!empty($category_filter)) {
        $sql .= " AND kategori = :category";
        $params[':category'] = $category_filter;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: Gagal mengambil data buku.");
}
?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<div class="filter-bar">
    <div class="categories">
        <span>Kategori</span>
        <div class="con-category">
            <a href="index.php" class="<?php echo empty($category_filter) ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $category): ?>
                <a href="index.php?category=<?php echo urlencode($category); ?>" class="<?php echo ($category_filter === $category) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="search-bar">
    <form action="index.php" method="GET">
        <?php if (!empty($category_filter)): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
        <?php endif; ?>
        <input type="search" name="search_query" placeholder="Cari berdasarkan judul atau penulis..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn">Cari</button>
    </form>
</div>

<div class="book-grid">
    <?php if (count($buku_list) > 0): ?>
        <?php foreach ($buku_list as $b) : ?>
            <div class="book-card">
                <div class="book-cover">
                    <?php if (!empty($b['sampul'])): ?>
                        <img src="../assets/uploads/<?php echo htmlspecialchars($b['sampul']); ?>" alt="Sampul buku">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/150x220.png?text=No+Cover" alt="Tidak ada sampul">
                <?php endif; ?>
            </div>
            <h3><?= htmlspecialchars($b['judul']) ?></h3>
            <p class=" author"><?= htmlspecialchars($b['penulis']) ?></p>
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