<?php

/*
|--------------------------------------------------------------------------
| Halaman Edit Data Buku
|--------------------------------------------------------------------------
|
| Halaman ini memiliki dua tugas utama:
| 1. Saat diakses pertama kali (method GET): Mengambil data buku dari
|    database berdasarkan ID yang ada di URL, lalu menampilkannya
|    di dalam formulir.
| 2. Saat formulir dikirim (method POST): Menerima data yang diubah,
|    memvalidasinya, dan memperbarui data tersebut di database.
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

// Siapkan variabel.
$errors = [];
$buku = null;

// Validasi ID buku dari URL. Pastikan ada dan berupa angka.
if (!isset($_GET['id']) || !validasiNumerik($_GET['id'])) {
    $_SESSION['error_message'] = "ID buku tidak valid.";
    header("Location: kelola_buku.php");
    exit();
}
$id_buku = $_GET['id'];

// --- Logika Utama: Cek Metode Request (POST atau GET) ---

// JIKA formulir disubmit (method POST)...
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil semua data dari formulir.
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $jumlah = trim($_POST['jumlah'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $sampul_lama = $_POST['sampul_lama'] ?? '';
    $sampul = $sampul_lama; // Secara default, kita asumsikan sampul tidak berubah.

    // Lakukan validasi data, mirip seperti pada halaman tambah buku.
    if (!validasiWajibDiisi($judul)) $errors[] = "Judul buku wajib diisi.";
    if (!validasiWajibDiisi($penulis)) $errors[] = "Nama penulis wajib diisi.";
    if (!validasiWajibDiisi($tahun_terbit)) $errors[] = "Tahun terbit wajib diisi.";
    if (!validasiTahun($tahun_terbit)) $errors[] = "Format tahun terbit tidak valid (YYYY).";
    if (!validasiWajibDiisi($jumlah)) $errors[] = "Jumlah stok wajib diisi.";
    if (!validasiJumlahPositif($jumlah)) $errors[] = "Jumlah stok harus angka positif.";

    // Proses upload sampul baru jika ada file yang diupload.
    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['sampul'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $errors[] = "Sampul tidak valid (Format: JPG/PNG/WebP, Maks: 2MB).";
        } else {
            // Buat nama unik dan pindahkan file baru.
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $sampul = 'buku_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $upload_dir = '../assets/uploads/';
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $sampul)) {
                // Jika upload berhasil, hapus file sampul yang lama agar tidak menumpuk.
                if (!empty($sampul_lama) && file_exists($upload_dir . $sampul_lama)) {
                    unlink($upload_dir . $sampul_lama);
                }
            } else {
                $errors[] = "Gagal mengupload sampul baru.";
                $sampul = $sampul_lama; // Jika gagal, kembalikan ke nama sampul lama.
            }
        }
    }

    // Jika tidak ada error sama sekali, update data di database.
    if (empty($errors)) {
        try {
            $conn = get_db_connection();
            $sql = "UPDATE buku SET judul = :judul, penulis = :penulis, penerbit = :penerbit, 
                    tahun_terbit = :tahun_terbit, jumlah = :jumlah, deskripsi = :deskripsi, sampul = :sampul 
                    WHERE id_buku = :id_buku";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':judul' => $judul,
                ':penulis' => $penulis,
                ':penerbit' => $penerbit,
                ':tahun_terbit' => $tahun_terbit,
                ':jumlah' => $jumlah,
                ':deskripsi' => $deskripsi,
                ':sampul' => $sampul,
                ':id_buku' => $id_buku
            ]);

            $_SESSION['success_message'] = "Data buku berhasil diperbarui.";
            header("Location: kelola_buku.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui data: " . $e->getMessage();
            error_log("Gagal update buku: " . $e->getMessage());
        }
    }
    
    // Jika ada error, isi kembali variabel $buku dengan data dari POST
    // agar form menampilkan data yang baru saja diinput (yang salah).
    $buku = $_POST;
    $buku['id_buku'] = $id_buku;

} else { // JIKA halaman diakses pertama kali (method GET)...
    
    try {
        // Ambil data buku dari database berdasarkan ID.
        $conn = get_db_connection();
        $stmt = $conn->prepare("SELECT * FROM buku WHERE id_buku = :id");
        $stmt->bindParam(':id', $id_buku, PDO::PARAM_INT);
        $stmt->execute();
        $buku = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika buku dengan ID tersebut tidak ditemukan, jangan lanjutkan.
        // Arahkan kembali ke halaman kelola buku.
        if (!$buku) {
            $_SESSION['error_message'] = "Buku dengan ID $id_buku tidak ditemukan.";
            header("Location: kelola_buku.php");
            exit();
        }
    } catch(PDOException $e) {
        die("Error: Gagal mengambil data buku. " . $e->getMessage());
    }
}

// Siapkan variabel untuk template header.
$pageTitle = "Edit Buku";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_buku.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Formulir untuk mengedit buku. -->
<div class="form-container-buku">
    <h2 class="page-title">Edit Data Buku: <?php echo htmlspecialchars($buku['judul'] ?? 'Buku'); ?></h2>

    <!-- Tampilkan pesan error validasi jika ada. -->
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

    <form action="edit_buku.php?id=<?php echo $id_buku; ?>" method="POST" enctype="multipart/form-data" class="data-form">
        
        <!-- Semua nilai (value) input diisi dengan data dari variabel $buku. -->
        <div class="form-group">
            <label for="judul">Judul Buku <span class="required">*</span></label>
            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul'] ?? ''); ?>" >
        </div>
        
        <div class="form-group">
            <label for="penulis">Penulis <span class="required">*</span></label>
            <input type="text" id="penulis" name="penulis" value="<?php echo htmlspecialchars($buku['penulis'] ?? ''); ?>" >
        </div>
        
        <div class="form-group">
            <label for="penerbit">Penerbit</label>
            <input type="text" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="tahun_terbit">Tahun Terbit <span class="required">*</span></label>
            <input type="text" id="tahun_terbit" name="tahun_terbit" value="<?php echo htmlspecialchars($buku['tahun_terbit'] ?? ''); ?>" >
        </div>
        
        <div class="form-group">
            <label for="jumlah">Jumlah Stok <span class="required">*</span></label>
            <input type="text" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($buku['jumlah'] ?? ''); ?>" >
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo htmlspecialchars($buku['deskripsi'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="sampul">Ganti Sampul Buku</label>
            <!-- Tampilkan pratinjau sampul saat ini. -->
            <?php if (!empty($buku['sampul'])): ?>
                <div class="current-cover">
                    <img src="../assets/uploads/<?php echo htmlspecialchars($buku['sampul']); ?>" alt="Sampul saat ini" style="max-width: 150px; margin-bottom: 10px; border-radius: 4px;">
                </div>
            <?php endif; ?>
            <input type="text" id="sampul" name="sampul" >
            <!-- Kirim nama sampul lama untuk proses penghapusan jika diganti. -->
            <input type="text" name="sampul_lama" value="<?php echo htmlspecialchars($buku['sampul'] ?? ''); ?>">
            <small class="form-hint">Kosongkan jika tidak ingin mengubah sampul.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Update Data</button>
            <a href="kelola_buku.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>