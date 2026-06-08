<?php
// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cuti = new Cuti();
    if (isset($_POST['tambah'])) {
        $result = $cuti->create($_POST);
        if ($result['status']) {
            header('Location: ?page=cuti&msg=added');
        } else {
            header('Location: ?page=cuti&msg=error&error=' . urlencode($result['message']));
        }
        exit;
    } elseif (isset($_POST['update_status'])) {
        $cuti->updateStatus($_POST['id_cuti'], $_POST['status']);
        header('Location: ?page=cuti&msg=updated');
        exit;
    }
}
if (isset($_GET['hapus'])) {
    $cuti = new Cuti();
    $cuti->delete($_GET['hapus']);
    header('Location: ?page=cuti&msg=deleted');
    exit;
}

// Ambil parameter URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'c.created_at';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc';
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;

$allowedLimits = [10, 25, 50, 100, 'all'];
if (!in_array($limit, $allowedLimits)) $limit = 10;

if ($limit === 'all') {
    $limitValue = 999999;
    $offset = 0;
} else {
    $limitValue = (int)$limit;
    $offset = ($page - 1) * $limitValue;
}

// Filter data jika role adalah staff
$filterId = !in_array(Session::get('role'), ['hrd', 'manager', 'admin']) ? Session::get('user_id') : null;

$cuti = new Cuti();
$totalData = $cuti->getTotalCount($search, $filterId);
$totalPages = ($limit === 'all') ? 1 : ceil($totalData / $limitValue);

$startPage = max(1, $page - 1);
$endPage = min($totalPages, $startPage + 3);
if ($endPage - $startPage < 3 && $startPage > 1) {
    $startPage = max(1, $endPage - 3);
}

$data = $cuti->getData($search, $sort, $order, $limitValue, $offset, $filterId);

// Data untuk dropdown pegawai (hanya yang aktif dan masih punya cuti)
$pegawaiList = [];
if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])) {
    $pegawaiModel = new Pegawai();
    $pegawaiList = $pegawaiModel->getAll();
}

$showAddModal = isset($_GET['action']) && $_GET['action'] == 'tambah';

$msg = '';
$msgType = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':   $msg = 'Pengajuan cuti berhasil!'; $msgType = 'success'; break;
        case 'updated': $msg = 'Status cuti berhasil diupdate!'; $msgType = 'success'; break;
        case 'deleted': $msg = 'Data cuti berhasil dihapus!'; $msgType = 'success'; break;
        case 'error':   $msg = isset($_GET['error']) ? urldecode($_GET['error']) : 'Terjadi kesalahan'; $msgType = 'error'; break;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-umbrella-beach"></i> Manajemen Cuti</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= $msg ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="cuti">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari nama pegawai atau status..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="?page=cuti" class="reset-btn">Reset</a>
            <?php endif; ?>
        </form>
        <div class="filter-add">
            <div class="filter-form">
                <span>Tampilkan:</span>
                <select name="limit" onchange="this.form.submit()" form="filterForm">
                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    <option value="all" <?= $limit == 'all' ? 'selected' : '' ?>>Semua</option>
                </select>
                <form id="filterForm" method="get" style="display: none;">
                    <input type="hidden" name="page" value="cuti">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
                </form>
            </div>
            <a href="?page=cuti&action=tambah" class="btn-add"><i class="fas fa-plus"></i> Ajukan Cuti</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><a href="?page=cuti&search=<?= urlencode($search) ?>&sort=c.id_cuti&order=<?= $sort == 'c.id_cuti' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">ID <?= $sort == 'c.id_cuti' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=cuti&search=<?= urlencode($search) ?>&sort=p.nama&order=<?= $sort == 'p.nama' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Pegawai <?= $sort == 'p.nama' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=cuti&search=<?= urlencode($search) ?>&sort=c.tgl_mulai&order=<?= $sort == 'c.tgl_mulai' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Tanggal Mulai <?= $sort == 'c.tgl_mulai' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=cuti&search=<?= urlencode($search) ?>&sort=c.tgl_selesai&order=<?= $sort == 'c.tgl_selesai' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Tanggal Selesai <?= $sort == 'c.tgl_selesai' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th>Jumlah Hari</th>
                    <th><a href="?page=cuti&search=<?= urlencode($search) ?>&sort=c.status&order=<?= $sort == 'c.status' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Status <?= $sort == 'c.status' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): 
                        $start = new DateTime($row['tgl_mulai']);
                        $end = new DateTime($row['tgl_selesai']);
                        $end->modify('+1 day');
                        $hari = $start->diff($end)->days;
                    ?>
                    <tr>
                        <td><?= $row['id_cuti'] ?></td>
                        <td><?= htmlspecialchars($row['pegawai_nama']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tgl_mulai'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tgl_selesai'])) ?></td>
                        <td><?= $hari ?> hari</td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="badge badge-warning">Pending</span>
                            <?php elseif ($row['status'] == 'disetujui'): ?>
                                <span class="badge badge-disetujui">Disetujui</span>
                            <?php else: ?>
                                <span class="badge badge-ditolak">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions text-center">
                            <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <button class="btn-status btn-approve" data-id="<?= $row['id_cuti'] ?>" data-status="disetujui" title="Setujui"><i class="fas fa-check"></i></button>
                                    <button class="btn-status btn-reject" data-id="<?= $row['id_cuti'] ?>" data-status="ditolak" title="Tolak"><i class="fas fa-times"></i></button>
                                <?php endif; ?>
                                <button class="btn-delete" data-id="<?= $row['id_cuti'] ?>" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">Tidak ada data cuti</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($limit !== 'all' && $totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=cuti&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page-1 ?>" class="page-link">« Prev</a>
        <?php endif; ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=cuti&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=cuti&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page+1 ?>" class="page-link">Next »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Cuti -->
