<?php
if (!isset($_SESSION)) {
    session_start();
}
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$is_logged_in = isset($_SESSION['user_login']) && $_SESSION['user_login'] === true;

if (!$is_admin && !$is_logged_in) {
    $current_page = basename($_SERVER['PHP_SELF']);
    $public_pages = ['index.php', 'login.php', 'register.php'];
    if (!in_array($current_page, $public_pages) && $_SERVER['REQUEST_URI'] !== '/perpustakaan/') {
        $path_to_login = ($current_page !== 'index.php') ? '/perpustakaan/login/' : '/perpustakaan/login/';
        header("Location: $path_to_login");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Perpustakaan Umum'; ?> - Perpustakaan Umum</title>
    <link rel="stylesheet" href="/perpustakaan/assets/css/global.css">
    <?php if (isset($cssFile)): ?>
    <link rel="stylesheet" href="<?php echo $cssFile; ?>">
    <?php endif; ?>
</head>
<body>

    <header class="main-header">
        <div class="container">
            <h1><a href="/perpustakaan/">Perpustakaan Umum</a></h1>
            <nav>
                <?php if ($is_admin): ?>
                    <ul>
                        <li><a href="/perpustakaan/admin/manajemen-buku/">Manajemen Buku</a></li>
                        <li><a href="/perpustakaan/admin/manajemen-pengguna/">Manajemen Pengguna</a></li>
                        <li><a href="/perpustakaan/admin/manajemen-peminjaman/">Manajemen Pinjaman</a></li>
                        <li><a href="/perpustakaan/logout.php" class="btn btn-logout">Logout</a></li>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li><a href="/perpustakaan/koleksi-buku/">Koleksi Buku</a></li>
                        <?php if ($is_logged_in): ?>
                            <li><a href="/perpustakaan/history-peminjaman/">History</a></li>
                            <li class="user-nav">
                                
                                <span class="user-name">
                                    <a href="/perpustakaan/edit-profil/"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                                </span>
                                <a href="/perpustakaan/logout.php" class="btn btn-logout">Logout</a>
                            </li>
                        <?php else: ?>
                            <li><a href="/perpustakaan/login/">Login</a></li>
                            <li><a href="/perpustakaan/register/">Register</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
