<?php
$pageTitle = "Manajemen Peminjaman";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_pinjaman.css";
include('../../templates/header.php');
?>

<h2 class="page-title">Mengelola Daftar Buku Pinjaman</h2>

<table class="data-table">
    <thead>
        <tr>
            <th>ID Pinjam</th>
            <th>Judul Buku</th>
            <th>Nama Peminjam</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>PJ001</td>
            <td>Bumi Manusia</td>
            <td>John Doe</td>
            <td>01 Nov 2025</td>
            <td>08 Nov 2025</td>
            <td><span class="status-badge dipinjam">Dipinjam</span></td>
            <td>
                <a href="#" class="btn-aksi kembali">Tandai Kembali</a>
            </td>
        </tr>
        <tr>
            <td>PJ002</td>
            <td>Laskar Pelangi</td>
            <td>Jane Smith</td>
            <td>25 Okt 2025</td>
            <td>01 Nov 2025</td>
            <td><span class="status-badge terlambat">Terlambat</span></td>
            <td>
                 <a href="#" class="btn-aksi kembali">Tandai Kembali</a>
            </td>
        </tr>
         <tr>
            <td>PJ003</td>
            <td>Ayat-Ayat Cinta</td>
            <td>John Doe</td>
            <td>15 Okt 2025</td>
            <td>22 Okt 2025</td>
            <td><span class="status-badge sudah-kembali">Sudah Kembali</span></td>
            <td>-</td>
        </tr>
    </tbody>
</table>

<?php include('../../templates/footer.php'); ?>