<?php
$pageTitle = "Login";
$cssFile = "/perpustakaan/assets/css/login.css";
include('../templates/header.php');
?>

<div class="form-container">
    <h2 class="page-title">Login Admin</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" >
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="text" id="password" name="password" >
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include('../templates/footer.php'); ?>