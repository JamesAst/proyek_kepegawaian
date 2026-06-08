<?php
require_once 'config/config.php';

$page = $_GET['page'] ?? 'login';
$auth = new User();
if ($page != 'login' && !$auth->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

$role = Session::get('role');
$master_pages = ['usaha', 'departemen', 'jabatan', 'pegawai']; // Halaman-halaman yang termasuk dalam kategori "Master"
if (in_array($page, $master_pages) && !in_array($role, ['hrd', 'manager', 'admin'])) {
    header('Location: index.php?page=dashboard');
    exit;
}

switch ($page) {
    //memanggil login, register dan dashboard
    case 'login': include 'views/login.php'; break;
    case 'dashboard': include 'views/dashboard.php'; break;

    //memanggil file master
    case 'usaha': $obj = new Usaha(); include 'views/usaha/index.php'; break;
    case 'departemen': $obj = new Departemen(); include 'views/departemen/index.php'; break;
    case 'jabatan': $obj = new Jabatan(); include 'views/jabatan/index.php'; break;
    case 'pegawai': $obj = new Pegawai(); include 'views/pegawai/index.php'; break;

    //memanggil file transaksi
    case 'cuti': $obj = new Cuti(); include 'views/cuti/index.php'; break;
    case 'izin': $obj = new Izin(); include 'views/izin/index.php'; break;
    case 'peringatan': $obj = new Peringatan(); include 'views/peringatan/index.php'; break;
    case 'penghargaan': $obj = new Penghargaan(); include 'views/penghargaan/index.php'; break;

    // Papan Pengumuman
    case 'pengumuman': include 'views/pengumuman/index.php'; break;

    //memanggil file report
    case 'report_pegawai': include 'views/report/print_pegawai.php'; break;
    case 'report_sp': include 'views/report/print_sp.php'; break;
    case 'report_cuti': include 'views/report/print_cuti.php'; break;
    case 'report_izin': include 'views/report/print_izin.php'; break;
    case 'report_penghargaan': include 'views/report/print_penghargaan.php'; break;

    //memanggil setting dan logout
    case 'setting': include 'views/setting/index.php'; break;
    case 'logout': $auth->logout(); header('Location: index.php?page=login'); break;
    default: include 'views/404.php';
}
?>