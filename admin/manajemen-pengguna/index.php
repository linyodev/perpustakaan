<?php
$pageTitle = "Manajemen Pengguna";
$cssFile = "/perpustakaan/assets/css/admin_manajemen_pengguna.css";
include('../../templates/header.php');
?>

<h2 class="page-title">Manajemen Daftar Pengguna (Pemustaka)</h2>
<button class="btn">Tambah Pengguna Baru</button>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>USR001</td>
            <td>John Doe</td>
            <td>johndoe</td>
            <td>johndoe@example.com</td>
            <td>
                <a href="#" class="btn-aksi edit">Edit</a>
                <a href="#" class="btn-aksi hapus">Hapus</a>
            </td>
        </tr>
         <tr>
            <td>USR002</td>
            <td>Jane Smith</td>
            <td>janesmith</td>
            <td>jane@example.com</td>
            <td>
                <a href="#" class="btn-aksi edit">Edit</a>
                <a href="#" class="btn-aksi hapus">Hapus</a>
            </td>
        </tr>
    </tbody>
</table>

<?php include('../../templates/footer.php'); ?>