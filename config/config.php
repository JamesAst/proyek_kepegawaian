<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_lat_hrd');
define('BASE_URL', 'http://localhost/proyek_kepegawaian/');

// --- TAMBAHKAN BARIS DI BAWAH INI ---
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$db) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Auto-Initialize Data Jabatan jika kosong
$checkJabatan = mysqli_query($db, "SELECT COUNT(*) as total FROM tbl_jabatan");
if ($checkJabatan) {
    $row = mysqli_fetch_assoc($checkJabatan);
    if ($row['total'] == 0) {
        mysqli_query($db, "INSERT INTO tbl_jabatan (id_jabatan, jabatan) VALUES 
            ('JBT001', 'Manager'),
            ('JBT002', 'HRD'),
            ('JBT003', 'Staff'),
            ('JBT004', 'Admin')");
    }
}
// ------------------------------------

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

require_once __DIR__ . '/../fpdf.php';
?>