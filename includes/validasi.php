<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validasiWajibDiisi($input) {
    return !empty(trim($input));
}

function validasiAlfabet($input) {
    return preg_match("/^[a-zA-Z\s]*$/", $input);
}

function validasiAlfanumerik($input) {
    return preg_match("/^[a-zA-Z0-9]*$/", $input);
}

function validasiNumerik($input) {
    return is_numeric($input);
}

function validasiEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validasiTelepon($telefon, $min = 10, $max = 13) {
    return preg_match("/^[0-9]{{$min},{$max}}$/", $telefon);
}

function validasiPanjangMin($input, $min) {
    return strlen($input) >= $min;
}

function validasiPanjangMax($input, $max) {
    return strlen($input) <= $max;
}

function validasiPanjangDigit($input, $digit) {
    return preg_match("/^[0-9]{{$digit}}$/", $input);
}

function validasiNama($nama) {
    return preg_match("/^[a-zA-Z\s']*$/", $nama);
}

function validasiAlamat($alamat) {
    return preg_match("/^[a-zA-Z0-9\s,.-\/]*$/", $alamat);
}

function validasiPassword($password, $min = 6) {
    if (strlen($password) < $min) {
        return false;
    }
    return true;
}

function validasiTanggal($tanggal) {
    $d = DateTime::createFromFormat('Y-m-d', $tanggal);
    return $d && $d->format('Y-m-d') === $tanggal;
}

function validasiTahun($tahun) {
    return preg_match("/^[0-9]{4}$/", $tahun);
}

function validasiJumlahPositif($jumlah) {
    return is_numeric($jumlah) && $jumlah > 0;
}
?>