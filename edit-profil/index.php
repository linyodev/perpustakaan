<?php
session_start();
require_once('../includes/authorization.php');
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

$pageTitle = "Edit Profil";
$cssFile = "/perpustakaan/assets/css/edit_profil.css";

$errors = [];
$success_message = '';
$conn = get_db_connection();
$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama_lengkap = sanitizeInput($_POST['nama_lengkap']);
    $email = sanitizeInput($_POST['email']);
    $no_hp = sanitizeInput($_POST['no_hp']);
    $alamat = sanitizeInput($_POST['alamat']); 
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    
    if (!validasiWajibDiisi($nama_lengkap)) $errors[] = "Nama lengkap wajib diisi.";
    if (!validasiNama($nama_lengkap)) $errors[] = "Nama lengkap hanya boleh berisi huruf dan spasi.";

    if (!validasiWajibDiisi($email)) $errors[] = "Email wajib diisi.";
    elseif (!validasiEmail($email)) $errors[] = "Format email tidak valid.";

    if (!validasiWajibDiisi($no_hp)) $errors[] = "No HP wajib diisi.";
    elseif (!validasiTelepon($no_hp)) $errors[] = "Format No HP tidak valid (harus 10-13 digit angka).";
    
    if (!validasiWajibDiisi($alamat)) $errors[] = "Alamat wajib diisi.";
    elseif (!validasiAlamat($alamat)) $errors[] = "Alamat mengandung karakter yang tidak diizinkan.";


    $update_password = false;
    if (validasiWajibDiisi($new_password)) {
        $update_password = true;
        if (!validasiWajibDiisi($current_password)) {
            $errors[] = "Password saat ini wajib diisi untuk mengubah password.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM pemustaka WHERE id_pemustaka = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || hash('sha256', $current_password) !== $user['password']) {
                $errors[] = "Password saat ini salah.";
            }
        }

        if (!validasiPassword($new_password, 6)) {
            $errors[] = "Password baru minimal harus 6 karakter.";
        }
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE pemustaka SET nama = :nama, email = :email, no_hp = :no_hp, alamat = :alamat";
            if ($update_password) {
                $sql .= ", password = :password";
            }
            $sql .= " WHERE id_pemustaka = :id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nama', $nama_lengkap, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':no_hp', $no_hp, PDO::PARAM_STR);
            $stmt->bindParam(':alamat', $alamat, PDO::PARAM_STR); 
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

            if ($update_password) {
                $hashed_new_password = hash('sha256', $new_password);
                $stmt->bindParam(':password', $hashed_new_password, PDO::PARAM_STR);
            }

            $stmt->execute();

            $_SESSION['user_name'] = $nama_lengkap;
            $_SESSION['user_email'] = $email; 

            $success_message = "Profil berhasil diperbarui!";

        } catch (PDOException $e) {
            var_dump($e);
            $errors[] = "Gagal memperbarui profil. Terjadi kesalahan database.";
        }
    }
}

try {
    $stmt = $conn->prepare("SELECT nama, email, alamat, no_hp, alamat FROM pemustaka WHERE id_pemustaka = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        die("Data pengguna tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal mengambil data pengguna.");
}


include('../templates/header.php');
?>

<div class="form-container">
    <h2 class="page-title">Edit Profil Saya</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($user_data['nama']) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>">
        </div>
        <div class="form-group">
            <label for="no_hp">No HP</label>
            <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($user_data['no_hp'] ?? '') ?>">
        </div>
         <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat"><?= htmlspecialchars($user_data['alamat'] ?? '') ?></textarea>
        </div>
        <hr>
        <p><strong>Ubah Password (opsional)</strong></p>
        <div class="form-group">
            <label for="current_password">Password Saat Ini</label>
            <input type="password" id="current_password" name="current_password">
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru</label>
            <input type="password" id="new_password" name="new_password">
        </div>
        <button type="submit" class="btn">Simpan Perubahan</button>
    </form>
</div>

<?php include('../templates/footer.php'); ?>