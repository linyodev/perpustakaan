<?php
session_start();
if (isset($_SESSION['user_login']) && $_SESSION['user_login'] === true) {
    header("Location: ../index.php");
    exit();
}
require_once('../includes/validasi.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    
    if (!validasiWajibDiisi($email)) {
        $errors[] = "email wajib diisi";
    }
    
    if (!validasiEmail($email)) {
        $errors[] = "email tidak valid";
    }
    
    if (!validasiWajibDiisi($password)) {
        $errors[] = "Password wajib diisi";
    }
    
    if (!empty($errors)) {
        $_SESSION['login_error'] = implode(", ", $errors);
        header("Location: index.php");
        exit();
    }
    $email = sanitizeInput($email);
    require_once('../includes/db_config.php');
    
    try {
        $conn = get_db_connection();
        $hashed_password = hash('sha256', $password);
        $stmt = $conn->prepare("SELECT nama,password, email,id_pemustaka FROM pemustaka WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_login'] = true;
            $_SESSION['user_id'] = $userdata['id_pemustaka'];
            $_SESSION['user_name'] = $userdata['nama'];
            $_SESSION['user_email'] = $userdata['email'];
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "email atau password salah";
            header("Location: index.php");
            exit();
        }  
    } catch(PDOException $e) {
        $_SESSION['login_error'] = "Terjadi kesalahan sistem";
        var_dump($e->getMessage());
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>