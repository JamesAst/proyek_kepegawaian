<?php
require_once __DIR__ . '/../../config/config.php';

$jabatan = new jabatan();
$usaha = new Usaha();
$dataUsaha = $usaha->getData();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$data = $jabatan->getAllData($search);

// Buat PDF dengan orientasi Portrait (P)
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Kop surat (jika ada data usaha)
if ($dataUsaha) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, strtoupper($dataUsaha['nama']), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $dataUsaha['alamat'], 0, 1, 'C');
    $alamat2 = "Telp: " . $dataUsaha['nomor_telepon'] . " | Fax: " . $dataUsaha['fax'] . " | Email: " . $dataUsaha['email'];
    $pdf->Cell(0, 5, $alamat2, 0, 1, 'C');
    $pdf->Ln(3);
    $pdf->Cell(0, 0, '', 'B', 1);
    $pdf->Ln(5);
} else {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, 'LAPORAN DATA JABATAN', 0, 1, 'C');
    $pdf->Ln(5);
}

// Judul laporan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN DATA JABATAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// Header tabel (lebar disesuaikan untuk portrait: total 190mm)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(45, 8, 'Kode Jabatan', 1);
$pdf->Cell(100, 8, 'Nama Jabatan', 1);
$pdf->Cell(45, 8, 'Tanggal Dibuat', 1);
$pdf->Ln();

// Isi data
$pdf->SetFont('Arial', '', 10);
foreach ($data as $row) {
    $pdf->Cell(45, 7, $row['id_jabatan'], 1);
    $pdf->Cell(100, 7, $row['jabatan'], 1);
    $pdf->Cell(45, 7, date('d-m-Y', strtotime($row['created_at'])), 1);
    $pdf->Ln();
}

// Output preview di browser (I = inline, langsung tampil)
$pdf->Output('I', 'data_jabatan.pdf');
exit;
?>
