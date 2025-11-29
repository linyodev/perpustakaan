<?php
if (!isset($_SESSION)) {
    session_start();
}
$pageTitle = "Login";
$cssFile = "/perpustakaan/assets/css/login.css";
include('../templates/header.php');
?>

<div class="form-container">
      <?php
        if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION['login_error']);
        }
        ?>
    <h2 class="page-title">Login Pemustaka</h2>
    <form action="process.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" >
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" >
        </div>
        <button type="submit" class="btn">Login</button>
        <p class="form-link">Belum punya akun? <a href="/perpustakaan/register/">Register di sini</a></p>
        <p class="form-link">Login sebagai Admin? <a href="/perpustakaan/admin/login.php">Login Admin</a></p>
    </form>
</div>

<?php include('../templates/footer.php'); ?>