<div id="modalForm" class="modal" style="display: <?= $showAddModal ? 'flex' : 'none' ?>;">
    <div class="modal-content" style="width: 550px;">
        <div class="modal-header">
            <h3><i class="fas fa-calendar-plus"></i> Form Pengajuan Cuti</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="post" style="margin-top: 20px;">
            <input type="hidden" name="tambah" value="1">
            <div class="form-group">
                <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
                    <label>Pilih Pegawai</label>
                    <select name="id_pegawai" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($pegawaiList as $peg): ?>
                            <option value="<?= $peg['id_pegawai'] ?>"><?= htmlspecialchars($peg['nama']) ?> (Sisa cuti: <?= $peg['jumlah_cuti'] ?> hari)</option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <label>Nama Pengaju</label>
                    <input type="text" value="<?= htmlspecialchars(Session::get('nama')) ?>" readonly style="background: rgba(255,255,255,0.05); color: #95a5a6;">
                    <input type="hidden" name="id_pegawai" value="<?= Session::get('user_id') ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" required>
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" required>
            </div>
            <div class="form-group">
                <label>Alasan Cuti</label>
                <textarea name="alasan" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save" style="flex: 2;"><i class="fas fa-paper-plane"></i> Ajukan Cuti</button>
                <button type="button" class="btn-cancel" onclick="closeModal()" style="flex: 1;"><i class="fas fa-times"></i> Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 350px;">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-circle"></i> Konfirmasi Hapus</h3>
            <span class="close" onclick="closeConfirmModal()">&times;</span>
        </div>
        <p style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin menghapus data cuti ini?</p>
        <div class="modal-footer">
            <button id="confirmDeleteBtn" class="btn-save" style="background: linear-gradient(135deg, #e74c3c, #c0392b);"><i class="fas fa-trash"></i> Hapus</button>
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()">Batal</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Status -->
<div id="statusModal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 350px;">
        <div class="modal-header">
            <h3><i class="fas fa-question-circle"></i> Konfirmasi Status</h3>
            <span class="close" onclick="closeStatusModal()">&times;</span>
        </div>
        <p id="statusText" style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin mengubah status ini?</p>
        <div class="modal-footer">
            <button id="confirmStatusBtn" class="btn-save" style="background: linear-gradient(135deg, #3498db, #2980b9);">Ya, Lanjutkan</button>
            <button type="button" class="btn-cancel" onclick="closeStatusModal()">Batal</button>
        </div>
    </div>
</div>

<!-- Modal untuk update status (form tersembunyi) -->
<form id="statusForm" method="post" style="display: none;">
    <input type="hidden" name="update_status" value="1">
    <input type="hidden" name="id_cuti" id="status_id">
    <input type="hidden" name="status" id="status_value">
</form>

<style>
.badge { display: inline-block; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.badge-warning { background: rgba(243, 156, 18, 0.2); color: #f39c12; }
.badge-success { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
.badge-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
.btn-status { background: none; border: none; cursor: pointer; margin: 0 5px; font-size: 18px; }
/* Menggunakan style global dari assets/css/style.css */
</style>

<script>
function closeModal() {
    const url = new URL(window.location.href);
    url.searchParams.delete('action');
    window.location.href = url.toString();
}
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}
let deleteId = null;
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        deleteId = this.getAttribute('data-id');
        document.getElementById('confirmModal').style.display = 'flex';
    });
});
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteId) {
        window.location.href = '?page=cuti&hapus=' + deleteId + '&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>';
    }
});

// Update status
let targetStatusId = null;
let targetStatusVal = null;
document.querySelectorAll('.btn-status').forEach(btn => {
    btn.addEventListener('click', function() {
        targetStatusId = this.getAttribute('data-id');
        targetStatusVal = this.getAttribute('data-status');
        
        const text = targetStatusVal === 'disetujui' ? 'menyetujui' : 'menolak';
        document.getElementById('statusText').innerText = `Apakah Anda yakin ingin ${text} pengajuan cuti ini?`;
        document.getElementById('statusModal').style.display = 'flex';
    });
});

document.getElementById('confirmStatusBtn').addEventListener('click', function() {
    if (targetStatusId && targetStatusVal) {
        document.getElementById('status_id').value = targetStatusId;
        document.getElementById('status_value').value = targetStatusVal;
        document.getElementById('statusForm').submit();
    }
});
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        if (event.target.id === 'modalForm') {
            closeModal();
        } else if (event.target.id === 'statusModal') {
            closeStatusModal();
        } else {
            closeConfirmModal();
        }
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
