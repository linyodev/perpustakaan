<?php

/*
|--------------------------------------------------------------------------
| Halaman Edit Profil Pengguna
|--------------------------------------------------------------------------
|
| Halaman ini memungkinkan pengguna yang sedang login untuk mengubah
| detail informasi pribadi mereka, seperti nama, email, dan alamat.
| Pengguna juga bisa mengubah password mereka di halaman ini.
|
*/

// Mulai session dan pastikan pengguna sudah login.
session_start();
require_once('../includes/authorization.php');

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// Siapkan variabel-variabel awal.
$pageTitle = "Edit Profil";
$cssFile = "/perpustakaan/assets/css/edit_profil.css";
$errors = [];
$success_message = '';
$user_id = $_SESSION['user_id'];

// Dapatkan koneksi database.
$conn = get_db_connection();

// --- Logika Pemrosesan Form (jika form disubmit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil semua data dari form.
    $nama = sanitizeInput($_POST['nama']);
    $email = sanitizeInput($_POST['email']);
    $no_hp = sanitizeInput($_POST['no_hp'] ?? null);
    $alamat = sanitizeInput($_POST['alamat'] ?? null);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Lakukan validasi pada data profil.
    if (!validasiWajibDiisi($nama)) $errors[] = "Nama lengkap wajib diisi.";
    if (!validasiEmail($email)) $errors[] = "Format email tidak valid.";
    if (!empty($no_hp) && !validasiTelepon($no_hp)) $errors[] = "Format No. HP tidak valid (harus 10-13 digit angka).";

    // --- Logika Validasi Perubahan Password ---
    // Bagian ini hanya berjalan jika pengguna mengisi field 'Password Baru'.
    $update_password = false;
    if (!empty($new_password)) {
        $update_password = true;
        // Jika mau ganti password, password lama harus diisi.
        if (empty($current_password)) {
            $errors[] = "Password saat ini wajib diisi untuk mengubah password.";
        } else {
            // Verifikasi kebenaran password lama.
            $stmt = $conn->prepare("SELECT password FROM pemustaka WHERE id_pemustaka = :id");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            /* == PERINGATAN KEAMANAN ==
             * Verifikasi password ini tidak aman. Seharusnya menggunakan:
             * if (password_verify($current_password, $user['password'])) { ... }
             */
            if (!$user || hash('sha256', $current_password) !== $user['password']) {
                $errors[] = "Password saat ini salah.";
            }
        }

        // Validasi kekuatan password baru.
        if (!validasiPanjangMin($new_password, 8)) {
            $errors[] = "Password baru minimal harus 8 karakter.";
        }
    }

    // --- Proses Update ke Database ---
    // Hanya berjalan jika tidak ada error validasi sama sekali.
    if (empty($errors)) {
        try {
            // Bangun query SQL dasar.
            $sql = "UPDATE pemustaka SET nama = :nama, email = :email, no_hp = :no_hp, alamat = :alamat";
            $params = [
                ':nama' => $nama,
                ':email' => $email,
                ':no_hp' => $no_hp,
                ':alamat' => $alamat,
                ':id' => $user_id
            ];

            // Jika pengguna ingin update password, tambahkan bagian password ke query.
            if ($update_password) {
                $sql .= ", password = :password";
                /* == PERINGATAN KEAMANAN ==
                 * Hashing password ini tidak aman. Seharusnya menggunakan:
                 * $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                 */
                $params[':password'] = hash('sha256', $new_password);
            }
            $sql .= " WHERE id_pemustaka = :id";

            // Eksekusi query dengan parameter yang sudah disiapkan.
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // Update nama dan email di session agar langsung berubah di header.
            $_SESSION['user_name'] = $nama;
            $_SESSION['user_email'] = $email; 

            $success_message = "Profil berhasil diperbarui!";

        } catch (PDOException $e) {
            // Tangani error jika email yang baru sudah digunakan orang lain.
            if ($e->errorInfo[1] == 1062) { // Kode error SQL untuk 'duplicate entry'.
                $errors[] = "Email '$email' sudah digunakan oleh pengguna lain.";
            } else {
                $errors[] = "Gagal memperbarui profil. Terjadi kesalahan database.";
                error_log("Gagal update profil: " . $e->getMessage());
            }
        }
    }
}

// --- Pengambilan Data untuk Ditampilkan di Form ---
// Blok ini akan selalu berjalan, baik saat halaman pertama kali dibuka,
// maupun setelah form disubmit (untuk menampilkan data terbaru).
try {
    $stmt = $conn->prepare("SELECT nama, email, alamat, no_hp FROM pemustaka WHERE id_pemustaka = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        die("Data pengguna tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal mengambil data pengguna: " . $e->getMessage());
}

// Jika terjadi error saat submit, timpa data dari database dengan data yang
// baru saja diinput pengguna. Ini agar pengguna tidak perlu mengetik ulang.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)) {
    $user_data['nama'] = $_POST['nama'] ?? $user_data['nama'];
    $user_data['email'] = $_POST['email'] ?? $user_data['email'];
    $user_data['no_hp'] = $_POST['no_hp'] ?? $user_data['no_hp'];
    $user_data['alamat'] = $_POST['alamat'] ?? $user_data['alamat'];
}

// Panggil template header.
include('../templates/header.php');
?>

<!-- Formulir Edit Profil -->
<div class="form-container">
    <h2 class="page-title">Edit Profil Saya</h2>

    <!-- Tampilkan pesan sukses atau error. -->
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <!-- Bagian Informasi Pribadi -->
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user_data['nama']); ?>" >
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" >
        </div>
        <div class="form-group">
            <label for="no_hp">No. HP</label>
            <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($user_data['no_hp'] ?? ''); ?>" >
        </div>
         <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
        </div>
        
        <hr>

        <!-- Bagian Ubah Password -->
        <p class="section-title"><strong>Ubah Password (opsional)</strong></p>
        <div class="form-group">
            <label for="current_password">Password Saat Ini</label>
            <input type="text" id="current_password" name="current_password" >
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru</label>
            <input type="text" id="new_password" name="new_password" >
        </div>
        
        <button type="submit" class="btn">Simpan Perubahan</button>
    </form>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>