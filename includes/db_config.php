<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'library');
define('DB_USER', 'root');
define('DB_PASS', '');

function get_db_connection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8mb4");
        return $conn; 
    } catch(PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Terjadi kesalahan koneksi database");
    }
}
?>