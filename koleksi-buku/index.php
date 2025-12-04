<?php

/*
|--------------------------------------------------------------------------
| Halaman Koleksi Buku
|--------------------------------------------------------------------------
|
| Ini adalah halaman "etalase" di mana pengguna dapat melihat semua
| buku yang tersedia di perpustakaan. Fitur pencarian dan filter
| berdasarkan kategori juga diimplementasikan di halaman ini.
|
*/

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// Siapkan variabel untuk template header.
$pageTitle = "Koleksi Buku";
$cssFile = "/perpustakaan/assets/css/koleksi_buku.css";

// Panggil template header. Header ini juga akan memastikan
// bahwa hanya pengguna yang sudah login yang bisa mengakses halaman ini.
include('../templates/header.php');

// --- Logika Pencarian dan Filter ---
// Ambil nilai pencarian atau filter dari URL (jika ada).
// Gunakan fungsi sanitizeInput untuk membersihkan input demi keamanan.
$search_query = isset($_GET['search_query']) ? sanitizeInput($_GET['search_query']) : '';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

try {
    // Dapatkan koneksi database.
    $conn = get_db_connection();

    // Ambil semua kategori unik dari database untuk ditampilkan sebagai tombol filter.
    $category_stmt = $conn->query("SELECT DISTINCT kategori FROM buku WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");
    $categories = $category_stmt->fetchAll(PDO::FETCH_COLUMN);

    // --- Bangun Query Dinamis ---
    // Mulai dengan query dasar.
    $sql = "SELECT id_buku, judul, penulis, sampul, jumlah FROM buku WHERE 1=1";
    $params = []; // Siapkan array untuk menampung parameter query.

    // Jika ada query pencarian, tambahkan kondisi 'LIKE' ke SQL.
    if (!empty($search_query)) {
        $sql .= " AND (judul LIKE :query OR penulis LIKE :query)";
        $params[':query'] = "%" . $search_query . "%";
    }

    // Jika ada filter kategori, tambahkan kondisi 'AND' ke SQL.
    if (!empty($category_filter)) {
        $sql .= " AND kategori = :category";
        $params[':category'] = $category_filter;
    }

    // Urutkan hasilnya berdasarkan judul.
    $sql .= " ORDER BY judul ASC";

    // Siapkan dan eksekusi query yang sudah dibangun secara dinamis.
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $buku_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika ada masalah dengan database.
    error_log("Gagal mengambil koleksi buku: " . $e->getMessage());
    die("Error: Gagal mengambil data buku. Silakan coba lagi nanti.");
}
?>

<h2 class="page-title">Daftar Koleksi Buku</h2>

<!-- Bagian untuk filter berdasarkan kategori. -->
<div class="filter-bar">
    <div class="categories">
        <span>Kategori:</span>
        <div class="con-category">
            <!-- Tombol "Semua" untuk menghapus filter. -->
            <a href="index.php" class="<?php echo empty($category_filter) ? 'active' : ''; ?>">Semua</a>
            <!-- Loop untuk membuat tombol filter untuk setiap kategori. -->
            <?php foreach ($categories as $category): ?>
                <a href="index.php?category=<?php echo urlencode($category); ?>" class="<?php echo ($category_filter === $category) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Bagian untuk pencarian. -->
<div class="search-bar">
    <form action="index.php" method="GET">
        <!-- 
          Jika sedang memfilter kategori, sertakan kategori itu dalam form
          sebagai input tersembunyi. Ini agar filter tidak hilang saat
          melakukan pencarian.
        -->
        <?php if (!empty($category_filter)): ?>
            <input type="text" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
        <?php endif; ?>
        <input type="text" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" class="btn">Cari</button>
    </form>
</div>

<!-- Tampilkan pesan feedback dari proses peminjaman (jika ada). -->
<?php if (isset($_SESSION['peminjaman_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['peminjaman_status']; ?>">
        <?php echo htmlspecialchars($_SESSION['peminjaman_message']); unset($_SESSION['peminjaman_message'], $_SESSION['peminjaman_status']); ?>
    </div>
<?php endif; ?>

<!-- Grid untuk menampilkan semua kartu buku. -->
<div class="book-grid">
    <?php if (count($buku_list) > 0): ?>
        <!-- Loop untuk setiap buku yang ditemukan. -->
        <?php foreach ($buku_list as $b) : ?>
            <div class="book-card">
                <div class="book-cover">
                    <?php if (!empty($b['sampul'])): ?>
                        <img src="../assets/uploads/<?php echo htmlspecialchars($b['sampul']); ?>" alt="Sampul buku <?php echo htmlspecialchars($b['judul']); ?>">
                    <?php else: ?>
                        <!-- Tampilkan gambar placeholder jika tidak ada sampul. -->
                        <img src="https://via.placeholder.com/150x220.png?text=No+Cover" alt="Tidak ada sampul">
                    <?php endif; ?>
                </div>
                <div class="book-info">
                    <h3><?php echo htmlspecialchars($b['judul']); ?></h3>
                    <p class="author">oleh <?php echo htmlspecialchars($b['penulis']); ?></p>
                    <p class="status <?php echo ($b['jumlah'] > 0) ? 'tersedia' : 'tidak-tersedia'; ?>">
                        Status: <strong><?php echo ($b['jumlah'] > 0) ? "Tersedia" : "Habis"; ?></strong> (Stok: <?php echo htmlspecialchars($b['jumlah']); ?>)
                    </p>
                    <!-- Tombol pinjam akan dinonaktifkan jika stok buku habis. -->
                    <a href="process.php?action=pinjam&id=<?php echo $b['id_buku']; ?>" 
                       class="btn btn-pinjam <?php if ($b['jumlah'] <= 0) echo 'disabled'; ?>"
                       <?php if ($b['jumlah'] <= 0) echo 'onclick="return false;"'; ?>>
                        Pinjam Buku
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Tampilkan pesan ini jika tidak ada buku yang cocok dengan filter atau pencarian. -->
        <p class="no-results">Tidak ada buku yang cocok dengan kriteria pencarian Anda.</p>
    <?php endif; ?>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>