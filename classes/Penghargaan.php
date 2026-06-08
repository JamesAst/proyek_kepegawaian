<?php
class Penghargaan {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    private function generateId() {
        $tahunBulan = date('Ym');
        $prefix = 'SP' . $tahunBulan;
        $stmt = $this->db->prepare("SELECT id_penghargaan FROM tbl_penghargaan WHERE id_penghargaan LIKE ? ORDER BY id_penghargaan DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($last) {
            $lastNum = (int)substr($last['id_penghargaan'], strlen($prefix));
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        return $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT p.*, peg.nama as pegawai_nama 
                                  FROM tbl_penghargaan p 
                                  JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                                  ORDER BY p.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, peg.nama as pegawai_nama 
                                    FROM tbl_penghargaan p 
                                    JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                                    WHERE p.id_penghargaan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $id_penghargaan = $this->generateId();
        $sql = "INSERT INTO tbl_penghargaan (id_penghargaan, id_pegawai, tgl_penghargaan, nama_penghargaan, keterangan) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $id_penghargaan,
            $data['id_pegawai'],
            $data['tgl_penghargaan'],
            $data['nama_penghargaan'],
            $data['keterangan']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE tbl_penghargaan SET id_pegawai=?, tgl_penghargaan=?, nama_penghargaan=?, keterangan=? WHERE id_penghargaan=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_pegawai'],
            $data['tgl_penghargaan'],
            $data['nama_penghargaan'],
            $data['keterangan'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tbl_penghargaan WHERE id_penghargaan = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tbl_penghargaan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search 
                OR p.nama_penghargaan LIKE :search 
                OR p.keterangan LIKE :search";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getData($search = '', $orderBy = 'p.tgl_penghargaan', $orderDir = 'DESC', $limit = 10, $offset = 0) {
        $allowedColumns = ['p.id_penghargaan', 'peg.nama', 'p.tgl_penghargaan', 'p.nama_penghargaan', 'p.created_at'];
        
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'p.tgl_penghargaan';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
    
        $sql = "SELECT p.*, peg.nama as pegawai_nama 
                FROM tbl_penghargaan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search 
                OR p.nama_penghargaan LIKE :search 
                OR p.keterangan LIKE :search
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

    public function getAllData($search = '') {
        $sql = "SELECT p.*, peg.nama as pegawai_nama 
                FROM tbl_penghargaan p 
                JOIN tbl_pegawai peg ON p.id_pegawai = peg.id_pegawai
                WHERE peg.nama LIKE :search OR p.nama_penghargaan LIKE :search
                ORDER BY p.tgl_penghargaan ASC";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>