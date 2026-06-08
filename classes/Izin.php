<?php
class izin {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    private function generateId() {
        $tahunBulan = date('Ym');
        $prefix = 'CT' . $tahunBulan;
        $stmt = $this->db->prepare("SELECT id_izin FROM tbl_izin WHERE id_izin LIKE ? ORDER BY id_izin DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($last) {
            $lastNum = (int)substr($last['id_izin'], strlen($prefix));
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        return $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.*, p.nama as pegawai_nama 
                                    FROM tbl_izin c 
                                    JOIN tbl_pegawai p ON c.id_pegawai = p.id_pegawai
                                    WHERE c.id_izin = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $role = Session::get('role');
            if (in_array($role, ['hrd', 'manager', 'admin']) && !empty($data['id_pegawai'])) {
                $id_pegawai = $data['id_pegawai'];
            } else {
                $id_pegawai = Session::get('user_id');
            }
            $tgl_izin = $data['tgl_izin'];
            $alasan = $data['alasan'];
            $id_izin = $this->generateId();

            $sql = "INSERT INTO tbl_izin (id_izin, id_pegawai, tgl_izin, alasan, status) 
                    VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id_izin, $id_pegawai, $tgl_izin, $alasan]);
            
            return ['status' => $result, 'message' => 'Berhasil'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE tbl_izin SET status = ? WHERE id_izin = ?";
        return $this->db->prepare($sql)->execute([$status, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM tbl_izin WHERE id_izin = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }

    public function getTotalCount($search = '', $filterId = null) {
        $whereId = $filterId ? " AND c.id_pegawai = :filter_id " : "";
        $sql = "SELECT COUNT(*) as total FROM tbl_izin c 
                JOIN tbl_pegawai p ON c.id_pegawai = p.id_pegawai
                WHERE (p.nama LIKE :search OR c.status LIKE :search) $whereId";
        $stmt = $this->db->prepare($sql);
        if($filterId) $stmt->bindValue(':filter_id', $filterId);
        $searchTerm = "%$search%";
        $stmt->bindValue(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getData($search = '', $limit = 10, $offset = 0, $filterId = null) {
        $whereId = $filterId ? " AND c.id_pegawai = :filter_id " : "";
        $sql = "SELECT c.*, p.nama as pegawai_nama 
                FROM tbl_izin c 
                JOIN tbl_pegawai p ON c.id_pegawai = p.id_pegawai
                WHERE (p.nama LIKE :search OR c.status LIKE :search) $whereId
                ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        if($filterId) $stmt->bindValue(':filter_id', $filterId);
        $searchTerm = "%$search%";
        $stmt->bindValue(':search', $searchTerm);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil semua data tanpa pagination (untuk report/print)
    public function getAllData($search = '') {
        $sql = "SELECT c.*, p.nama as pegawai_nama 
                FROM tbl_izin c 
                JOIN tbl_pegawai p ON c.id_pegawai = p.id_pegawai
                WHERE p.nama LIKE :search OR c.status LIKE :search
                ORDER BY c.tgl_izin ASC";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}