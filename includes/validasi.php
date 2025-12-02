<?php
/**
 * @file
 * Pustaka fungsi untuk validasi dan sanitasi data.
 *
 * Berisi kumpulan fungsi yang dapat digunakan kembali untuk memastikan
 * data yang diterima dari pengguna aman dan sesuai format yang diharapkan.
 */

/**
 * Membersihkan string input untuk mencegah XSS.
 *
 * @param string $data Data input yang akan disanitasi.
 * @return string Data yang sudah bersih.
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Memeriksa apakah input tidak kosong.
 *
 * @param string $input
 * @return bool True jika tidak kosong.
 */
function validasiWajibDiisi($input) {
    return !empty(trim($input));
}

/**
 * Memeriksa apakah input hanya berisi huruf dan spasi.
 *
 * @param string $input
 * @return bool True jika valid.
 */
function validasiAlfabet($input) {
    return preg_match("/^[a-zA-Z\s]*$/", $input);
}

/**
 * Memeriksa apakah input hanya berisi huruf dan angka.
 *
 * @param string $input
 * @return bool True jika valid.
 */
function validasiAlfanumerik($input) {
    return preg_match("/^[a-zA-Z0-9]*$/", $input);
}

/**
 * Memeriksa apakah input adalah nilai numerik.
 *
 * @param mixed $input
 * @return bool True jika numerik.
 */
function validasiNumerik($input) {
    return is_numeric($input);
}

/**
 * Memvalidasi format alamat email.
 *
 * @param string $email
 * @return bool True jika format email valid.
 */
function validasiEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Memvalidasi format nomor telepon (hanya angka, panjang min/max).
 *
 * @param string $telefon Nomor telepon.
 * @param int $min Panjang minimal.
 * @param int $max Panjang maksimal.
 * @return bool True jika valid.
 */
function validasiTelepon($telefon, $min = 10, $max = 13) {
    return preg_match("/^[0-9]{{$min},{$max}}$/", $telefon);
}

/**
 * Memeriksa panjang minimal string.
 *
 * @param string $input
 * @param int $min Panjang minimal yang diizinkan.
 * @return bool True jika panjang sesuai.
 */
function validasiPanjangMin($input, $min) {
    return strlen($input) >= $min;
}

/**
 * Memeriksa panjang maksimal string.
 *
 * @param string $input
 * @param int $max Panjang maksimal yang diizinkan.
 * @return bool True jika panjang sesuai.
 */
function validasiPanjangMax($input, $max) {
    return strlen($input) <= $max;
}

/**
 * Memeriksa apakah input adalah angka dengan panjang digit yang tepat.
 *
 * @param string $input
 * @param int $digit Jumlah digit yang diharapkan.
 * @return bool True jika valid.
 */
function validasiPanjangDigit($input, $digit) {
    return preg_match("/^[0-9]{{$digit}}$/", $input);
}

/**
 * Memvalidasi nama (membolehkan huruf, spasi, dan apostrof).
 *
 * @param string $nama
 * @return bool True jika valid.
 */
function validasiNama($nama) {
    return preg_match("/^[a-zA-Z\s']*$/", $nama);
}

/**
 * Memvalidasi alamat (karakter alfanumerik dan beberapa simbol umum).
 *
 * @param string $alamat
 * @return bool True jika valid.
 */
function validasiAlamat($alamat) {
    return preg_match("/^[a-zA-Z0-9\s,.-]*$/", $alamat);
}

/**
 * Memvalidasi kekuatan password (hanya berdasarkan panjang minimal).
 *
 * @param string $password
 * @param int $min Panjang minimal.
 * @return bool True jika valid.
 */
function validasiPassword($password, $min = 8) {
    return strlen($password) >= $min;
}

/**
 * Memvalidasi format tanggal YYYY-MM-DD.
 *
 * @param string $tanggal
 * @return bool True jika valid.
 */
function validasiTanggal($tanggal) {
    $d = DateTime::createFromFormat('Y-m-d', $tanggal);
    return $d && $d->format('Y-m-d') === $tanggal;
}

/**
 * Memvalidasi format tahun (4 digit angka).
 *
 * @param string $tahun
 * @return bool True jika valid.
 */
function validasiTahun($tahun) {
    return preg_match("/^[0-9]{4}$/", $tahun);
}

/**
 * Memeriksa apakah nilai adalah angka dan lebih besar dari nol.
 *
 * @param mixed $jumlah
 * @return bool True jika valid.
 */
function validasiJumlahPositif($jumlah) {
    // is_numeric digunakan agar '123' dianggap valid.
    return is_numeric($jumlah) && $jumlah >= 0;
}

/**
 * Memeriksa apakah string mengandung tag HTML.
 *
 * @param string $input String yang akan diperiksa.
 * @return bool True jika tidak ada tag HTML, False jika ada.
 */
function validasiTanpaHtml($input) {
    return strip_tags($input) === $input;
}

?>
