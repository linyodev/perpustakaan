<?php
session_start();
$pageTitle = "Login";
$cssFile = "/perpustakaan/assets/css/login.css";
include('../templates/header.php');

$errors = $_SESSION['errors'] ?? [];
$success_message = $_SESSION['success_message'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];


unset($_SESSION['errors']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);
?>

<div class="form-container">
    <h2 class="page-title">Register Anggota</h2>
    <?php if ($success_message): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($errors['general'])): ?>
        <p class="error-message"><?php echo $errors['general']; ?></p>
    <?php endif; ?>
    <form action="process.php" method="POST">
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($form_data['nama'] ?? ''); ?>">
            <?php if (isset($errors['nama'])): ?>
                <p class="error-message"><?php echo $errors['nama']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
            <?php if (isset($errors['email'])): ?>
                <p class="error-message"><?php echo $errors['email']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <?php if (isset($errors['password'])): ?>
                <p class="error-message"><?php echo $errors['password']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password2">Retype Password</label>
            <input type="password" id="password2" name="password2">
            <?php if (isset($errors['password2'])): ?>
                <p class="error-message"><?php echo $errors['password2']; ?></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn">Register</button>
        <p class="form-link">Sudah punya akun? <a href="/perpustakaan/login/">Login di sini</a></p>
    </form>
</div>

<?php include('../templates/footer.php'); ?>