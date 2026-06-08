<?php
require_once __DIR__ . '/../../config/config.php';

$pegawai = new Pegawai();
$usaha = new Usaha();
$dataUsaha = $usaha->getData();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$data = $pegawai->getAllData($search);

$pdf = new FPDF('L', 'mm', 'A4'); // Landscape karena banyak kolom
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
    $pdf->Cell(0, 8, 'LAPORAN DATA PEGAWAI', 0, 1, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'LAPORAN DATA PEGAWAI', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Tanggal cetak: ' . date('d-m-Y H:i:s'), 0, 1, 'R');
$pdf->Ln(5);

// Header tabel (lebar total 270mm)
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 7, 'ID Pegawai', 1);
$pdf->Cell(40, 7, 'Nama', 1);
$pdf->Cell(35, 7, 'Departemen', 1);
$pdf->Cell(35, 7, 'Jabatan', 1);
$pdf->Cell(30, 7, 'Status Kerja', 1);
$pdf->Cell(25, 7, 'Jenis Kelamin', 1);
$pdf->Cell(25, 7, 'Pendidikan', 1);
$pdf->Cell(30, 7, 'Tgl Mulai', 1);
$pdf->Cell(25, 7, 'Gaji', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($data as $row) {
    $pdf->Cell(25, 6, $row['id_pegawai'], 1);
    $pdf->Cell(40, 6, $row['nama'], 1);
    $pdf->Cell(35, 6, $row['departemen'], 1);
    $pdf->Cell(35, 6, $row['jabatan'], 1);
    $pdf->Cell(30, 6, $row['status_kerja'], 1);
    $pdf->Cell(25, 6, $row['jenis_kelamin'], 1);
    $pdf->Cell(25, 6, $row['jenjang_pendidikan'], 1);
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($row['tgl_mulai_kerja'])), 1);
    $pdf->Cell(25, 6, number_format($row['gaji'], 0, ',', '.'), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'data_pegawai.pdf');
exit;
?>
