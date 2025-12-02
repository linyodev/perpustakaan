<?php

/*
|--------------------------------------------------------------------------
| Halaman Login Administrator
|--------------------------------------------------------------------------
|
| Halaman ini dikhususkan untuk login admin. Tampilannya sengaja dibuat
| terpisah dari template utama (tanpa header dan footer global) agar
| lebih sederhana dan terisolasi.
|
*/

// Mulai session untuk memeriksa status login atau menampilkan pesan error.
if (!isset($_SESSION)) {
    session_start();
}

// Jika admin ternyata sudah login, jangan tampilkan halaman ini.
// Langsung arahkan saja ke halaman dashboard admin.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Perpustakaan Umum</title>
    
    <!-- 
      Halaman ini sengaja tidak menggunakan header.php dan footer.php
      agar tampilannya bersih dan fokus hanya pada form login.
      Kita panggil stylesheet 'login.css' yang juga dipakai oleh form login
      pengguna biasa untuk menjaga konsistensi tampilan form.
    -->
    <link rel="stylesheet" href="../assets/css/global.css"> <!-- Dibutuhkan untuk .form-group, .btn, dll. -->
    <link rel="stylesheet" href="../assets/css/login.css"> 
    <style>
        /* 
          Sedikit penyesuaian style khusus untuk halaman ini agar
          form tidak terlalu lebar di layar besar.
        */
        body {
            background-color: #f0f2f5; /* Latar belakang sedikit berbeda untuk membedakan halaman admin */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            max-width: 400px;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Kontainer utama untuk formulir login. -->
    <div class="form-container">
        <h2>Login Administrator</h2>

        <?php
        // Cek dan tampilkan pesan error jika ada, lalu hapus dari session.
        if (isset($_SESSION['login_error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
            unset($_SESSION['login_error']);
        }
        ?>

        <!-- Formulir Login Admin -->
        <!-- Data dikirim ke 'login_process.php' untuk diverifikasi. -->
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Link untuk kembali ke halaman utama situs. -->
        <div class="form-link" style="margin-top: 20px;">
            <a href="/perpustakaan/">← Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>