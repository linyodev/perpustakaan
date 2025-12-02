<?php

/*
|--------------------------------------------------------------------------
| Halaman Cetak Invoice/Bukti Transaksi
|--------------------------------------------------------------------------
|
| Halaman ini dirancang khusus untuk menampilkan detail sebuah transaksi
| peminjaman dalam format yang rapi dan mudah dicetak. Halaman ini
| tidak menggunakan header/footer global agar tampilannya bersih.
|
*/

// Mulai session.
session_start();

// --- Otorisasi ---
// Pastikan ada pengguna yang login, baik itu admin atau pemustaka biasa.
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_logged_in'])) {
    http_response_code(403); // Kode status 'Forbidden'
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Panggil file konfigurasi database.
require_once '../includes/db_config.php';

// --- Validasi Input ---
// Pastikan ID transaksi dikirim melalui URL dan formatnya adalah angka.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400); // Kode status 'Bad Request'
    die("ID peminjaman tidak valid atau tidak ditemukan.");
}
$id_peminjaman = $_GET['id'];

try {
    // Dapatkan koneksi database.
    $conn = get_db_connection();
    
    // --- Pengambilan Data dari Database ---
    // Query ini mengambil semua data yang dibutuhkan untuk invoice dengan
    // menggabungkan tiga tabel: peminjaman, buku, dan pemustaka.
    $query = "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, p.status, 
                     p.id_pemustaka, b.judul AS judul_buku, pm.nama AS nama_pemustaka
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN pemustaka pm ON p.id_pemustaka = pm.id_pemustaka
              WHERE p.id_peminjaman = :id_peminjaman";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_peminjaman', $id_peminjaman, PDO::PARAM_INT);
    $stmt->execute();
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data dengan ID tersebut tidak ditemukan, hentikan proses.
    if (!$loan) {
        http_response_code(404); // Kode status 'Not Found'
        die("Data peminjaman tidak ditemukan.");
    }

    // --- Pemeriksaan Kepemilikan (Penting untuk Keamanan) ---
    // Jika yang mengakses adalah pengguna biasa (bukan admin)...
    if (isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
        // ...pastikan ID pemilik invoice sama dengan ID pengguna yang sedang login.
        if ($loan['id_pemustaka'] != $_SESSION['user_id']) {
            // Jika tidak sama, artinya pengguna mencoba melihat invoice orang lain.
            http_response_code(403); // Kode status 'Forbidden'
            die("Akses ditolak. Anda tidak memiliki izin untuk melihat invoice ini.");
        }
    }

} catch(PDOException $e) {
    // Jika terjadi masalah dengan database.
    http_response_code(500); // Kode status 'Internal Server Error'
    error_log("Gagal membuat invoice: " . $e->getMessage());
    die("Gagal mengambil data dari database. Silakan coba lagi nanti.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Peminjaman #<?php echo htmlspecialchars($loan['id_peminjaman']); ?></title>
    <style>
        /* 
          CSS ini diletakkan langsung di sini (inline) karena halaman ini
          bersifat mandiri dan tidak memanggil template header/footer.
        */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px 40px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.07);
            border: 1px solid #ddd;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .invoice-header h1 { margin: 0; font-size: 2em; }
        .invoice-header p { margin: 5px 0 0; color: #666; }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .invoice-details th {
            width: 35%;
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: capitalize;
            background-color: #d4edda;
            color: #155724;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 50px;
            font-size: 0.9em;
            color: #888;
        }
        .print-button {
            display: block;
            width: 150px;
            margin: 30px auto 10px;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 1em;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .print-button:hover { background-color: #0056b3; }

        /* Aturan khusus ini akan aktif saat halaman dicetak. */
        @media print {
            body { background-color: #fff; padding: 0; margin: 0; }
            .invoice-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .print-button { display: none; } /* Sembunyikan tombol saat mencetak. */
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <header class="invoice-header">
            <h1>Bukti Transaksi Perpustakaan</h1>
            <p>Perpustakaan Umum Digital</p>
        </header>

        <section class="invoice-details">
            <table>
                <tr>
                    <th>ID Transaksi</th>
                    <td>#<?php echo htmlspecialchars($loan['id_peminjaman']); ?></td>
                </tr>
                <tr>
                    <th>Nama Peminjam</th>
                    <td><?php echo htmlspecialchars($loan['nama_pemustaka']); ?></td>
                </tr>
                <tr>
                    <th>Judul Buku</th>
                    <td><?php echo htmlspecialchars($loan['judul_buku']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <td><?php echo htmlspecialchars(date('d F Y', strtotime($loan['tanggal_pinjam']))); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Kembali</th>
                    <td><?php echo $loan['tanggal_kembali'] ? htmlspecialchars(date('d F Y', strtotime($loan['tanggal_kembali']))) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><span class="status-badge"><?php echo htmlspecialchars($loan['status']); ?></span></td>
                </tr>
            </table>
        </section>

        <footer class="invoice-footer">
            <p>Ini adalah bukti transaksi yang sah. Terima kasih atas kunjungan Anda.</p>
        </footer>

        <!-- Tombol ini memicu fungsi cetak di browser. -->
        <button class="print-button" onclick="window.print()">Cetak</button>
    </div>

</body>
</html>
