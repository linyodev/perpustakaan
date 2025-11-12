<?php
$pageTitle = "Manajemen Buku";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_buku.css";
include('../../templates/header.php');
?>

<h2 class="page-title">Manajemen Daftar Buku</h2>
<button class="btn">Tambah Buku Baru</button>

<table class="data-table">
    <thead>
        <tr>
            <th>ID Buku</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>BK001</td>
            <td>Laskar Pelangi</td>
            <td>Andrea Hirata</td>
            <td>5</td>
            <td>
                <a href="#" class="btn-aksi edit">Edit</a>
                <a href="#" class="btn-aksi hapus">Hapus</a>
            </td>
        </tr>
        <tr>
            <td>BK002</td>
            <td>Bumi Manusia</td>
            <td>Pramoedya Ananta Toer</td>
            <td>3</td>
            <td>
                <a href="#" class="btn-aksi edit">Edit</a>
                <a href="#" class="btn-aksi hapus">Hapus</a>
            </td>
        </tr>
    </tbody>
</table>

<?php include('../../templates/footer.php'); ?>