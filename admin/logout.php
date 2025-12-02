<?php
/**
 * @file
 * Menangani proses logout khusus untuk administrator.
 *
 * Skrip ini akan menghancurkan semua data session dan
 * mengalihkan admin kembali ke halaman login admin.
 *
 * Catatan: Logika logout ini disederhanakan dari versi sebelumnya
 * untuk kejelasan dan konsistensi dengan logout pengguna utama.
 * Menghapus cookie secara manual dan memulai ulang session
 * untuk pesan logout tidak diperlukan.
 */

// Selalu mulai session untuk dapat mengakses dan menghancurkannya.
session_start();

// Menghapus semua variabel session.
session_unset();

// Menghancurkan session.
session_destroy();

// Alihkan admin ke halaman login mereka setelah logout.
header("Location: login.php");
exit();
?>
