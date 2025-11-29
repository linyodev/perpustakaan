<?php
session_start();
require_once '../includes/db_config.php';
require_once '../includes/validasi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $pdo = get_db_connection();

    // Sanitize inputs
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    $password2 = sanitizeInput($_POST['password2'] ?? '');

    // Validate Nama
    if (!validasiWajibDiisi($nama)) {
        $errors['nama'] = 'Nama tidak boleh kosong.';
    } elseif (!validasiAlfabet($nama)) {
        $errors['nama'] = 'Nama hanya boleh berisi huruf dan spasi.';
    }

    // Validate Email
    if (!validasiWajibDiisi($email)) {
        $errors['email'] = 'Email tidak boleh kosong.';
    } elseif (!validasiEmail($email)) {
        $errors['email'] = 'Format email tidak valid.';
    } else {
        // Check if email already exists in pemustaka table
        $stmt = $pdo->prepare("SELECT id_pemustaka FROM pemustaka WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
        $stmt = null; // Close statement
    }

    // Validate Password
    if (!validasiWajibDiisi($password)) {
        $errors['password'] = 'Password tidak boleh kosong.';
    } elseif (!validasiPanjangMin($password, 6)) { // Assuming min 6 characters for password
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    // Validate Password Confirmation
    if (!validasiWajibDiisi($password2)) {
        $errors['password2'] = 'Konfirmasi password tidak boleh kosong.';
    } elseif ($password !== $password2) {
        $errors['password2'] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = hash('sha256', $password);

        // Insert new user into pemustaka table
        $stmt = $pdo->prepare("INSERT INTO pemustaka (nama, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama, $email, $hashed_password])) {
            $_SESSION['success_message'] = 'Registrasi berhasil! Silakan login.';
            header('Location: ../login/index.php');
            exit();
        } else {
            $errors['general'] = 'Terjadi kesalahan saat registrasi. Mohon coba lagi.';
        }
        $stmt = null; // Close statement
    }

    // If there are errors, store them in session and redirect back to register page
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Keep form data to repopulate
        header('Location: ../register/index.php');
        exit();
    }
} else {
    // If accessed directly without POST request
    header('Location: ../register/index.php');
    exit();
}
?>