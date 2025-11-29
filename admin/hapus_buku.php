<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once('../includes/validasi.php');
require_once('../includes/db_config.php');
if (!isset($_GET['id']) || !validasiNumerik($_GET['id'])) {
    header("Location: kelola_buku.php");
    exit();
}
$id_buku = $_GET['id'];
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT COUNT(*) as jumlah FROM peminjaman WHERE id_buku = :id AND status != 'dikembalikan'");
    $stmt->bindParam(':id', $id_buku, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['jumlah'] > 0) {
        $_SESSION['success_message'] = "Tidak dapat menghapus buku yang sedang dipinjam";
        header("Location: kelola_buku.php");
        exit();
    }
    $stmt = $conn->prepare("DELETE FROM buku WHERE id_buku = :id");
    $stmt->bindParam(':id', $id_buku, PDO::PARAM_INT);
    $stmt->execute();
    $_SESSION['success_message'] = "Buku berhasil dihapus";
} catch(PDOException $e) {
    $_SESSION['success_message'] = "Gagal menghapus buku";
}
header("Location: kelola_buku.php");
exit();
?>