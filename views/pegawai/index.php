<?php
// Proses CRUD (sama seperti sebelumnya, tidak ada perubahan)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pegawai = new Pegawai();
    if (isset($_POST['tambah'])) {
        $pegawai->create($_POST, $_FILES['foto']);
        header('Location: ?page=pegawai&msg=added');
        exit;
    } elseif (isset($_POST['edit'])) {
        $pegawai->update($_POST['id_pegawai'], $_POST, $_FILES['foto']);
        header('Location: ?page=pegawai&msg=updated');
        exit;
    }
}
if (isset($_GET['hapus'])) {
    $pegawai = new Pegawai();
    $pegawai->delete($_GET['hapus']);
    header('Location: ?page=pegawai&msg=deleted');
    exit;
}

// Ambil parameter URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.id_pegawai';
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

$pegawai = new Pegawai();
$totalData = $pegawai->getTotalCount($search);
$totalPages = ($limit === 'all') ? 1 : ceil($totalData / $limitValue);

$startPage = max(1, $page - 1);
$endPage = min($totalPages, $startPage + 3);
if ($endPage - $startPage < 3 && $startPage > 1) {
    $startPage = max(1, $endPage - 3);
}

$data = $pegawai->getData($search, $sort, $order, $limitValue, $offset);
$editData = isset($_GET['edit']) ? $pegawai->getById($_GET['edit']) : null;

$showAddModal = isset($_GET['action']) && $_GET['action'] == 'tambah';
$showEditModal = ($editData !== null);
$showModal = $showAddModal || $showEditModal;

$msg = '';
$msgType = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':   $msg = 'Data pegawai berhasil ditambahkan!'; $msgType = 'success'; break;
        case 'updated': $msg = 'Data pegawai berhasil diupdate!'; $msgType = 'success'; break;
        case 'deleted': $msg = 'Data pegawai berhasil dihapus!'; $msgType = 'success'; break;
        case 'error':   $msg = 'Terjadi kesalahan, silakan coba lagi.'; $msgType = 'error'; break;
    }
}

