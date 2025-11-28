<?php
session_start();
if (!isset($_ENV['APP_URL'])) {
    $_ENV['APP_URL'] = "/perpustakaan";
}
if (!isset($_SESSION['user_login'])) {
    header("Location: ".$_ENV['APP_URL']."/login");
    exit();
}