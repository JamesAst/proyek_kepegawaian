<?php
require_once __DIR__ . '/../../config/config.php';

$izin = new Izin();
$usaha = new Usaha();
$dataUsaha = $usaha->getData();
$search = $_GET['search'] ?? '';
$data = $izin->getAllData($search);

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
    $pdf->Cell(0, 8, 'LAPORAN IZIN PEGAWAI', 0, 1, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN IZIN PEGAWAI', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 7, 'ID Izin', 1);
$pdf->Cell(50, 7, 'Pegawai', 1);
$pdf->Cell(30, 7, 'Tanggal Izin', 1);
$pdf->Cell(80, 7, 'Alasan', 1);
$pdf->Cell(30, 7, 'Status', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($data as $row) {
    $pdf->Cell(30, 6, $row['id_izin'], 1);
    $pdf->Cell(50, 6, $row['pegawai_nama'], 1);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($row['tgl_izin'])), 1);
    $pdf->Cell(80, 6, $row['alasan'], 1);
    $status = $row['status'] == 'pending' ? 'Pending' : ($row['status'] == 'disetujui' ? 'Disetujui' : 'Ditolak');
    $pdf->Cell(30, 6, $status, 1);
    $pdf->Ln();
}

$pdf->Output('I', 'laporan_izin.pdf');
exit;
?>
