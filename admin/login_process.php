<?php

/*
|--------------------------------------------------------------------------
| Skrip Pemrosesan Login Admin
|--------------------------------------------------------------------------
|
| Sama seperti proses login pengguna, file ini adalah "otak" untuk
| verifikasi login admin. Ia akan mencocokkan username dan password
| yang dikirim dengan data di tabel 'admin' pada database.
|
*/

// Mulai session.
session_start();

// Jika admin sudah login, jangan proses lagi, langsung lempar ke dashboard.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Panggil file-file yang dibutuhkan.
require_once('../includes/db_config.php');
require_once('../includes/validasi.php');

// Pastikan skrip ini diakses dari form (metode POST).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan input.
    $username = sanitizeInput($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validasi dasar: pastikan tidak ada yang kosong.
    if (!validasiWajibDiisi($username) || !validasiWajibDiisi($password)) {
        $_SESSION['login_error'] = "Username dan password wajib diisi.";
        header("Location: login.php");
        exit();
    }
    
    // Proses otentikasi.
    try {
        // Dapatkan koneksi database.
        $conn = get_db_connection();
        
        /*
         * =============================================================
         * == PERINGATAN KEAMANAN TINGKAT TINGGI ==
         * =============================================================
         * Verifikasi password ini sangat tidak aman. Sama seperti pada
         * login pengguna, seharusnya menggunakan `password_verify()`.
         */
        $hashed_password = hash('sha256', $password);
        
        // Siapkan query untuk mencari admin yang cocok.
        $stmt = $conn->prepare("SELECT id_admin, username FROM admin WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();
        
        // Jika ditemukan satu (atau lebih) admin yang cocok...
        if ($stmt->rowCount() > 0) {
            // ...login berhasil!
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buat session untuk admin.
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Regenerasi ID session adalah langkah keamanan yang baik untuk
            // mencegah serangan 'session fixation'.
            session_regenerate_id(true);
            
            // Arahkan ke dashboard admin.
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika tidak ada yang cocok, kirim pesan error.
            $_SESSION['login_error'] = "Username atau password salah.";
            header("Location: login.php");
            exit();
        }  
    } catch(PDOException $e) {
        // Jika ada masalah dengan database.
        error_log("Gagal login admin: " . $e->getMessage());
        $_SESSION['login_error'] = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        header("Location: login.php");
        exit();
    }
} else {
    // Jika diakses langsung, tendang ke halaman login.
    header("Location: login.php");
    exit();
}
?>