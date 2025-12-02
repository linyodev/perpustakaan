<?php
/**
 * @file
 * Menangani proses logout untuk semua pengguna.
 *
 * Skrip ini akan menghancurkan semua data session yang ada
 * dan mengalihkan pengguna kembali ke halaman utama.
 */

// Selalu mulai session untuk dapat mengakses dan menghancurkannya.
session_start();

// session_unset() menghapus semua variabel session.
session_unset();

// session_destroy() menghancurkan session itu sendiri.
session_destroy();

// Alihkan pengguna ke halaman utama setelah logout.
header("Location: /perpustakaan/");
exit();
?>