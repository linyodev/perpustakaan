<?php
$pageTitle = "Register";
$cssFile = "/perpustakaan/assets/css/register.css";
include('../templates/header.php');
?>

<div class="form-container">
    <h2 class="page-title">Registrasi Anggota Baru</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Register</button>
        <p class="form-link">Sudah punya akun? <a href="/perpustakaan/login/">Login di sini</a></p>
    </form>
</div>

<?php include('../templates/footer.php'); ?>