<?php
/**
 * @file
 * Skrip otorisasi untuk halaman yang memerlukan login pengguna.
 *
 * File ini memastikan bahwa pengguna telah login sebelum mengakses halaman.
 * Jika tidak ada sesi login yang aktif, pengguna akan dialihkan ke
 * halaman login.
 *
 * Cara penggunaan:
 * require_once 'includes/authorization.php';
 * di bagian paling atas dari setiap file PHP yang ingin dilindungi.
 *
 * Catatan: Logika serupa sudah ada di templates/header.php, membuat
 * skrip ini agak redundan untuk halaman yang menggunakan header tersebut.
 * Namun, ini berguna untuk endpoint pemrosesan (seperti process.php)
 * yang tidak menampilkan HTML tetapi memerlukan otorisasi.
 */

// Memulai session jika belum aktif.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Menentukan URL dasar aplikasi jika belum di-set.
if (!isset($_ENV['APP_URL'])) {
    $_ENV['APP_URL'] = "/perpustakaan";
}

// Cek apakah variabel session 'user_login' ada dan bernilai true.
if (!isset($_SESSION['user_login']) || $_SESSION['user_login'] !== true) {
    // Jika tidak, alihkan ke halaman login.
    header("Location: " . $_ENV['APP_URL'] . "/login/");
    exit();
}
