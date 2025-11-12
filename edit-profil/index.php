<?php
$pageTitle = "Edit Profil";
$cssFile = "/perpustakaan/assets/css/edit_profil.css";
include('../templates/header.php');
?>

<div class="form-container">
    <h2 class="page-title">Edit Profil Saya</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="John Doe" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="johndoe@example.com" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="johndoe" disabled>
            <small>Username tidak dapat diubah.</small>
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