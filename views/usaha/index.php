<?php
$usaha = new Usaha();
$hasData = $usaha->hasData();
$dataUsaha = $hasData ? $usaha->getData() : null;

// Proses simpan (insert atau update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($hasData) {
        // Update data yang sudah ada
        $usaha->update($_POST);
        $msg = 'Data usaha berhasil diupdate!';
        $msgType = 'success';
    } else {
        // Insert data baru
        $usaha->insert($_POST);
        $msg = 'Data usaha berhasil disimpan!';
        $msgType = 'success';
    }
    // Redirect untuk menghindari form resubmit
    header('Location: ?page=usaha&msg=' . urlencode($msg) . '&type=' . $msgType);
    exit;
}

// Ambil pesan dari session/GET
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$msgType = isset($_GET['type']) ? $_GET['type'] : '';

include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card">
    <h2><i class="fas fa-building"></i> Profil Perusahaan</h2>
    
    <?php if ($msg): ?>
    <div class="alert <?= $msgType ?>">
        <span><?= htmlspecialchars($msg) ?></span>
        <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php endif; ?>

    <div class="info-text">
        <p><i class="fas fa-info-circle"></i> Halaman ini digunakan untuk mengelola data identitas perusahaan. Data ini akan digunakan sebagai kop surat pada laporan PDF.</p>
    </div>

    <form method="post" class="usaha-form">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label><i class="fas fa-building"></i> Nama Perusahaan</label>
                <input type="text" name="nama" value="<?= $hasData ? htmlspecialchars($dataUsaha['nama']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
                <textarea name="alamat" rows="3"><?= $hasData ? htmlspecialchars($dataUsaha['alamat']) : '' ?></textarea>
            </div>
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Nomor Telepon</label>
                <input type="text" name="nomor_telepon" value="<?= $hasData ? htmlspecialchars($dataUsaha['nomor_telepon']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-fax"></i> Fax</label>
                <input type="text" name="fax" value="<?= $hasData ? htmlspecialchars($dataUsaha['fax']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?= $hasData ? htmlspecialchars($dataUsaha['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> NPWP</label>
                <input type="text" name="npwp" value="<?= $hasData ? htmlspecialchars($dataUsaha['npwp']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-university"></i> Bank</label>
                <input type="text" name="bank" value="<?= $hasData ? htmlspecialchars($dataUsaha['bank']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-credit-card"></i> Nomor Rekening</label>
                <input type="text" name="noaccount" value="<?= $hasData ? htmlspecialchars($dataUsaha['noaccount']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Atas Nama Rekening</label>
                <input type="text" name="atasnama" value="<?= $hasData ? htmlspecialchars($dataUsaha['atasnama']) : '' ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-user"></i> Pimpinan Perusahaan</label>
                <input type="text" name="pimpinan" value="<?= $hasData ? htmlspecialchars($dataUsaha['pimpinan']) : '' ?>">
            </div>
        </div>
        <?php if ($hasData): ?>
            <input type="hidden" name="id_usaha" value="<?= $dataUsaha['id_usaha'] ?>">
        <?php endif; ?>
        <div class="form-buttons">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Simpan</button>
            <button type="reset" class="btn-cancel"><i class="fas fa-undo"></i> Reset</button>
        </div>
    </form>
</div>

<style>
.card {
    background: linear-gradient(135deg, rgba(20, 30, 60, 0.95), rgba(10, 20, 50, 0.95));
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
    border: 1px solid rgba(255, 255, 255, 0.1);
    max-width: 1000px;
    margin: 0 auto;
}
.card h2 {
    color: #ecf0f1;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}
.info-text {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1));
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    color: #a8d8ff;
    border-left: 4px solid #3498db;
}
.info-text i {
    margin-right: 8px;
    color: #3498db;
}
.usaha-form {
    margin-top: 10px;
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #ecf0f1;
    font-size: 14px;
}
.form-group label i {
    color: #3498db;
    margin-right: 6px;
}
.form-group input, .form-group textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid rgba(52, 152, 219, 0.3);
    border-radius: 8px;
    background: rgba(52, 152, 219, 0.05);
    color: #ecf0f1;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
}
.form-group input::placeholder, .form-group textarea::placeholder {
    color: rgba(255, 255, 255, 0.3);
}
.form-group input:focus, .form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    background: rgba(52, 152, 219, 0.1);
    box-shadow: 0 0 10px rgba(52, 152, 219, 0.2);
}
.form-group textarea {
    resize: vertical;
}
.form-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}
.btn-save {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
    color: white;
    border: none;
    padding: 11px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
}
.btn-cancel {
    background: rgba(52, 152, 219, 0.1);
    color: #bdc3c7;
    border: 1px solid rgba(52, 152, 219, 0.2);
    padding: 11px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-cancel:hover {
    background: rgba(52, 152, 219, 0.2);
    color: #ecf0f1;
}
.alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    position: relative;
    border-left: 4px solid;
    background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 12px;
}
.alert.success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
    color: #d1fae5;
    border-left-color: #10b981;
}
.close-alert {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 20px;
    padding: 0;
}
@media (max-width: 768px) {
    .usaha-form > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
