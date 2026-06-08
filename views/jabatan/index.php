<?php
// Proses CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jabatan = new jabatan();
    if (isset($_POST['tambah'])) {
        $jabatan->create($_POST);
        header('Location: ?page=jabatan&msg=added');
        exit;
    } elseif (isset($_POST['edit'])) {
        $jabatan->update($_POST['id_jabatan'], $_POST);
        header('Location: ?page=jabatan&msg=updated');
        exit;
    }
}
if (isset($_GET['hapus'])) {
    $jabatan = new jabatan();
    $jabatan->delete($_GET['hapus']);
    header('Location: ?page=jabatan&msg=deleted');
    exit;
}

// Ambil parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_jabatan';
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

$jabatan = new jabatan();
$totalData = $jabatan->getTotalCount($search);
$totalPages = ($limit === 'all') ? 1 : ceil($totalData / $limitValue);

$startPage = max(1, $page - 1);
$endPage = min($totalPages, $startPage + 3);
if ($endPage - $startPage < 3 && $startPage > 1) {
    $startPage = max(1, $endPage - 3);
}

$data = $jabatan->getData($search, $sort, $order, $limitValue, $offset);
$editData = isset($_GET['edit']) ? $jabatan->getById($_GET['edit']) : null;

// Menentukan modal yang tampil
$showAddModal = isset($_GET['action']) && $_GET['action'] == 'tambah';
$showEditModal = ($editData !== null);
$showModal = $showAddModal || $showEditModal;

$msg = '';
$msgType = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':   $msg = 'Data berhasil ditambahkan!'; $msgType = 'success'; break;
        case 'updated': $msg = 'Data berhasil diupdate!'; $msgType = 'success'; break;
        case 'deleted': $msg = 'Data berhasil dihapus!'; $msgType = 'success'; break;
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-building"></i> Manajemen jabatan</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= $msg ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="jabatan">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari kode atau nama jabatan..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="?page=jabatan" class="reset-btn">Reset</a>
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
                    <input type="hidden" name="page" value="jabatan">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
                </form>
            </div>
            <a href="?page=jabatan&action=tambah" class="btn-add"><i class="fas fa-plus"></i> Tambah</a>
            <a href="views/report/print_jabatan.php?search=<?= urlencode($search) ?>" class="btn-print"><i class="fas fa-print"></i> Print</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><a href="?page=jabatan&search=<?= urlencode($search) ?>&sort=id_jabatan&order=<?= $sort == 'id_jabatan' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Kode <?= $sort == 'id_jabatan' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=jabatan&search=<?= urlencode($search) ?>&sort=jabatan&order=<?= $sort == 'jabatan' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Nama jabatan <?= $sort == 'jabatan' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_jabatan']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td class="actions">
                            <a href="?page=jabatan&edit=<?= $row['id_jabatan'] ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn-delete" data-id="<?= $row['id_jabatan'] ?>" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">Tidak ada data jabatan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($limit !== 'all' && $totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=jabatan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page-1 ?>" class="page-link">« Prev</a>
        <?php endif; ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=jabatan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=jabatan&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page+1 ?>" class="page-link">Next »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah/Edit -->
<div id="modalForm" class="modal" style="display: <?= $showModal ? 'flex' : 'none' ?>;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?= $showEditModal ? '<i class="fas fa-edit"></i> Edit Jabatan' : '<i class="fas fa-plus-circle"></i> Tambah Jabatan' ?></h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="post">
            <?php if ($showEditModal): ?>
                <input type="hidden" name="id_jabatan" value="<?= $editData['id_jabatan'] ?>">
                <input type="hidden" name="edit" value="1">
            <?php else: ?>
                <input type="hidden" name="tambah" value="1">
            <?php endif; ?>
            <div class="form-group">
                <label>Nama Jabatan</label>
                <input type="text" name="jabatan" value="<?= $showEditModal ? htmlspecialchars($editData['jabatan']) : '' ?>" required autofocus>
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
        <p style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin menghapus jabatan ini? Data yang dihapus tidak dapat dipulihkan.</p>
        <div class="modal-footer">
            <button id="confirmDeleteBtn" class="btn-save" style="background: linear-gradient(135deg, #e74c3c, #c0392b);"><i class="fas fa-trash"></i> Hapus</button>
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()"><i class="fas fa-times"></i> Batal</button>
        </div>
    </div>
</div>

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
        window.location.href = '?page=jabatan&hapus=' + deleteId + '&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>';
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