// Ambil data departemen dan jabatan untuk dropdown
$deptModel = new Departemen();
$jabModel = new Jabatan();
$departemenList = $deptModel->getAll();
$jabatanList = $jabModel->getAll();

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-users"></i> Manajemen Pegawai</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= $msg ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="toolbar">
        <form method="get" class="search-form">
            <input type="hidden" name="page" value="pegawai">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input type="hidden" name="order" value="<?= $order ?>">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari ID, nama, departemen, jabatan..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="?page=pegawai" class="reset-btn">Reset</a>
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
                    <input type="hidden" name="page" value="pegawai">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
                </form>
            </div>
            <a href="?page=pegawai&action=tambah" class="btn-add"><i class="fas fa-plus"></i> Tambah</a>
            <a href="views/report/print_pegawai.php?search=<?= urlencode($search) ?>" class="btn-print"><i class="fas fa-print"></i> Print</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th><a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=p.id_pegawai&order=<?= $sort == 'p.id_pegawai' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">ID Pegawai <?= $sort == 'p.id_pegawai' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=p.nama&order=<?= $sort == 'p.nama' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Nama <?= $sort == 'p.nama' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=d.departemen&order=<?= $sort == 'd.departemen' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Departemen <?= $sort == 'd.departemen' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=j.jabatan&order=<?= $sort == 'j.jabatan' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Jabatan <?= $sort == 'j.jabatan' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th><a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=p.status_kerja&order=<?= $sort == 'p.status_kerja' && $order == 'asc' ? 'desc' : 'asc' ?>&limit=<?= $limit ?>&page_num=<?= $page ?>">Status Kerja <?= $sort == 'p.status_kerja' ? ($order == 'asc' ? '▲' : '▼') : '' ?></a></th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="foto-cell">
                            <?php 
                            // Path folder uploads dari root proyek (karena file ini di views/pegawai/, 
                            // kita gunakan path absolut dari document root)
                            $fotoFile = $row['foto'];
                            if (!empty($fotoFile)) {
                                // Sesuaikan 'proyek_kepegawaian' dengan nama folder proyek Anda
                                $fotoPath = $_SERVER['DOCUMENT_ROOT'] . '/proyek_kepegawaian/assets/uploads/' . $fotoFile;
                                if (file_exists($fotoPath)) {
                                    echo '<img src="' . BASE_URL . 'assets/uploads/' . $fotoFile . '" class="rounded-circle" width="45" height="45" style="object-fit: cover;">';
                                } else {
                                    echo '<div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:45px;height:45px;"><i class="fas fa-user text-white"></i></div>';
                                }
                            } else {
                                echo '<div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:45px;height:45px;"><i class="fas fa-user text-white"></i></div>';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['id_pegawai']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['departemen']) ?></td>
                        <td><?= htmlspecialchars($row['jabatan']) ?></td>
                        <td><?= $row['status_kerja'] ?></td>
                        <td class="actions">
                            <a href="?page=pegawai&edit=<?= $row['id_pegawai'] ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn-delete" data-id="<?= $row['id_pegawai'] ?>" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">Tidak ada data pegawai</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($limit !== 'all' && $totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page-1 ?>" class="page-link">« Prev</a>
        <?php endif; ?>
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=pegawai&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page+1 ?>" class="page-link">Next »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah/Edit -->
<div id="modalForm" class="modal" style="display: <?= $showModal ? 'flex' : 'none' ?>;">
    <div class="modal-content" style="width: 750px; max-width: 95%;">
        <div class="modal-header">
            <h3 style="margin:0;"><i class="<?= $showEditModal ? 'fas fa-edit' : 'fas fa-plus-circle' ?>"></i> <?= $showEditModal ? 'Edit Data Pegawai' : 'Tambah Pegawai Baru' ?></h3>
            <span class="close" onclick="closeModal()" style="line-height: 1;">&times;</span>
        </div>
        <form method="post" enctype="multipart/form-data" style="margin-top: 20px;">
            <?php if ($showEditModal): ?>
                <input type="hidden" name="id_pegawai" value="<?= $editData['id_pegawai'] ?>">
                <input type="hidden" name="edit" value="1">
            <?php else: ?>
                <input type="hidden" name="tambah" value="1">
            <?php endif; ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-height: 60vh; overflow-y: auto; padding-right: 10px; margin-bottom: 20px;">
                <!-- Semua field form sama seperti sebelumnya, tidak perlu diubah -->
                <?php if (!$showEditModal): ?>
                <div class="form-group" style="grid-column: span 2; background: rgba(52, 152, 219, 0.1); padding: 15px; border-radius: 8px; border: 1px dashed #3498db; margin-bottom: 10px;">
                    <h4 style="margin-top:0; color:#3498db; margin-bottom:10px;"><i class="fas fa-key"></i> Akun Login Pegawai</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Username Login</label>
                            <input type="text" name="username" placeholder="Username untuk login" required>
                        </div>
                        <div>
                            <label>Password Awal</label>
                            <input type="password" name="password" placeholder="Password default" required>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= $showEditModal ? htmlspecialchars($editData['nama']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Departemen</label>
                    <select name="id_departemen" required>
                        <option value="">Pilih Departemen</option>
                        <?php foreach ($departemenList as $dept): ?>
                        <option value="<?= $dept['id_departemen'] ?>" <?= ($showEditModal && $editData['id_departemen'] == $dept['id_departemen']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['departemen']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jabatan</label>
                    <select name="id_jabatan" required>
                        <option value="">Pilih Jabatan</option>
                        <?php foreach ($jabatanList as $jab): ?>
                        <option value="<?= $jab['id_jabatan'] ?>" <?= ($showEditModal && $editData['id_jabatan'] == $jab['id_jabatan']) ? 'selected' : '' ?>><?= htmlspecialchars($jab['jabatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="2"><?= $showEditModal ? htmlspecialchars($editData['alamat']) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="telepon" value="<?= $showEditModal ? htmlspecialchars($editData['telepon']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $showEditModal ? htmlspecialchars($editData['email']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Gaji</label>
                    <input type="number" step="0.01" name="gaji" value="<?= $showEditModal ? $editData['gaji'] : '' ?>">
                </div>
                <div class="form-group">
                    <label>Status Pernikahan</label>
                    <select name="status_pernikahan">
                        <option value="Menikah" <?= ($showEditModal && $editData['status_pernikahan'] == 'Menikah') ? 'selected' : '' ?>>Menikah</option>
                        <option value="Belum" <?= ($showEditModal && $editData['status_pernikahan'] == 'Belum') ? 'selected' : '' ?>>Belum</option>
                        <option value="Berpisah" <?= ($showEditModal && $editData['status_pernikahan'] == 'Berpisah') ? 'selected' : '' ?>>Berpisah</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin">
                        <option value="Laki-Laki" <?= ($showEditModal && $editData['jenis_kelamin'] == 'Laki-Laki') ? 'selected' : '' ?>>Laki-Laki</option>
                        <option value="Perempuan" <?= ($showEditModal && $editData['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Kerja</label>
                    <select name="status_kerja" required>
                        <option value="Tetap" <?= ($showEditModal && $editData['status_kerja'] == 'Tetap') ? 'selected' : '' ?>>Tetap</option>
                        <option value="Kontrak" <?= ($showEditModal && $editData['status_kerja'] == 'Kontrak') ? 'selected' : '' ?>>Kontrak</option>
                        <option value="Pensiun" <?= ($showEditModal && $editData['status_kerja'] == 'Pensiun') ? 'selected' : '' ?>>Pensiun</option>
                        <option value="Keluar" <?= ($showEditModal && $editData['status_kerja'] == 'Keluar') ? 'selected' : '' ?>>Keluar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah Cuti</label>
                    <input type="number" name="jumlah_cuti" value="<?= $showEditModal ? $editData['jumlah_cuti'] : 0 ?>" min="0">
                </div>
                <div class="form-group">
                    <label>Jenjang Pendidikan</label>
                    <select name="jenjang_pendidikan">
                        <option value="SD" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'SD') ? 'selected' : '' ?>>SD</option>
                        <option value="SMP" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'SMP') ? 'selected' : '' ?>>SMP</option>
                        <option value="SMA" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'SMA') ? 'selected' : '' ?>>SMA</option>
                        <option value="SMK" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'SMK') ? 'selected' : '' ?>>SMK</option>
                        <option value="D1" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'D1') ? 'selected' : '' ?>>D1</option>
                        <option value="D2" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'D2') ? 'selected' : '' ?>>D2</option>
                        <option value="D3" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'D3') ? 'selected' : '' ?>>D3</option>
                        <option value="D4" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'D4') ? 'selected' : '' ?>>D4</option>
                        <option value="S1" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'S1') ? 'selected' : '' ?>>S1</option>
                        <option value="S2" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'S2') ? 'selected' : '' ?>>S2</option>
                        <option value="S3" <?= ($showEditModal && $editData['jenjang_pendidikan'] == 'S3') ? 'selected' : '' ?>>S3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Mulai Kerja</label>
                    <input type="date" name="tgl_mulai_kerja" value="<?= $showEditModal ? $editData['tgl_mulai_kerja'] : '' ?>">
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" name="foto" accept="image/*" style="padding: 8px;">
                    <?php if ($showEditModal && !empty($editData['foto'])): ?>
                        <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.05); padding: 5px; border-radius: 8px;">
                            <img src="<?= BASE_URL ?>assets/uploads/<?= $editData['foto'] ?>" width="40" height="40" style="border-radius: 50%; object-fit: cover; border: 1px solid #3498db;">
                            <small style="color: #95a5a6;">Foto saat ini. Kosongkan jika tidak diganti.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()" style="flex: 1;"><i class="fas fa-times"></i> Batal</button>
                <button type="submit" class="btn-save" style="flex: 2;"><i class="fas fa-save"></i> Simpan Data Pegawai</button>
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
        <p style="color: #bdc3c7; margin: 20px 0;">Apakah Anda yakin ingin menghapus pegawai ini? Data yang dihapus tidak dapat dipulihkan.</p>
        <div class="modal-footer">
            <button id="confirmDeleteBtn" class="btn-save" style="background: linear-gradient(135deg, #e74c3c, #c0392b);"><i class="fas fa-trash"></i> Hapus</button>
            <button type="button" class="btn-cancel" onclick="closeConfirmModal()"><i class="fas fa-times"></i> Batal</button>
        </div>
    </div>
</div>

<style>
.foto-cell img { border-radius: 50%; object-fit: cover; width: 45px; height: 45px; }
.foto-cell .bg-secondary { background-color: #6c757d; display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; }
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
        window.location.href = '?page=pegawai&hapus=' + deleteId + '&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>&page_num=<?= $page ?>';
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
