<?php
session_start();
require_once('../includes/authorization.php');


$type = $_GET['type'];
$title = $_GET['title'];
$message = $_GET['message'];
$pageTitle = htmlspecialchars($title);
$cssFile = "/perpustakaan/assets/css/status_page.css";
include('../templates/header.php');


$boxClass = ($type === 'success') ? 'success' : 'error';

?>

<div class="status-container">
    <div class="status-box <?= $boxClass ?>">
        <h2><?= htmlspecialchars($title) ?></h2>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="index.php" class="btn">Kembali ke Koleksi Buku</a>
    </div>
</div>

<?php include('../templates/footer.php'); ?>
