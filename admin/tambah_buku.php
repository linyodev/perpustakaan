<?php

/*
|--------------------------------------------------------------------------
| Halaman Tambah Buku Baru
|--------------------------------------------------------------------------
|
| Halaman ini berisi formulir untuk admin menambahkan data buku baru
| ke dalam sistem, termasuk mengupload gambar sampul. Logika validasi
| dan penyimpanan data juga ada di dalam file ini.
|
*/

// Mulai session dan otorisasi admin.
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// Siapkan array untuk menampung pesan error.
$errors = [];

// --- Proses Hanya Jika Formulir Dikirim (Method POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari formulir, gunakan trim untuk menghapus spasi di awal/akhir.
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $jumlah = trim($_POST['jumlah'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $sampul = ""; // Nama file sampul akan diisi jika upload berhasil.

    // --- Tahap Validasi ---
    if (!validasiWajibDiisi($judul)) $errors[] = "Judul buku wajib diisi.";
    if (!validasiWajibDiisi($penulis)) $errors[] = "Nama penulis wajib diisi.";
    if (!validasiWajibDiisi($tahun_terbit)) $errors[] = "Tahun terbit wajib diisi.";
    if (!validasiTahun($tahun_terbit)) $errors[] = "Format tahun terbit tidak valid (contoh: 2024).";
    if (!validasiWajibDiisi($jumlah)) $errors[] = "Jumlah stok wajib diisi.";
    if (!validasiJumlahPositif($jumlah)) $errors[] = "Jumlah stok harus berupa angka (0 atau lebih).";

    // --- Validasi Upload File Sampul (jika ada) ---
    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['sampul'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // Batas ukuran file 2MB.

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Format sampul tidak valid. Gunakan JPG, PNG, atau WebP.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "Ukuran file sampul maksimal 2MB.";
        } else {
            // Jika file valid, buat nama unik untuk mencegah duplikasi nama file.
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $sampul = 'buku_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $upload_dir = '../assets/uploads/';
            // Buat direktori jika belum ada.
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            // Pindahkan file dari lokasi sementara ke direktori permanen.
            if (!move_uploaded_file($file['tmp_name'], $upload_dir . $sampul)) {
                $errors[] = "Gagal mengupload file sampul.";
                $sampul = ""; // Reset nama sampul jika gagal pindah.
            }
        }
    }

    // --- Proses ke Database Jika Tidak Ada Error Validasi ---
    if (empty($errors)) {
        try {
            $conn = get_db_connection();
            
            $sql = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, jumlah, deskripsi, sampul) 
                    VALUES (:judul, :penulis, :penerbit, :tahun_terbit, :jumlah, :deskripsi, :sampul)";
            
            $stmt = $conn->prepare($sql);
            
            // Binding parameter untuk keamanan (mencegah SQL Injection).
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':penulis', $penulis);
            $stmt->bindParam(':penerbit', $penerbit);
            $stmt->bindParam(':tahun_terbit', $tahun_terbit);
            $stmt->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
            $stmt->bindParam(':deskripsi', $deskripsi);
            $stmt->bindParam(':sampul', $sampul);
            
            $stmt->execute();
            
            // Jika berhasil, kirim pesan sukses dan arahkan kembali ke halaman utama manajemen buku.
            $_SESSION['success_message'] = "Buku '" . htmlspecialchars($judul) . "' berhasil ditambahkan.";
            header("Location: kelola_buku.php");
            exit();
            
        } catch(PDOException $e) {
            // Tangani jika ada error saat proses simpan ke database.
            $errors[] = "Gagal menyimpan data ke database: " . $e->getMessage();
            error_log("Gagal menambah buku: " . $e->getMessage());
        }
    }
}

// Siapkan variabel untuk template header.
$pageTitle = "Tambah Buku Baru";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_buku.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Kontainer untuk formulir tambah buku. -->
<div class="form-container-buku">
    <h2 class="page-title">Tambah Buku Baru</h2>

    <!-- Tampilkan daftar error validasi jika ada. -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- 
      Formulir ini menggunakan 'enctype="multipart/form-data"' yang WAJIB
      ada jika formulir memiliki input untuk upload file.
    -->
    <form action="tambah_buku.php" method="POST" enctype="multipart/form-data" class="data-form">
        <div class="form-group">
            <label for="judul">Judul Buku <span class="required">*</span></label>
            <!-- Tampilkan kembali data yang sudah diinput jika terjadi error -->
            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="penulis">Penulis <span class="required">*</span></label>
            <input type="text" id="penulis" name="penulis" value="<?php echo htmlspecialchars($_POST['penulis'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="penerbit">Penerbit</label>
            <input type="text" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($_POST['penerbit'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="tahun_terbit">Tahun Terbit <span class="required">*</span></label>
            <input type="text" id="tahun_terbit" name="tahun_terbit" value="<?php echo htmlspecialchars($_POST['tahun_terbit'] ?? ''); ?>" required placeholder="YYYY">
        </div>
        
        <div class="form-group">
            <label for="jumlah">Jumlah Stok <span class="required">*</span></label>
            <input type="number" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($_POST['jumlah'] ?? '1'); ?>" required min="0">
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="sampul">Sampul Buku</label>
            <input type="file" id="sampul" name="sampul" accept="image/jpeg,image/png,image/webp">
            <small class="form-hint">Format: JPG, PNG, WebP. Maksimal 2MB.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Simpan Buku</button>
            <a href="kelola_buku.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>