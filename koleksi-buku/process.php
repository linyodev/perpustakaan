<?php

/*
|--------------------------------------------------------------------------
| Skrip Pemrosesan Peminjaman Buku
|--------------------------------------------------------------------------
|
| Skrip ini tidak menampilkan halaman, tetapi bertindak sebagai "endpoint"
| yang dipanggil ketika pengguna menekan tombol "Pinjam Buku".
| Tugasnya adalah membuat catatan permintaan peminjaman di database
| dengan status awal 'menunggu persetujuan'.
|
*/

// Panggil file otorisasi. Ini akan memastikan hanya pengguna yang sudah
// login yang bisa menjalankan skrip ini.
require_once '../includes/authorization.php';
// Panggil file konfigurasi database.
require_once '../includes/db_config.php';

// Mulai session agar kita bisa mengirim pesan feedback ke halaman koleksi buku.
session_start();

// --- Validasi Request ---
// Pastikan skrip ini diakses dengan metode GET dan ada parameter 'id' buku di URL.
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Validasi ID buku harus berupa angka.
$id_buku = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_buku === false) {
    // Jika ID tidak valid, kirim pesan error dan kembali ke halaman koleksi.
    $_SESSION['peminjaman_message'] = 'ID buku yang dimasukkan tidak valid.';
    $_SESSION['peminjaman_status'] = 'danger'; // 'danger' akan menghasilkan alert merah.
    header("Location: index.php");
    exit();
}

// Ambil ID pengguna yang sedang login dari session.
$id_pemustaka = $_SESSION['user_id'];

// --- Proses Utama Peminjaman ---
try {
    $conn = get_db_connection();

    // Mulai transaksi. Ini penting untuk mencegah kondisi balapan (race condition)
    // dan menjaga integritas data.
    $conn->beginTransaction();

    // 1. Cek ketersediaan buku.
    // 'FOR UPDATE' mengunci baris data ini selama transaksi, mencegah pengguna lain
    // meminjam buku yang sama di waktu yang persis sama jika stoknya tinggal satu.
    $stmt_check = $conn->prepare("SELECT judul, jumlah FROM buku WHERE id_buku = :id_buku FOR UPDATE");
    $stmt_check->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt_check->execute();
    $buku = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // Jika buku tidak ditemukan atau stoknya 0 atau kurang, batalkan proses.
    if (!$buku || $buku['jumlah'] <= 0) {
        $conn->rollBack(); // Batalkan transaksi.
        $judul_buku = $buku ? htmlspecialchars($buku['judul']) : 'yang Anda pilih';
        $_SESSION['peminjaman_message'] = "Maaf, stok buku '$judul_buku' telah habis atau buku tidak ditemukan.";
        $_SESSION['peminjaman_status'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    // 2. Cek apakah pengguna sudah punya permintaan aktif untuk buku yang sama.
    // Ini mencegah pengguna melakukan spam permintaan untuk buku yang sama.
    $stmt_self_check = $conn->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_buku = :id_buku AND id_pemustaka = :id_pemustaka AND status != 'dikembalikan'");
    $stmt_self_check->execute([':id_buku' => $id_buku, ':id_pemustaka' => $id_pemustaka]);
    if ($stmt_self_check->fetchColumn() > 0) {
        $conn->rollBack(); // Batalkan transaksi.
        $_SESSION['peminjaman_message'] = "Anda sudah memiliki permintaan aktif atau sedang meminjam buku ini.";
        $_SESSION['peminjaman_status'] = 'warning'; // 'warning' untuk alert kuning.
        header("Location: index.php");
        exit();
    }

    // --- Jika semua pengecekan lolos, masukkan data peminjaman baru ---
    $tanggal_pinjam = date('Y-m-d');
    $status = 'menunggu persetujuan'; // Ini adalah status awal setiap peminjaman.

    $stmt_insert = $conn->prepare(
        "INSERT INTO peminjaman (id_buku, id_pemustaka, tanggal_pinjam, status) 
         VALUES (:id_buku, :id_pemustaka, :tanggal_pinjam, :status)"
    );
    $stmt_insert->execute([
        ':id_buku' => $id_buku,
        ':id_pemustaka' => $id_pemustaka,
        ':tanggal_pinjam' => $tanggal_pinjam,
        ':status' => $status
    ]);

    // Jika semua query berhasil, simpan perubahan ke database.
    $conn->commit();
    
    // Siapkan pesan sukses untuk ditampilkan.
    $judul_buku = htmlspecialchars($buku['judul']);
    $_SESSION['peminjaman_message'] = "Permintaan peminjaman untuk buku '$judul_buku' telah berhasil diajukan.";
    $_SESSION['peminjaman_status'] = 'success'; // 'success' untuk alert hijau.

} catch (PDOException $e) {
    // Jika terjadi error di manapun dalam blok 'try', batalkan semua perubahan.
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Gagal memproses peminjaman: " . $e->getMessage());
    $_SESSION['peminjaman_message'] = 'Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi nanti.';
    $_SESSION['peminjaman_status'] = 'danger';

} finally {
    // Apapun yang terjadi (sukses atau gagal), selalu arahkan pengguna kembali
    // ke halaman koleksi buku.
    header("Location: index.php");
    exit();
}
?>