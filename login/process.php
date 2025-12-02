<?php

/*
|--------------------------------------------------------------------------
| Skrip Pemrosesan Login Pengguna
|--------------------------------------------------------------------------
|
| File ini bertugas untuk memverifikasi data login yang dikirimkan
| oleh pengguna dari formulir login. Jika email dan password cocok
| dengan yang ada di database, skrip ini akan membuat session
| yang menandakan pengguna sudah login.
|
*/

// Mulai session untuk membuat session login.
session_start();

// --- Keamanan: Jika Pengguna Sudah Login ---
// Jika pengguna mencoba mengakses halaman ini padahal sudah login,
// langsung arahkan saja ke halaman utama.
if (isset($_SESSION['user_login']) && $_SESSION['user_login'] === true) {
    header("Location: /perpustakaan/");
    exit();
}

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// --- Keamanan: Hanya Proses Jika Request dari Formulir (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari formulir.
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // --- Validasi Sederhana ---
    // Pastikan kedua field tidak kosong.
    if (!validasiWajibDiisi($email) || !validasiWajibDiisi($password)) {
        $_SESSION['login_error'] = "Email dan password wajib diisi.";
        header("Location: ./"); // Arahkan kembali ke form login.
        exit();
    }
    
    // Pastikan format email benar.
    if (!validasiEmail($email)) {
        $_SESSION['login_error'] = "Format email tidak valid.";
        header("Location: ./"); // Arahkan kembali ke form login.
        exit();
    }
    
    try {
        // Dapatkan koneksi database.
        $conn = get_db_connection();

        /*
         * =============================================================
         * == PERINGATAN KEAMANAN TINGKAT TINGGI ==
         * =============================================================
         * Metode verifikasi password di bawah ini SANGAT TIDAK AMAN.
         * Seharusnya menggunakan `password_verify()` untuk membandingkan
         * password yang diinput dengan hash yang ada di database.
         * 
         * Contoh yang benar:
         * 1. Ambil dulu data pengguna (termasuk hash password) dari DB berdasarkan email.
         *    $stmt = $conn->prepare("SELECT * FROM pemustaka WHERE email = ?");
         *    $stmt->execute([$email]);
         *    $user = $stmt->fetch();
         * 
         * 2. Verifikasi password yang diinput dengan hash dari DB.
         *    if ($user && password_verify($password, $user['password'])) {
         *        // Login berhasil! Lanjutkan membuat session.
         *    } else {
         *        // Login gagal.
         *    }
         */
        $hashed_password = hash('sha256', $password);
        
        // Siapkan query untuk mencari pengguna dengan email DAN password yang cocok.
        $stmt = $conn->prepare("SELECT id_pemustaka, nama, email FROM pemustaka WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        
        // --- Pengambilan Keputusan ---
        // Jika query menemukan satu baris data (atau lebih)...
        if ($stmt->rowCount() > 0) {
            // ...berarti login berhasil!
            
            // Ambil data pengguna dari hasil query.
            $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buat session untuk menandakan pengguna sudah login
            // dan simpan beberapa data penting ke dalam session.
            $_SESSION['user_login'] = true;
            $_SESSION['user_id'] = $userdata['id_pemustaka'];
            $_SESSION['user_name'] = $userdata['nama'];
            $_SESSION['user_email'] = $userdata['email'];
            
            // Arahkan pengguna ke halaman utama sebagai pengguna yang sudah login.
            header("Location: /perpustakaan/");
            exit();
        } else {
            // Jika tidak ada baris data yang cocok, berarti email atau password salah.
            $_SESSION['login_error'] = "Email atau password salah.";
            header("Location: ./"); // Arahkan kembali ke form login.
            exit();
        }  
    } catch(PDOException $e) {
        // Jika terjadi masalah dengan koneksi atau query database.
        error_log("Gagal login: " . $e->getMessage());
        $_SESSION['login_error'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
        header("Location: ./"); // Arahkan kembali ke form login.
        exit();
    }
} else {
    // Jika ada yang mencoba mengakses file ini langsung, tendang balik ke halaman login.
    header("Location: ./");
    exit();
}
?>