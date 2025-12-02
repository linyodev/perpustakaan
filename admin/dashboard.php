<?php

/*
|--------------------------------------------------------------------------
| Halaman Dashboard Administrator
|--------------------------------------------------------------------------
|
| Ini adalah halaman utama yang dilihat admin setelah berhasil login.
| Halaman ini berfungsi sebagai pusat kendali, memberikan sambutan
| dan ringkasan singkat (walaupun saat ini masih data statis)
| serta navigasi ke fitur-fitur manajemen lainnya.
|
*/

// Mulai session dan lakukan otorisasi.
session_start();

// Keamanan: Pastikan hanya admin yang sudah login yang bisa mengakses halaman ini.
// Jika tidak, tendang kembali ke halaman login.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Siapkan judul halaman untuk dikirim ke template header.
$pageTitle = "Dashboard Administrator";

// Panggil template header.
// Template ini sudah cukup pintar untuk menampilkan menu navigasi khusus admin.
include('../templates/header.php'); 
?>

<!-- Konten utama untuk halaman dashboard. -->
<div class="dashboard-content">
    <h2 class="page-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
    <p>Anda telah masuk ke panel administrator. Dari sini, Anda dapat mengelola berbagai aspek dari sistem perpustakaan.</p>
    
    <!-- 
      Bagian ini untuk menampilkan ringkasan data atau statistik.
      Saat ini masih menggunakan angka statis (contoh), tapi idealnya
      angka-angka ini diambil dari database dengan query COUNT().
    -->
    <div class="dashboard-summary">
        <div class="summary-card">
            <h3>Total Buku</h3>
            <p>1,234</p> <!-- TODO: Ganti dengan data dinamis dari DB -->
        </div>
        <div class="summary-card">
            <h3>Total Pengguna</h3>
            <p>567</p> <!-- TODO: Ganti dengan data dinamis dari DB -->
        </div>
        <div class="summary-card">
            <h3>Peminjaman Aktif</h3>
            <p>89</p> <!-- TODO: Ganti dengan data dinamis dari DB -->
        </div>
    </div>
    
    <p style="margin-top: 20px;">Silakan gunakan menu navigasi di atas untuk mulai mengelola buku, pengguna, atau peminjaman.</p>
</div>

<!-- 
  Style tambahan khusus untuk halaman dashboard.
  Untuk proyek yang lebih besar, ini sebaiknya dipindahkan ke file CSS terpisah,
  misalnya 'assets/css/dashboard.css'.
-->
<style>
.dashboard-summary {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}
.summary-card {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    flex: 1; /* Membuat setiap kartu memiliki lebar yang sama */
    text-align: center;
}
.summary-card h3 {
    margin-top: 0;
    font-size: 1.2em;
}
.summary-card p {
    font-size: 2em;
    font-weight: bold;
    margin: 0;
    color: #0056b3;
}
</style>

<?php 
// Panggil template footer untuk menutup halaman.
include('../templates/footer.php'); 
?>