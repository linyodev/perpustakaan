<?php

/*
|--------------------------------------------------------------------------
| Halaman Login Pengguna
|--------------------------------------------------------------------------
|
| Halaman ini menampilkan formulir untuk pengguna (pemustaka) agar
| bisa masuk ke dalam sistem. Halaman ini juga akan menampilkan pesan
| jika terjadi kesalahan login (misal: password salah) atau pesan sukses
| setelah pengguna berhasil mendaftar.
|
*/

// Mulai session agar bisa menerima atau menampilkan pesan.
if (!isset($_SESSION)) {
    session_start();
}

// Siapkan variabel untuk template header.
$pageTitle = "Login";
$cssFile = "/perpustakaan/assets/css/login.css";

// Panggil template header.
include('../templates/header.php');
?>

<!-- Kontainer utama untuk formulir login. -->
<div class="form-container">
    <h2 class="page-title">Login Pemustaka</h2>

    <?php
    // --- Tampilkan Pesan Feedback dari Session ---

    // Cek apakah ada pesan error dari proses login sebelumnya.
    if (isset($_SESSION['login_error'])) {
        // Tampilkan pesan error di dalam kotak peringatan.
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
        
        // Hapus pesan dari session agar tidak muncul lagi saat halaman di-refresh.
        unset($_SESSION['login_error']);
    }
    
    // Cek apakah ada pesan sukses (biasanya dari halaman registrasi).
    if (isset($_SESSION['success_message'])) {
        // Tampilkan pesan sukses.
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        // Hapus juga pesan ini setelah ditampilkan.
        unset($_SESSION['success_message']);
    }
    ?>

    <!-- Formulir Login -->
    <!-- Data akan dikirim ke 'process.php' dengan metode POST. -->
    <form action="process.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" >
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="text" id="password" name="password" >
        </div>
        
        <button type="submit" class="btn">Login</button>
        
        <!-- Link bantuan untuk pengguna -->
        <p class="form-link">Belum punya akun? <a href="/perpustakaan/register/">Register di sini</a></p>
        <p class="form-link">Masuk sebagai Admin? <a href="/perpustakaan/admin/login.php">Login Admin</a></p>
    </form>
</div>

<?php 
// Panggil template footer.
include('../templates/footer.php'); 
?>