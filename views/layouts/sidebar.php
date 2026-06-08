<div class="sidebar">
    <ul>
        <li><a href="<?= BASE_URL ?>index.php?page=dashboard">📊 Dashboard</a></li>
        <?php if (in_array(Session::get('role'), ['hrd', 'manager', 'admin'])): ?>
        <li><span>📁 Master</span>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php?page=usaha">🏢 Usaha</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=departemen">🏛️ Departemen</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=jabatan">💼 Jabatan</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=pegawai">👥 Pegawai</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li><span>📝 Transaksi</span>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php?page=cuti">🏖️ Cuti</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=izin">📅 Izin</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=peringatan">⚠️ Peringatan</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=penghargaan">🏆 Penghargaan</a></li>
            </ul>
        </li>
        <li><span>📄 Report</span>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php?page=report_pegawai">📄 Data Pegawai</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=report_sp">📜 SP</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=report_cuti">🏖️ Cuti</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=report_izin">📅 Izin</a></li>
                <li><a href="<?= BASE_URL ?>index.php?page=report_penghargaan">🏆 Penghargaan</a></li>
            </ul>
        </li>
        <li><a href="<?= BASE_URL ?>index.php?page=setting">⚙️ Setting</a></li>
        <li><a href="<?= BASE_URL ?>index.php?page=logout">🚪 Logout</a></li>
    </ul>
</div>