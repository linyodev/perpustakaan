<?php

/*
|--------------------------------------------------------------------------
| Skrip Penghapusan Buku
|--------------------------------------------------------------------------
|
| Skrip ini tidak menampilkan halaman apa pun. Tugasnya murni untuk
| memproses permintaan penghapusan buku dari halaman 'kelola_buku.php'.
| Skrip ini melakukan beberapa langkah penting demi keamanan dan
| kebersihan data.
|
*/

// Mulai session dan otorisasi admin.
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// --- Validasi ID dari URL ---
// Pastikan ada ID buku yang dikirim dan formatnya adalah angka.
if (!isset($_GET['id']) || !validasiNumerik($_GET['id'])) {
    $_SESSION['error_message'] = "Permintaan tidak valid: ID buku tidak ditemukan atau salah format.";
    header("Location: kelola_buku.php");
    exit();
}
$id_buku = $_GET['id'];

try {
    // Dapatkan koneksi database.
    $conn = get_db_connection();

    // --- Pengecekan Keamanan Sebelum Hapus ---
    // Kita tidak boleh menghapus buku yang sedang aktif dipinjam orang.
    // Query ini menghitung berapa banyak transaksi peminjaman untuk buku ini
    // yang statusnya BUKAN 'dikembalikan' atau 'ditolak'.
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_buku = :id_buku AND status NOT IN ('dikembalikan', 'ditolak')");
    $stmt_check->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt_check->execute();
    $peminjaman_aktif = $stmt_check->fetchColumn();

    // Jika hasilnya lebih dari 0, berarti buku ini tidak boleh dihapus.
    if ($peminjaman_aktif > 0) {
        $_SESSION['error_message'] = "Gagal menghapus: Buku ini sedang dalam proses peminjaman aktif dan tidak dapat dihapus.";
        header("Location: kelola_buku.php");
        exit();
    }

    // --- Proses Penghapusan ---
    // Jika lolos pengecekan di atas, kita lanjutkan ke proses hapus.

    // 1. Ambil nama file sampul dari database sebelum datanya dihapus.
    $stmt_get_sampul = $conn->prepare("SELECT sampul FROM buku WHERE id_buku = :id_buku");
    $stmt_get_sampul->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt_get_sampul->execute();
    $sampul = $stmt_get_sampul->fetchColumn();

    // 2. Hapus data buku dari tabel 'buku' di database.
    $stmt_delete = $conn->prepare("DELETE FROM buku WHERE id_buku = :id_buku");
    $stmt_delete->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    
    // Jika query hapus berhasil dieksekusi...
    if ($stmt_delete->execute()) {
        // 3. ...lanjutkan untuk menghapus file gambar sampulnya dari server.
        // Ini penting agar tidak ada file sampah yang menumpuk.
        if (!empty($sampul)) {
            $file_path = '../assets/uploads/' . $sampul;
            if (file_exists($file_path)) {
                unlink($file_path); // Fungsi PHP untuk menghapus file.
            }
        }
        // Kirim pesan sukses kembali ke halaman kelola buku.
        $_SESSION['success_message'] = "Buku berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus buku dari database.";
    }

} catch(PDOException $e) {
    // Jika terjadi error pada database di semua proses di atas.
    error_log("Gagal hapus buku: " . $e->getMessage());
    $_SESSION['error_message'] = "Terjadi kesalahan pada server saat mencoba menghapus buku.";
}

// Terakhir, selalu arahkan admin kembali ke halaman kelola buku.
header("Location: kelola_buku.php");
exit();
?>