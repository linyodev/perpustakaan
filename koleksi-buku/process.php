<?php
require_once "../includes/validasi.php";
require_once "../includes/db_config.php";

try {
    $erros = [];
    $conn = get_db_connection();
    $id = isset($_POST['id']) ? trim($_POST['id']) : null;
    if (!$id) {
        $erros[] = "Id Tidak terdefinisi";
    }
    $id = sanitizeInput($id);
    $stmt = $conn->prepare("INSERT INTO ");
} catch (Exception $e) {
}
