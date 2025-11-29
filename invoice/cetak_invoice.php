<?php
session_start();


if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_logged_in'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

require_once '../includes/db_config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID peminjaman tidak valid.");
}

$id_peminjaman = $_GET['id'];

try {
    $conn = get_db_connection();
    $query = "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, p.status, 
                     b.judul AS judul_buku, 
                     pm.nama AS nama_pemustaka
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN pemustaka pm ON p.id_pemustaka = pm.id_pemustaka
              WHERE p.id_peminjaman = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
    $stmt->execute();
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$loan) {
        die("Data peminjaman tidak ditemukan.");
    }

    
    if (isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
        $stmt_check = $conn->prepare("SELECT id_pemustaka FROM peminjaman WHERE id_peminjaman = :id");
        $stmt_check->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
        $stmt_check->execute();
        $owner = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($owner['id_pemustaka'] != $_SESSION['user_id']) {
            die("Anda tidak memiliki akses ke invoice ini.");
        }
    }

} catch(PDOException $e) {
    die("Gagal mengambil data dari database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Peminjaman #<?php echo htmlspecialchars($loan['id_peminjaman']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .invoice-container {
            width: 80%;
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #333;
        }
        .invoice-header p {
            margin: 5px 0 0;
            color: #666;
        }
        .invoice-details, .invoice-summary {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .invoice-details th {
            width: 40%;
            background-color: #f8f8f8;
            font-weight: 500;
        }
        .status-returned {
            color: #28a745;
            font-weight: bold;
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
            margin: 30px auto 0;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .invoice-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-container">
        <div class="invoice-header">
            <h1>Bukti Pengembalian Buku</h1>
            <p>Perpustakaan Umum</p>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <th>ID Peminjaman</th>
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
                    <td><?php echo date('d F Y', strtotime($loan['tanggal_pinjam'])); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Kembali</th>
                    <td><?php echo $loan['tanggal_kembali'] ? date('d F Y', strtotime($loan['tanggal_kembali'])) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td class="status-returned"><?php echo htmlspecialchars($loan['status']); ?></td>
                </tr>
            </table>
        </div>

        <div class="invoice-footer">
            <p>Terima kasih telah menggunakan layanan kami.</p>
        </div>

        <button class="print-button" onclick="window.print()">Cetak Invoice</button>
    </div>

</body>
</html>
