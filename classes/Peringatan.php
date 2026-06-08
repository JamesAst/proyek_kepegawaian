<?php
class Peringatan {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    // Generate ID otomatis dengan format SP + TahunBulan + 3 digit nomor urut
    private function generateId() {
        $tahunBulan = date('Ym');
        $prefix = 'SP' . $tahunBulan;
        $stmt = $this->db->prepare("SELECT id_peringatan FROM tbl_peringatan WHERE id_peringatan LIKE ? ORDER BY id_peringatan DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($last) {
            $lastNum = (int)substr($last['id_peringatan'], strlen($prefix));
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        return $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    // Ambil semua data peringatan (join dengan pegawai)
    public function getAll() {
        $stmt = $this->db->query("SELECT p.*, peg.nama as pegawai_nama 
                                  FROM tbl_peringatan p 
                                  JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                                  ORDER BY p.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil satu data peringatan berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, peg.nama as pegawai_nama 
                                    FROM tbl_peringatan p 
                                    JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                                    WHERE p.id_peringatan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah peringatan
    public function create($data) {
        $id_peringatan = $this->generateId();
        $sql = "INSERT INTO tbl_peringatan (id_peringatan, id_pegawai, tgl_peringatan, jenis, keterangan) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $id_peringatan,
            $data['id_pegawai'],
            $data['tgl_peringatan'],
            $data['jenis'],
            $data['keterangan']
        ]);
        return $result;
    }

    // Update peringatan
    public function update($id, $data) {
        $sql = "UPDATE tbl_peringatan SET id_pegawai=?, tgl_peringatan=?, jenis=?, keterangan=? WHERE id_peringatan=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_pegawai'],
            $data['tgl_peringatan'],
            $data['jenis'],
            $data['keterangan'],
            $id
        ]);
    }

    // Hapus peringatan
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tbl_peringatan WHERE id_peringatan = ?");
        return $stmt->execute([$id]);
    }

    // Hitung total data untuk pagination
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tbl_peringatan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search OR p.jenis LIKE :search OR p.keterangan LIKE :search";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Ambil data dengan filter, sorting, pagination
    public function getData($search = '', $orderBy = 'p.tgl_peringatan', $orderDir = 'DESC', $limit = 10, $offset = 0) {
        $allowedColumns = ['p.id_peringatan', 'peg.nama', 'p.tgl_peringatan', 'p.jenis', 'p.created_at'];
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'p.tgl_peringatan';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT p.*, peg.nama as pegawai_nama 
                FROM tbl_peringatan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search OR p.jenis LIKE :search OR p.keterangan LIKE :search
                ORDER BY $orderBy $orderDir 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Untuk laporan (opsional)
    public function getAllData($search = '') {
        $sql = "SELECT p.*, peg.nama as pegawai_nama 
                FROM tbl_peringatan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search OR p.jenis LIKE :search
                ORDER BY p.tgl_peringatan ASC";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
