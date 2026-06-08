<?php
require_once __DIR__ . '/../../config/config.php';

$penghargaan = new Penghargaan();
$usaha = new Usaha();
$dataUsaha = $usaha->getData();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$data = $penghargaan->getAllData($search);

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Kop surat
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
    $pdf->Cell(0, 8, 'LAPORAN PENGHARGAAN PEGAWAI', 0, 1, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN PENGHARGAAN PEGAWAI', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 7, 'ID Penghargaan', 1);
$pdf->Cell(50, 7, 'Pegawai', 1);
$pdf->Cell(30, 7, 'Tanggal', 1);
$pdf->Cell(60, 7, 'Nama Penghargaan', 1);
$pdf->Cell(70, 7, 'Keterangan', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($data as $row) {
    $pdf->Cell(30, 6, $row['id_penghargaan'], 1);
    $pdf->Cell(50, 6, $row['pegawai_nama'], 1);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($row['tgl_penghargaan'])), 1);
    $pdf->Cell(60, 6, $row['nama_penghargaan'], 1);
    $pdf->Cell(70, 6, $row['keterangan'], 1);
    $pdf->Ln();
}

$pdf->Output('I', 'laporan_penghargaan.pdf');
exit;
?>
