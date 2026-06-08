<?php
// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $penghargaan = new penghargaan();
    if (isset($_POST['tambah'])) {
        $result = $penghargaan->create($_POST);
        header('Location: ?page=penghargaan&msg=' . ($result ? 'added' : 'error'));
        exit;
    } elseif (isset($_POST['edit'])) {
        $result = $penghargaan->update($_POST['id_penghargaan'], $_POST);
        header('Location: ?page=penghargaan&msg=' . ($result ? 'updated' : 'error'));
        exit;
    }
}
if (isset($_GET['hapus'])) {
    $penghargaan = new penghargaan();
    $penghargaan->delete($_GET['hapus']);
    header('Location: ?page=penghargaan&msg=deleted');
    exit;
}

// Ambil parameter URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.tgl_penghargaan';
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

$penghargaan = new penghargaan();
$totalData = $penghargaan->getTotalCount($search);
$totalPages = ($limit === 'all') ? 1 : ceil($totalData / $limitValue);

$startPage = max(1, $page - 1);
$endPage = min($totalPages, $startPage + 3);
if ($endPage - $startPage < 3 && $startPage > 1) {
    $startPage = max(1, $endPage - 3);
}

$data = $penghargaan->getData($search, $sort, $order, $limitValue, $offset);
$editData = isset($_GET['edit']) ? $penghargaan->getById($_GET['edit']) : null;

$showAddModal = isset($_GET['action']) && $_GET['action'] == 'tambah';
$showEditModal = ($editData !== null);
$showModal = $showAddModal || $showEditModal;

$msg = '';
$msgType = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':   $msg = 'Data penghargaan berhasil ditambahkan!'; $msgType = 'success'; break;
        case 'updated': $msg = 'Data penghargaan berhasil diupdate!'; $msgType = 'success'; break;
        case 'deleted': $msg = 'Data penghargaan berhasil dihapus!'; $msgType = 'success'; break;
        case 'error':   $msg = 'Terjadi kesalahan, silakan coba lagi.'; $msgType = 'error'; break;
    }
}

// Ambil data pegawai untuk dropdown
$pegawaiModel = new Pegawai();
$pegawaiList = $pegawaiModel->getAll();

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-exclamation-triangle"></i> Manajemen Penghargaan (MP)</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= $msg ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="penghargaan">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari nama pegawai, jenis MP, keterangan..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="?page=penghargaan" class="reset-btn">Reset</a>
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
                    <input type="hidden" name="page" value="penghargaan">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
                </form>
            </div>
            <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
                <a href="?page=penghargaan&action=tambah" class="btn-add"><i class="fas fa-plus"></i> Tambah MP</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=p.id_penghargaan&order=<?= $sort == 'p.id_penghargaan' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">ID SP <?= $sort == 'p.id_penghargaan' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=peg.nama&order=<?= $sort == 'peg.nama' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Pegawai <?= $sort == 'peg.nama' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=p.tgl_penghargaan&order=<?= $sort == 'p.tgl_penghargaan' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Tanggal <?= $sort == 'p.tgl_penghargaan' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=p.jenis&order=<?= $sort == 'p.jenis' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Jenis SP <?= $sort == 'p.jenis' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th>Keterangan</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= $row['id_penghargaan'] ?></td>
                        <td><?= htmlspecialchars($row['pegawai_nama']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tgl_penghargaan'])) ?></td>
                        <td>
                            <?php if ($row['jenis'] == 'MP1'): ?>
                                <span class="badge badge-warning">MP 1</span>
                            <?php elseif ($row['jenis'] == 'MP2'): ?>
                                <span class="badge badge-danger">MP 2</span>
                            <?php else: ?>
                                <span class="badge badge-dark">MP 3</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(substr($row['keterangan'], 0, 60)) . (strlen($row['keterangan']) > 60 ? '...' : '') ?></td>
                        <td class="actions">
                            <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
                                <a href="?page=penghargaan&edit=<?= $row['id_penghargaan'] ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn-delete" data-id="<?= $row['id_penghargaan'] ?>" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">Tidak ada data penghargaan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($limit !== 'all' && $totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page-1 ?>" class="page-link">« Prev</a>
        <?php endif; ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=penghargaan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page+1 ?>" class="page-link">Next »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah/Edit -->
<div id="modalForm" class="modal" style="display: <?= $showModal ? 'flex' : 'none' ?>;">
    <div class="modal-content" style="width: 550px;">
        <div class="modal-header">
            <h3><?= $showEditModal ? '<i class="fas fa-edit"></i> Edit Penghargaan' : '<i class="fas fa-plus-circle"></i> Tambah Penghargaan' ?></h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="post">
            <?php if ($showEditModal): ?>
                <input type="hidden" name="id_penghargaan" value="<?= $editData['id_penghargaan'] ?>">
                <input type="hidden" name="edit" value="1">
            <?php else: ?>
                <input type="hidden" name="tambah" value="1">
            <?php endif; ?>
            <div class="form-group">
                <label>Pegawai</label>
                <select name="id_pegawai" required>
                    <option value="">Pilih Pegawai</option>
                    <?php foreach ($pegawaiList as $peg): ?>
                        <option value="<?= $peg['id_pegawai'] ?>" <?= ($showEditModal && $editData['id_pegawai'] == $peg['id_pegawai']) ? 'selected' : '' ?>><?= htmlspecialchars($peg['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal penghargaan</label>
                <input type="date" name="tgl_penghargaan" value="<?= $showEditModal ? $editData['tgl_penghargaan'] : date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis MP</label>
                <select name="jenis" required>
                    <option value="SP1" <?= ($showEditModal && $editData['jenis'] == 'MP1') ? 'selected' : '' ?>>MP 1 (penghargaan Pertama)</option>
                    <option value="SP2" <?= ($showEditModal && $editData['jenis'] == 'MP2') ? 'selected' : '' ?>>MP 2 (penghargaan Kedua)</option>
                    <option value="SP3" <?= ($showEditModal && $editData['jenis'] == 'MP3') ? 'selected' : '' ?>>MP 3 (penghargaan Ketiga)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Keterangan / Alasan</label>
                <textarea name="keterangan" rows="4" required><?= $showEditModal ? htmlspecialchars($editData['keterangan']) : '' ?></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Batal</button>
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
        <p style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin menghapus data penghargaan ini? Data yang dihapus tidak dapat dipulihkan.</p>
        <div class="modal-footer">
            <button id="confirmDeleteBtn" class="btn-save" style="background: linear-gradient(135deg, #e74c3c, #c0392b);"><i class="fas fa-trash"></i> Hapus</button>
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()"><i class="fas fa-times"></i> Batal</button>
        </div>
    </div>
</div>

<style>
.badge { display: inline-block; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.badge-warning { background: rgba(243, 156, 18, 0.2); color: #f39c12; }
.badge-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
.badge-dark { background: rgba(209, 213, 219, 0.2); color: #d1d5db; }
/* Modal Form Styling menggunakan style.css global */
</style>

<script>
function closeModal() {
    const url = new URL(window.location.href);
    url.searchParams.delete('edit');
    url.searchParams.delete('action');
    window.location.href = url.toString();
}
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
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
        window.location.href = '?page=penghargaan&hapus=' + deleteId + '&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>';
    }
});
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        if (event.target.id === 'modalForm') {
            closeModal();
        } else {
            closeConfirmModal();
        }
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
