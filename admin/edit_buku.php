<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once('../includes/validasi.php');
require_once('../includes/db_config.php');
$errors = array();
if (!isset($_GET['id']) || !validasiNumerik($_GET['id'])) {
    header("Location: kelola_buku.php");
    exit();
}
$id_buku = $_GET['id'];
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM buku WHERE id_buku = :id");
    $stmt->bindParam(':id', $id_buku, PDO::PARAM_INT);
    $stmt->execute();   
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$buku) {
        header("Location: kelola_buku.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error: Gagal mengambil data");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = isset($_POST['judul']) ? trim($_POST['judul']) : "";
    $penulis = isset($_POST['penulis']) ? trim($_POST['penulis']) : "";
    $penerbit = isset($_POST['penerbit']) ? trim($_POST['penerbit']) : "";
    $tahun = isset($_POST['tahun']) ? trim($_POST['tahun']) : "";
    $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : "";
    $jumlah = isset($_POST['jumlah']) ? trim($_POST['jumlah']) : "";
    $sampul_lama = isset($_POST['sampul_lama']) ? $_POST['sampul_lama'] : "";
    $sampul = $sampul_lama;

    if (!validasiWajibDiisi($judul)) {
        $errors[] = "Judul buku wajib diisi";
    }
    
    if (!empty($judul) && !validasiPanjangMin($judul, 3)) {
        $errors[] = "Judul minimal 3 karakter";
    }
    
    if (!empty($judul) && !validasiPanjangMax($judul, 200)) {
        $errors[] = "Judul maksimal 200 karakter";
    }
    
    if (!validasiWajibDiisi($penulis)) {
        $errors[] = "Nama penulis wajib diisi";
    }
    
    if (!empty($penulis) && !validasiNama($penulis)) {
        $errors[] = "Nama penulis hanya boleh berisi huruf dan spasi";
    }
    
    if (!empty($penulis) && !validasiPanjangMax($penulis, 150)) {
        $errors[] = "Nama penulis maksimal 150 karakter";
    }
    
    if (!empty($penerbit) && !validasiPanjangMax($penerbit, 150)) {
        $errors[] = "Nama penerbit maksimal 150 karakter";
    }
    if (!validasiWajibDiisi($tahun)) {
        $errors[] = "Tahun terbit wajib diisi";
    } 
    elseif (!validasiTahun($tahun)) {
        $errors[] = "Tahun harus berupa 4 digit angka (contoh: 2024)";
    }

    if (!empty($kategori) && !validasiPanjangMax($kategori, 100)) {
        $errors[] = "Kategori maksimal 100 karakter";
    }

    if (!validasiWajibDiisi($jumlah)) {
        $errors[] = "Jumlah stok wajib diisi";
    } 
    elseif (!validasiJumlahPositif($jumlah)) {
        $errors[] = "Jumlah stok harus berupa angka positif";
    } 
    elseif (strlen($jumlah) > 11) {
        $errors[] = "Jumlah stok maksimal 11 digit";
    }

    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['sampul'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Format file sampul tidak valid. Gunakan JPG, PNG, atau GIF.";
        } elseif ($file['size'] > $max_size) {
            $errors[] = "Ukuran file sampul maksimal 2MB.";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $sampul = uniqid('buku_') . '.' . $ext;
            $upload_dir = '../assets/uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $sampul)) {
                if (!empty($sampul_lama) && file_exists($upload_dir . $sampul_lama)) {
                    unlink($upload_dir . $sampul_lama);
                }
            } else {
                $errors[] = "Gagal mengupload file sampul.";
                $sampul = $sampul_lama;
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $judul = sanitizeInput($judul);
            $penulis = sanitizeInput($penulis);
            $penerbit = sanitizeInput($penerbit);
            $kategori = sanitizeInput($kategori);
            
            $stmt = $conn->prepare("UPDATE buku SET judul = :judul, penulis = :penulis, penerbit = :penerbit, tahun = :tahun, kategori = :kategori, jumlah = :jumlah, sampul = :sampul WHERE id_buku = :id");
            
            $stmt->bindParam(':judul', $judul, PDO::PARAM_STR);
            $stmt->bindParam(':penulis', $penulis, PDO::PARAM_STR);
            $stmt->bindParam(':penerbit', $penerbit, PDO::PARAM_STR);
            $stmt->bindParam(':tahun', $tahun, PDO::PARAM_STR);
            $stmt->bindParam(':kategori', $kategori, PDO::PARAM_STR);
            $stmt->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
            $stmt->bindParam(':sampul', $sampul, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id_buku, PDO::PARAM_INT);
            $stmt->execute();
            
            $_SESSION['success_message'] = "Data buku berhasil diupdate";
            header("Location: kelola_buku.php");
            exit();
            
        } catch(PDOException $e) {
            $errors[] = "Gagal mengupdate data ke database";
        }
    }   
    $buku['judul'] = $judul;
    $buku['penulis'] = $penulis;
    $buku['penerbit'] = $penerbit;
    $buku['tahun'] = $tahun;
    $buku['kategori'] = $kategori;
    $buku['jumlah'] = $jumlah;
}
$admin_username = htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Edit Data Buku</h1>
            <div class="admin-info">
                <span><?php echo $admin_username; ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
        
        <nav class="admin-menu">
            <ul>
                <li><a href="kelola_buku.php" class="active">Kelola Buku</a></li>
                <li><a href="lihat_pemustaka.php">Lihat Pemustaka</a></li>
                <li><a href="kelola_peminjaman.php">Kelola Peminjaman</a></li>
            </ul>
        </nav>
        
        <main>
            <div class="form-container">
                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <ul>
                            <?php foreach($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="edit_buku.php?id=<?php echo $id_buku; ?>" method="POST" enctype="multipart/form-data" class="data-form">
                    <div class="form-group">
                        <label for="judul">Judul Buku: <span class="required">*</span></label>
                        <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Minimal 3 karakter, maksimal 200 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="penulis">Penulis: <span class="required">*</span></label>
                        <input type="text" id="penulis" name="penulis" value="<?php echo htmlspecialchars($buku['penulis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Maksimal 150 karakter, hanya huruf dan spasi</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="penerbit">Penerbit:</label>
                        <input type="text" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Maksimal 150 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="tahun">Tahun Terbit: <span class="required">*</span></label>
                        <input type="text" id="tahun" name="tahun" value="<?php echo htmlspecialchars($buku['tahun'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Harus 4 digit angka (contoh: 2024)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="kategori">Kategori:</label>
                        <input type="text" id="kategori" name="kategori" value="<?php echo htmlspecialchars($buku['kategori'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Maksimal 100 karakter</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah">Jumlah Stok: <span class="required">*</span></label>
                        <input type="text" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($buku['jumlah'], ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Maksimal 11 digit, hanya angka positif</small>
                    </div>

                    <div class="form-group">
                        <label for="sampul">Sampul Buku:</label>
                        <?php if (!empty($buku['sampul'])): ?>
                            <div class="current-cover">
                                <img src="../assets/uploads/<?php echo htmlspecialchars($buku['sampul'], ENT_QUOTES, 'UTF-8'); ?>" alt="Sampul saat ini" style="max-width: 150px; margin-bottom: 10px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="sampul" name="sampul">
                        <input type="hidden" name="sampul_lama" value="<?php echo htmlspecialchars($buku['sampul'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small class="form-hint">Format file: JPG, JPEG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah sampul.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update</button>
                        <a href="kelola_buku.php" class="btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>