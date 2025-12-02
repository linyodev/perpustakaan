<?php

/*
|--------------------------------------------------------------------------
| Skrip Pemrosesan Pendaftaran
|--------------------------------------------------------------------------
|
| File ini adalah "otak" di balik formulir pendaftaran. Tugasnya adalah
| menerima data yang dikirim, memeriksa validitasnya (apakah semua
| kolom diisi dengan benar, apakah email sudah ada, dll.), dan jika
| semuanya baik-baik saja, data akan disimpan ke database.
|
*/

// Mulai session agar bisa mengirim pesan (error/sukses) kembali ke halaman formulir.
session_start();

// Memanggil file-file penting yang berisi fungsi-fungsi yang kita butuhkan.
require_once '../includes/db_config.php'; // Untuk koneksi database.
require_once '../includes/validasi.php';   // Untuk fungsi-fungsi validasi.

// --- Keamanan: Hanya Proses Jika Request dari Formulir (POST) ---
// Ini untuk mencegah seseorang mengakses file ini secara langsung dari browser.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Siapkan array kosong untuk menampung semua pesan error yang mungkin terjadi.
    $errors = [];
    
    // Dapatkan koneksi ke database dari file db_config.php.
    $pdo = get_db_connection();

    // --- Sanitasi Input ---
    // Meskipun kita menggunakan `htmlspecialchars` saat menampilkan, lebih baik
    // membersihkan input di sini juga untuk lapisan keamanan ekstra.
    // Namun, untuk password, kita biarkan apa adanya agar tidak merusak karakter.
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Tidak disanitasi agar karakter spesial tetap ada
    $password2 = $_POST['password2'] ?? '';

    // --- Tahap Validasi ---

    // 1. Validasi Nama
    if (!validasiWajibDiisi($nama)) {
        $errors['nama'] = 'Nama tidak boleh kosong.';
    }

    // 2. Validasi Email
    if (!validasiWajibDiisi($email)) {
        $errors['email'] = 'Email tidak boleh kosong.';
    } elseif (!validasiEmail($email)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        // Jika email valid, cek apakah sudah ada yang punya di database.
        $stmt = $pdo->prepare("SELECT id_pemustaka FROM pemustaka WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
    }

    // 3. Validasi Password
    if (!validasiWajibDiisi($password)) {
        $errors['password'] = 'Password tidak boleh kosong.';
    } elseif (!validasiPanjangMin($password, 8)) { 
        $errors['password'] = 'Password minimal 8 karakter.';
    } elseif (!validasiTanpaHtml($password)) {
        $errors['password'] = 'Format password tidak valid, dilarang menggunakan tag HTML.';
    }

    // 4. Validasi Konfirmasi Password
    if (!validasiWajibDiisi($password2)) {
        $errors['password2'] = 'Konfirmasi password tidak boleh kosong.';
    } elseif ($password !== $password2) {
        $errors['password2'] = 'Konfirmasi password tidak cocok.';
    } elseif (!validasiTanpaHtml($password)) {
        $errors['password2'] = 'Format password tidak valid, dilarang menggunakan tag HTML.';

    }

    // --- Pengambilan Keputusan ---
    
    // Jika setelah semua validasi, array $errors tetap kosong...
    if (empty($errors)) {
        // ...maka semua data valid dan siap disimpan.
        
        /*
         * CATATAN KEAMANAN PENTING:
         * Menggunakan hash('sha256', $password) sangat TIDAK AMAN untuk password.
         * Metode ini cepat dan tidak menggunakan 'salt', sehingga rentan terhadap
         * serangan rainbow table dan brute-force.
         * 
         * Gunakan fungsi bawaan PHP yang jauh lebih aman:
         * $hashed_password = password_hash($password, PASSWORD_DEFAULT);
         * 
         * Dan saat login, verifikasi dengan:
         * password_verify($password_input, $hashed_password_from_db);
         */
        $hashed_password = hash('sha256', $password);

        try {
            // Siapkan query untuk memasukkan pengguna baru ke tabel 'pemustaka'.
            $stmt = $pdo->prepare("INSERT INTO pemustaka (nama, email, password) VALUES (?, ?, ?)");
            
            // Eksekusi query dengan data yang sudah divalidasi.
            if ($stmt->execute([$nama, $email, $hashed_password])) {
                // Jika berhasil, kirim pesan sukses dan arahkan ke halaman login.
                $_SESSION['success_message'] = 'Registrasi berhasil! Silakan login.';
                header('Location: ../login/');
                exit();
            } else {
                // Ini jarang terjadi, tapi sebagai jaga-jaga.
                $errors['general'] = 'Terjadi kesalahan saat registrasi. Mohon coba lagi.';
            }
        } catch (PDOException $e) {
            // Jika ada masalah dengan database saat proses insert.
            error_log("Gagal registrasi: " . $e->getMessage());
            $errors['general'] = 'Terjadi kesalahan pada server. Mohon coba lagi nanti.';
        }
    }

    // Jika array $errors TIDAK kosong...
    if (!empty($errors)) {
        // ...maka ada kesalahan validasi.
        
        // Simpan array error dan data yang sudah diisi pengguna ke dalam session.
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; 
        
        // Arahkan pengguna kembali ke halaman pendaftaran untuk memperbaiki input.
        header('Location: ../register/');
        exit();
    }

} else {
    // Jika ada yang mencoba mengakses file ini langsung, tendang balik ke halaman registrasi.
    header('Location: ../register/');
    exit();
}
?>