<?php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/validasi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $pdo = get_db_connection();

    
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    $password2 = sanitizeInput($_POST['password2'] ?? '');

    
    if (!validasiWajibDiisi($nama)) {
        $errors['nama'] = 'Nama tidak boleh kosong.';
    } elseif (!validasiAlfabet($nama)) {
        $errors['nama'] = 'Nama hanya boleh berisi huruf dan spasi.';
    }

    
    if (!validasiWajibDiisi($email)) {
        $errors['email'] = 'Email tidak boleh kosong.';
    } elseif (!validasiEmail($email)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        
        $stmt = $pdo->prepare("SELECT id_pemustaka FROM pemustaka WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
        $stmt = null; 
    }

    
    if (!validasiWajibDiisi($password)) {
        $errors['password'] = 'Password tidak boleh kosong.';
    } elseif (!validasiPanjangMin($password, 6)) { 
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    
    if (!validasiWajibDiisi($password2)) {
        $errors['password2'] = 'Konfirmasi password tidak boleh kosong.';
    } elseif ($password !== $password2) {
        $errors['password2'] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        
        $hashed_password = hash('sha256', $password);

        
        $stmt = $pdo->prepare("INSERT INTO pemustaka (nama, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $email, $hashed_password])) {
            $_SESSION['success_message'] = 'Registrasi berhasil! Silakan login.';
            header('Location: ../login/index.php');
            exit();
        } else {
            $errors['general'] = 'Terjadi kesalahan saat registrasi. Mohon coba lagi.';
        }
        $stmt = null; 
    }

    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; 
        header('Location: ../register/index.php');
        exit();
    }
} else {
    
    header('Location: ../register/index.php');
    exit();
}
?>