<?php

/*
|--------------------------------------------------------------------------
| Template Header Universal
|--------------------------------------------------------------------------
|
| File ini adalah bagian "kepala" dari semua halaman di situs. Isinya
| mencakup tag <head> (metadata, judul, CSS) dan navigasi utama.
| File ini juga punya peran penting dalam keamanan, yaitu memastikan
| hanya pengguna yang sudah login yang bisa mengakses halaman-halaman
| yang dilindungi.
|
*/

// Pastikan session sudah dimulai, karena kita akan banyak berurusan dengan
// variabel session di sini (misalnya, untuk cek status login).
if (!isset($_SESSION)) {
    session_start();
}


// --- Logika Navigasi dan Otorisasi ---

// Cek apakah kita sedang berada di area admin. Ini akan menentukan
// menu navigasi mana yang akan ditampilkan.
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;

// Cek apakah pengguna sudah login. Ini juga untuk menentukan tampilan menu.
$is_logged_in = isset($_SESSION['user_login']) && $_SESSION['user_login'] === true;


// --- Logika Pengalihan (Redirect) untuk Keamanan ---
// Logika ini sangat penting. Tujuannya adalah untuk "menendang" pengunjung
// yang belum login jika mereka mencoba mengakses halaman yang seharusnya tidak bisa.
if (!$is_admin && !$is_logged_in) {
    // Dapatkan nama file yang sedang diakses, contoh: 'index.php', 'koleksi-buku.php'
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Ini adalah daftar halaman yang boleh diakses oleh semua orang.
    $public_pages = ['index.php', 'login.php', 'register.php'];

    // Jika halaman yang sedang diakses TIDAK ADA di dalam daftar halaman publik
    // DAN halaman itu bukan halaman utama, maka...
    if (!in_array($current_page, $public_pages) && $_SERVER['REQUEST_URI'] !== '/perpustakaan/') {
        // ...paksa pengguna untuk pindah ke halaman login.
        header("Location: /perpustakaan/login/");
        exit(); // Hentikan eksekusi skrip setelah redirect.
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Judul halaman akan diambil dari variabel $pageTitle yang didefinisikan di setiap halaman. -->
    <!-- Jika tidak ada, judul default akan digunakan. -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Perpustakaan Umum'; ?> - Perpustakaan Umum</title>
    
    <!-- Memanggil stylesheet global yang berlaku untuk semua halaman. -->
    <link rel="stylesheet" href="/perpustakaan/assets/css/global.css">
    
    <!-- Jika halaman tertentu butuh style khusus, panggil file CSS-nya di sini. -->
    <?php if (isset($cssFile)): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssFile); ?>">
    <?php endif; ?>
</head>
<body>

    <!-- Bagian header yang berisi logo dan navigasi. -->
    <header class="main-header">
        <div class="container">
            <!-- Logo/Judul Situs yang bisa diklik untuk kembali ke halaman utama. -->
            <h1><a href="/perpustakaan/">Perpustakaan Umum</a></h1>
            
            <!-- Bagian navigasi utama. -->
            <nav>
                <?php if ($is_admin): ?>
                    <!-- Jika ini adalah halaman admin, tampilkan menu navigasi admin. -->
                    <ul>
                        <li><a href="/perpustakaan/admin/kelola_buku.php">Manajemen Buku</a></li>
                        <li><a href="/perpustakaan/admin/lihat_pemustaka.php">Manajemen Pengguna</a></li>
                        <li><a href="/perpustakaan/admin/kelola_peminjaman.php">Manajemen Pinjaman</a></li>
                        <li><a href="/perpustakaan/admin/logout.php" class="btn btn-logout">Logout</a></li>
                    </ul>
                <?php else: ?>
                    <!-- Jika bukan halaman admin, tampilkan menu untuk pengguna biasa. -->
                    <ul>
                        <li><a href="/perpustakaan/koleksi-buku/">Koleksi Buku</a></li>
                        <?php if ($is_logged_in): ?>
                            <!-- Menu tambahan jika pengguna sudah login. -->
                            <li><a href="/perpustakaan/history-peminjaman/">History</a></li>
                            <li class="user-nav">
                                <span class="user-name">
                                    <a href="/perpustakaan/edit-profil/"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                                </span>
                                <a href="/perpustakaan/logout.php" class="btn btn-logout">Logout</a>
                            </li>
                        <?php else: ?>
                            <!-- Menu untuk pengunjung yang belum login. -->
                            <li><a href="/perpustakaan/login/">Login</a></li>
                            <li><a href="/perpustakaan/register/">Register</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Tag <main> ini akan membungkus konten utama dari setiap halaman. -->
    <!-- Tag penutupnya ada di file footer.php. -->
    <main class="container">
