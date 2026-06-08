<?php
require_once __DIR__ . '/../config/config.php';

try {
    // 1. Seed Departemen
    $deptModel = new Departemen();
    if ($deptModel->getTotalCount() == 0) {
        $depts = ['Teknologi Informasi', 'Human Resources', 'Pemasaran', 'Produksi'];
        foreach ($depts as $d) {
            $deptModel->create(['departemen' => $d]);
        }
        echo "Data Departemen berhasil ditambahkan.<br>";
    }

    // 2. Seed Jabatan
    $jabModel = new Jabatan();
    if ($jabModel->getTotalCount() == 0) {
        // Menyesuaikan dengan role yang ada di register: Manager, HRD, Staff, Admin
        $jabs = ['Manager', 'HRD', 'Staff', 'Admin'];
        foreach ($jabs as $j) {
            $jabModel->create(['jabatan' => $j]);
        }
        echo "Data Jabatan (Manager, HRD, Staff, Admin) berhasil ditambahkan.<br>";
    }

    // 3. Seed Profil Perusahaan (Usaha)
    $usahaModel = new Usaha();
    if (!$usahaModel->hasData()) {
        $dataUsaha = [
            'nama' => 'PT. Maju Sendirian',
            'alamat' => 'Jl. jalanin aja dulu',
            'nomor_telepon' => '021-5551234',
            'fax' => '021-5554321',
            'email' => 'MAJUSENDIRIAN@gmail.com',
            'npwp' => '01.234.567.8-012.000',
            'bank' => 'Bank Mandiri',
            'noaccount' => '1234567890',
            'atasnama' => 'PT. Maju Sendirian',
            'pimpinan' => 'Alucard Feeder, PhD'
        ];
        $usahaModel->insert($dataUsaha);
        echo "Data Profil Perusahaan berhasil ditambahkan.<br>";
    }

    echo "<strong>Proses Seeding Selesai!</strong> <a href='../index.php'>Kembali ke Dashboard</a>";
} catch (Exception $e) {
    echo "<strong>Gagal menjalankan seeding:</strong> " . $e->getMessage() . "<br>";
    echo "Pastikan database 'db_lat_hrd' dan tabel-tabelnya sudah dibuat di phpMyAdmin.";
}
?>