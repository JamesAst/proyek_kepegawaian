<?php 
include __DIR__ . '/layouts/header.php'; 

$pegawai = new Pegawai();
$departemen = new Departemen();
$jabatan = new Jabatan();
$cuti = new Cuti();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .dashboard-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(52, 152, 219, 0.3);
    }
    .dashboard-header h2 {
        font-size: 2rem;
        color: #ecf0f1;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dashboard-header p {
        color: #bdc3c7;
        margin: 8px 0 0 0;
        font-size: 0.95rem;
    }
    .stat-card i {
        font-size: 2rem;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 10px;
    }
</style>

<div class="dashboard-header">
    <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
    <p>Selamat datang di sistem informasi kepegawaian</p>
</div>

<div class="dashboard-stats">
    <div class="stat-card">
        <i class="fas fa-users"></i>
        <h3><?= $pegawai->countAll() ?></h3>
        <p>Total Pegawai</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-building"></i>
        <h3><?= $departemen->countAll() ?></h3>
        <p>Departemen</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-briefcase"></i>
        <h3><?= $jabatan->countAll() ?></h3>
        <p>Jabatan</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-hourglass-half"></i>
        <h3><?= $cuti->countPending() ?></h3>
        <p>Cuti Pending</p>
    </div>
</div>

<div class="row">
    <div class="col-half">
        <div class="card">
            <h3><i class="fas fa-user-circle"></i> Pegawai Terbaru</h3>
            <table class="table-mini">
                <thead>
                    <tr><th><i class="fas fa-user"></i> Nama</th><th><i class="fas fa-building"></i> Departemen</th><th><i class="fas fa-tag"></i> Jabatan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pegawai->getLatest(5) as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= htmlspecialchars($p['departemen']) ?></td>
                        <td><?= htmlspecialchars($p['jabatan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($pegawai->countAll() == 0): ?>
                    <tr><td colspan="3" style="text-align: center; color: #95a5a6;"><i class="fas fa-inbox"></i> Belum ada data pegawai</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-half">
        <div class="card">
            <h3><i class="fas fa-calendar-alt"></i> Cuti Menunggu Persetujuan</h3>
            <table class="table-mini">
                <thead>
                    <tr><th><i class="fas fa-user-tie"></i> Pegawai</th><th><i class="fas fa-calendar-check"></i> Mulai</th><th><i class="fas fa-calendar-times"></i> Selesai</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cuti->getPendingList(5) as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['pegawai_nama']) ?></td>
                        <td><?= $c['tgl_mulai'] ?></td>
                        <td><?= $c['tgl_selesai'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($cuti->countPending() == 0): ?>
                    <tr><td colspan="3" style="text-align: center; color: #95a5a6;"><i class="fas fa-inbox"></i> Tidak ada cuti pending</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-info-circle"></i> Informasi Sistem</h3>
    <p style="margin: 0; line-height: 1.8; color: #bdc3c7;">
        Sistem Informasi Kepegawaian adalah platform terpadu untuk mengelola data pegawai, departemen, jabatan, izin, cuti, penghargaan, dan peringatan. 
        Gunakan menu sidebar untuk mengakses berbagai fitur dan fungsi sistem.
    </p>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
