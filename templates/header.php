<?php
// Di aplikasi nyata, Anda akan memiliki logika untuk menentukan link mana yang akan ditampilkan
// berdasarkan status login (session). Untuk frontend ini, kita tampilkan semua sebagai contoh.
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Perpustakaan Umum</title>
    <link rel="stylesheet" href="/perpustakaan/assets/css/global.css">
    <link rel="stylesheet" href="<?php echo $cssFile; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <header class="main-header">
        <div class="container">
            <h1><a href="/perpustakaan/">Perpustakaan Umum</a></h1>
            <nav>
                <ul>
                    <?php if ($is_admin): ?>
                        <li><a href="/perpustakaan/admin/manajemen-buku/">Manajemen Buku</a></li>
                        <li><a href="/perpustakaan/admin/manajemen-pengguna/">Manajemen Pengguna</a></li>
                        <li><a href="/perpustakaan/admin/manajemen-peminjaman/">Manajemen Pinjaman</a></li>
                        <li><a href="/perpustakaan/login/">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/perpustakaan/koleksi-buku/">Koleksi Buku</a></li>
                        <li><a href="/perpustakaan/history-peminjaman/">History Peminjaman</a></li>
                        <li><a href="/perpustakaan/edit-profil/">Edit Profil</a></li>
                        <li><a href="/perpustakaan/login/">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">