<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Koneksi Database
|--------------------------------------------------------------------------
|
| File ini berisi semua pengaturan yang diperlukan untuk terhubung ke database.
| Di sini kita mendefinisikan host, nama database, user, dan password.
| Selain itu, ada juga sebuah fungsi untuk mempermudah pembuatan koneksi.
|
*/

// Alamat server database.
// Jika environment variable APP_DB_HOST ada, gunakan nilainya. Jika tidak, gunakan 'localhost'.
define('DB_HOST', $_ENV['APP_DB_HOST'] ?? 'localhost');

// Nama database yang akan digunakan.
define('DB_NAME', 'library');

// Username untuk login ke database.
// Sama seperti host, ini akan menggunakan environment variable jika tersedia.
define('DB_USER', $_ENV['APP_DB_USER'] ?? 'root');

// Password untuk login ke database.
// Biasanya kosong di lingkungan pengembangan lokal.
define('DB_PASS', $_ENV['APP_DB_PASSWORD'] ?? '');


/*
|--------------------------------------------------------------------------
| Fungsi untuk Membuat Koneksi Database
|--------------------------------------------------------------------------
|
| Fungsi ini, get_db_connection(), bertugas untuk membuat object koneksi
| ke database menggunakan PDO (PHP Data Objects). PDO dipilih karena lebih aman
| dan fleksibel dibandingkan mysql_ atau mysqli_.
|
| Jika koneksi berhasil, fungsi ini akan mengembalikan object koneksi.
| Jika gagal, skrip akan berhenti dan menampilkan pesan error.
|
*/
function get_db_connection() {
    try {
        // Proses pembuatan koneksi PDO.
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );

        // Mengatur agar PDO memberitahu kita jika ada error SQL,
        // alih-alih diam-diam gagal.
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Memastikan komunikasi dengan database menggunakan set karakter utf8mb4,
        // yang mendukung berbagai macam karakter termasuk emoji.
        $conn->exec("SET NAMES utf8mb4");

        // Jika semua lancar, kembalikan object koneksinya.
        return $conn; 

    } catch(PDOException $e) {
        // Jika blok 'try' di atas gagal, tangkap error-nya di sini.
        // Kita catat error-nya ke dalam log server agar bisa diinvestigasi nanti.
        error_log("Database connection failed: " . $e->getMessage());

        // Hentikan eksekusi skrip dan tampilkan pesan yang ramah ke pengguna.
        // Kita tidak menampilkan detail error teknis ke pengguna demi keamanan.
        die("Terjadi kesalahan koneksi ke database. Mohon coba lagi nanti.");
    }
}
?>
