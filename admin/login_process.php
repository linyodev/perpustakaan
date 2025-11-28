<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
require_once('../includes/validasi.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    
    if (!validasiWajibDiisi($username)) {
        $errors[] = "Username wajib diisi";
    }
    
    if (!empty($username) && !validasiAlfanumerik($username)) {
        $errors[] = "Username hanya boleh mengandung huruf dan angka";
    }
    
    if (!empty($username) && !validasiPanjangMin($username, 3)) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    if (!empty($username) && !validasiPanjangMax($username, 50)) {
        $errors[] = "Username maksimal 50 karakter";
    }
    
    if (!validasiWajibDiisi($password)) {
        $errors[] = "Password wajib diisi";
    }
    
    if (!empty($password) && !validasiPanjangMin($password, 6)) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if (!empty($errors)) {
        $_SESSION['login_error'] = implode(", ", $errors);
        header("Location: login.php");
        exit();
    }
    $username = sanitizeInput($username);
    require_once('../includes/db_config.php');
    
    try {
        $conn = get_db_connection();
        $hashed_password = hash('sha256', $password);
        $stmt = $conn->prepare("SELECT id_admin, username FROM admin WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            session_regenerate_id(true);
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Username atau password salah";
            header("Location: login.php");
            exit();
        }  
    } catch(PDOException $e) {
        $_SESSION['login_error'] = "Terjadi kesalahan sistem";
        header("Location: login.php");
        exit();
    }
    $conn = null;
} else {
    header("Location: login.php");
    exit();
}
?>