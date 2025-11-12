<?php
$pageTitle = "History Peminjaman";
$cssFile = "/perpustakaan/assets/css/history_peminjaman.css";
include('../templates/header.php');
?>

<h2 class="page-title">History Peminjaman Buku</h2>

<p>Berikut adalah daftar buku yang sedang Anda pinjam dan yang sudah dikembalikan.</p>

<table class="data-table">
    <thead>
        <tr>
            <th>Judul Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Bumi Manusia</td>
            <td>01 November 2025</td>
            <td>08 November 2025</td>
            <td><span class="status-badge dipinjam">Dipinjam</span></td>
        </tr>
        <tr>
            <td>Laskar Pelangi</td>
            <td>15 Oktober 2025</td>
            <td>22 Oktober 2025</td>
            <td><span class="status-badge kembali">Sudah Kembali</span></td>
        </tr>
        <tr>
            <td>Ayat-Ayat Cinta</td>
            <td>05 Oktober 2025</td>
            <td>12 Oktober 2025</td>
            <td><span class="status-badge kembali">Sudah Kembali</span></td>
        </tr>
    </tbody>
</table>

<?php include('../templates/footer.php'); ?>