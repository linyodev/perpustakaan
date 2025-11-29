<?php
if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
if (isset($_SESSION['login_error'])) {
    echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') . '</div>';
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Perpustakaan Online</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="login-container">
        <h2>Login Administrator</h2>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>

</html>