<?php
session_start();
require_once '../includes/authorization.php';
require_once '../includes/db_config.php';


function redirectToStatus($type, $title, $message) {
    $url = sprintf(
        "status.php?type=%s&title=%s&message=%s",
        urlencode($type),
        urlencode($title),
        urlencode($message)
    );
    header("Location: " . $url);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    
    header("Location: index.php");
    exit();
}

$id_buku = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$id_pemustaka = $_SESSION['user_id'];
$loan_period = 7; 

if ($id_buku === false) {
    redirectToStatus('error', 'Gagal', 'ID buku yang dimasukkan tidak valid.');
}

$conn = get_db_connection();

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT judul, jumlah FROM buku WHERE id_buku = :id_buku FOR UPDATE");
    $stmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt->execute();
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$buku || $buku['jumlah'] <= 0) {
        $conn->rollBack();
        $judul = $buku ? htmlspecialchars($buku['judul']) : 'yang Anda pilih';
        redirectToStatus('error', 'Gagal Pinjam', "Stok buku '" . $judul . "' telah habis atau buku tidak ditemukan.");
    }

    $updateStmt = $conn->prepare("UPDATE buku SET jumlah = jumlah - 1 WHERE id_buku = :id_buku");
    $updateStmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $updateStmt->execute();

    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime("+$loan_period days"));
    $status = 'Dipinjam';

    $insertStmt = $conn->prepare(
        "INSERT INTO peminjaman (id_buku, id_pemustaka, tanggal_pinjam, tanggal_kembali, status) 
         VALUES (:id_buku, :id_pemustaka, :tanggal_pinjam, :tanggal_kembali, :status)"
    );
    $insertStmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $insertStmt->bindParam(':id_pemustaka', $id_pemustaka, PDO::PARAM_INT);
    $insertStmt->bindParam(':tanggal_pinjam', $tanggal_pinjam);
    $insertStmt->bindParam(':tanggal_kembali', $tanggal_kembali);
    $insertStmt->bindParam(':status', $status);
    $insertStmt->execute();

    $conn->commit();
    
    $message = "Buku '" . htmlspecialchars($buku['judul']) . "' berhasil dipinjam. Waktu pinjam Anda adalah " . $loan_period . " hari, dimulai dari tanggal " . date('d F Y', strtotime($tanggal_pinjam)) . ".";
    redirectToStatus('success', 'Peminjaman Berhasil', $message);

} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    redirectToStatus('error', 'Kesalahan Database', 'Terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi nanti.');
}