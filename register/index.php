<?php

/*
|--------------------------------------------------------------------------
| Halaman Pendaftaran Anggota Baru
|--------------------------------------------------------------------------
|
| Halaman ini menampilkan formulir pendaftaran untuk pengguna baru.
| Jika ada kesalahan saat pengisian formulir (misalnya email sudah terdaftar),
| halaman ini akan menerima pesan error dari `process.php` dan menampilkannya
| kepada pengguna.
|
*/

// Mulai session agar kita bisa menerima pesan error atau data formulir
// dari halaman pemrosesan.
session_start();

// Siapkan variabel untuk dikirim ke template header.
$pageTitle = "Registrasi Anggota";
$cssFile = "/perpustakaan/assets/css/register.css";

// Sertakan bagian header halaman.
include('../templates/header.php');

// --- Ambil Pesan dari Session ---
// Setelah `process.php` selesai memvalidasi, ia akan mengirim kembali
// pesan error atau data yang sudah diisi melalui session. Kita ambil di sini.
$errors = $_SESSION['errors'] ?? []; // Ambil error, atau array kosong jika tidak ada.
$success_message = $_SESSION['success_message'] ?? ''; // Ambil pesan sukses.
$form_data = $_SESSION['form_data'] ?? []; // Ambil data yang sudah diisi pengguna.

// --- Bersihkan Session ---
// Setelah pesan dan data kita ambil, kita harus menghapusnya dari session
// agar tidak muncul lagi jika halaman di-refresh.
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);
?>

<!-- Ini adalah kontainer utama untuk formulir pendaftaran. -->
<div class="form-container">
    <h2 class="page-title">Register Anggota</h2>

    <!-- Tampilkan pesan sukses jika ada (misalnya, jika registrasi berhasil dan diarahkan ke login). -->
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Tampilkan pesan error umum jika ada. -->
    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>

    <!-- Formulir Pendaftaran -->
    <!-- Data akan dikirim ke 'process.php' menggunakan metode POST. -->
    <form action="process.php" method="POST">
    
        <!-- Bagian input untuk Nama Lengkap -->
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <!-- 
              Nilai (value) dari input diisi kembali dengan data yang sudah pernah
              diinput pengguna, jadi mereka tidak perlu mengetik ulang semuanya.
            -->
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($form_data['nama'] ?? ''); ?>" required>
            <!-- Tampilkan pesan error spesifik untuk field ini jika ada. -->
            <?php if (isset($errors['nama'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['nama']); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Bagian input untuk Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
            <?php if (isset($errors['email'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['email']); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Bagian input untuk Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($errors['password'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['password']); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Bagian input untuk konfirmasi password -->
        <div class="form-group">
            <label for="password2">Ulangi Password</label>
            <input type="password" id="password2" name="password2" required>
            <?php if (isset($errors['password2'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['password2']); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Tombol untuk mengirimkan formulir -->
        <button type="submit" class="btn">Register</button>
        
        <!-- Link untuk pengguna yang sudah punya akun -->
        <p class="form-link">Sudah punya akun? <a href="/perpustakaan/login/">Login di sini</a></p>
    </form>
</div>

<?php 
// Sertakan bagian footer halaman.
include('../templates/footer.php'); 
?>