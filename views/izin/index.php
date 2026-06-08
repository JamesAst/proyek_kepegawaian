<?php
$izin = new izin();

// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $izin->create($_POST);
        header('Location: ?page=izin&msg=added'); exit;
    } elseif (isset($_POST['update_status'])) {
        $izin->updateStatus($_POST['id_izin'], $_POST['status']);
        header('Location: ?page=izin&msg=updated'); exit;
    }
}
if (isset($_GET['hapus'])) {
    $izin->delete($_GET['hapus']);
    header('Location: ?page=izin&msg=deleted'); exit;
}

// Navigasi & Data
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page_num'] ?? 1));

$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
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

$data = $izin->getData($search, $limitValue, $offset, $filterId);
$totalData = $izin->getTotalCount($search, $filterId);
$totalPages = ($limit === 'all') ? 1 : ceil($totalData / $limitValue);

// Data untuk dropdown pegawai hanya jika role berwenang
$pegawaiList = [];
if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])) {
    $pegawaiModel = new Pegawai();
    $pegawaiList = $pegawaiModel->getAll();
}

$msg = '';
$msgType = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':   $msg = 'Pengajuan izin berhasil!'; $msgType = 'success'; break;
        case 'updated': $msg = 'Status izin berhasil diupdate!'; $msgType = 'success'; break;
        case 'deleted': $msg = 'Data izin berhasil dihapus!'; $msgType = 'success'; break;
        case 'error':   $msg = isset($_GET['error']) ? urldecode($_GET['error']) : 'Terjadi kesalahan'; $msgType = 'error'; break;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-envelope-open-text"></i> Manajemen Izin Pegawai</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= $msg ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="izin">
            <input type="hidden" name="sort" value="c.created_at"> <!-- Default sort for consistency -->
            <input type="hidden" name="order" value="desc"> <!-- Default order for consistency -->
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari nama pegawai atau status..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="?page=izin" class="reset-btn">Reset</a>
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
                    <input type="hidden" name="page" value="izin">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="c.created_at">
                    <input type="hidden" name="order" value="desc">
                </form>
            </div>
            <a href="?page=izin&action=tambah" class="btn-add"><i class="fas fa-plus"></i> Tambah Pengajuan</a>
        </div>
    </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Izin</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal Izin</th>
                        <th>Alasan / Keperluan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data) > 0): ?>
                        <?php foreach ($data as $row): ?>
                        <tr>
                            <td><span class="id-badge"><?= $row['id_izin'] ?></span></td>
                            <td><strong><?= htmlspecialchars($row['pegawai_nama']) ?></strong></td>
                            <td><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['tgl_izin'])) ?></td>
                            <td><span class="text-muted"><?= htmlspecialchars($row['alasan']) ?></span></td>
                            <td>
                                <span class="badge badge-<?= $row['status'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="actions text-center">
                                <?php if (in_array(Session::get('role'), ['hrd', 'manager'])): ?>
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <button onclick="confirmStatus('<?= $row['id_izin'] ?>', 'disetujui')" class="btn-approve" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="confirmStatus('<?= $row['id_izin'] ?>', 'ditolak')" class="btn-reject" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="confirmDelete('<?= $row['id_izin'] ?>')" class="btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=izin&page_num=<?= $i ?>&search=<?= urlencode($search) ?>&limit=<?= $limit ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['action']) && $_GET['action'] == 'tambah'): ?>
<div class="modal" style="display: flex;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-paper-plane"></i> Form Pengajuan Izin</h3>
            <a href="?page=izin" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #95a5a6; text-decoration: none;">&times;</a>
        </div>
        <form method="post">
            <input type="hidden" name="tambah" value="1">
            <div class="form-group">
                <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
                    <label>Pilih Pegawai</label>
                    <select name="id_pegawai" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($pegawaiList as $peg): ?>
                            <option value="<?= $peg['id_pegawai'] ?>"><?= htmlspecialchars($peg['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <label>Nama Pengaju</label>
                    <input type="text" value="<?= htmlspecialchars(Session::get('nama')) ?>" readonly style="background: rgba(255,255,255,0.05); color: #95a5a6;">
                    <input type="hidden" name="id_pegawai" value="<?= Session::get('user_id') ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Tanggal Izin</label>
                <input type="date" name="tgl_izin" required>
            </div>
            <div class="form-group">
                <label>Alasan Izin</label>
                <textarea name="alasan" placeholder="Contoh: Sakit, Urusan Keluarga, dll." rows="4" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save"><i class="fas fa-send"></i> Kirim Pengajuan</button>
                <a href="?page=izin" class="btn-cancel" style="display: flex; align-items: center; justify-content: center;"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal Konfirmasi Hapus -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 350px;">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-circle"></i> Konfirmasi Hapus</h3>
            <span class="close" onclick="closeConfirmModal()">&times;</span>
        </div>
        <p style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin menghapus data izin ini? Data yang dihapus tidak dapat dipulihkan.</p>
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

<form id="actionForm" method="post" style="display:none;">
    <input type="hidden" name="update_status" value="1">
    <input type="hidden" name="id_izin" id="action_id">
    <input type="hidden" name="status" id="action_val">
</form>

<style> /* Hanya menyisakan style yang unik untuk halaman ini */
.id-badge { background: rgba(52, 152, 219, 0.2); color: #3498db; padding: 4px 8px; border-radius: 6px; font-weight: bold; font-size: 13px; }
</style>

<script>
let targetStatusId = null;
let targetStatusVal = null;

function confirmStatus(id, status) {
    targetStatusId = id;
    targetStatusVal = status;
    const text = status === 'disetujui' ? 'menyetujui' : 'menolak';
    document.getElementById('statusText').innerText = `Apakah Anda yakin ingin ${text} pengajuan izin ini?`;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

document.getElementById('confirmStatusBtn').addEventListener('click', function() {
    if (targetStatusId && targetStatusVal) {
        document.getElementById('action_id').value = targetStatusId;
        document.getElementById('action_val').value = targetStatusVal;
        document.getElementById('actionForm').submit();
    }
});

let deleteId = null;
function confirmDelete(id) {
    deleteId = id;
    document.getElementById('confirmModal').style.display = 'flex';
}
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteId) {
        window.location.href = '?page=izin&hapus=' + deleteId;
    }
});
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        if (event.target.id === 'statusModal') {
            closeStatusModal();
        } else if (event.target.id === 'confirmModal') {
            closeConfirmModal();
        } else {
            event.target.style.display = 'none';
        }
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>