<?php
require_once __DIR__ . '/../../config/config.php';

$peringatan = new Peringatan();
$usaha = new Usaha();
$dataUsaha = $usaha->getData();
$data = $peringatan->getAll(); // menggunakan method getAll() dari class Peringatan

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
    $pdf->Cell(0, 8, 'LAPORAN SURAT PERINGATAN (SP)', 0, 1, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN SURAT PERINGATAN (SP)', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, 'ID SP', 1);
$pdf->Cell(50, 7, 'Pegawai', 1);
$pdf->Cell(30, 7, 'Tanggal SP', 1);
$pdf->Cell(25, 7, 'Jenis', 1);
$pdf->Cell(80, 7, 'Keterangan', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($data as $row) {
    $pdf->Cell(35, 6, $row['id_peringatan'], 1);
    $pdf->Cell(50, 6, $row['pegawai_nama'], 1);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($row['tgl_peringatan'])), 1);
    $pdf->Cell(25, 6, $row['jenis'], 1);
    $pdf->Cell(80, 6, $row['keterangan'], 1);
    $pdf->Ln();
}

$pdf->Output('I', 'laporan_sp.pdf');
exit;
?>
