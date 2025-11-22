<?php
$pageTitle = "Login";
$cssFile = "/perpustakaan/assets/css/login.css";
include('../templates/header.php');
?>

<div class="form-container">
    <h2 class="page-title">Register Anggota</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password2">Retype Password</label>
            <input type="password" id="password2" name="password2" required>
        </div>
        <button type="submit" class="btn">Register</button>
        <p class="form-link">Sudah punya akun? <a href="/perpustakaan/login/">Login di sini</a></p>
    </form>
</div>

<?php include('../templates/footer.php'); ?>