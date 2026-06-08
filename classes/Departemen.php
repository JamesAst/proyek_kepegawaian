<?php
class Departemen {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    // Ambil data dengan filter, sorting, pagination
    public function getData($search = '', $orderBy = 'id_departemen', $orderDir = 'ASC', $limit = 10, $offset = 0) {
        // Validasi kolom yang boleh di-sort
        $allowedColumns = ['id_departemen', 'departemen', 'created_at'];
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'id_departemen';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM tbl_departemen 
                WHERE id_departemen LIKE :search OR departemen LIKE :search 
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

    // Hitung total data untuk pagination
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tbl_departemen 
                WHERE id_departemen LIKE :search OR departemen LIKE :search";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Method lain (getAll, getById, generateId, create, update, delete, countAll) tetap sama seperti sebelumnya
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM tbl_departemen ORDER BY id_departemen");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_departemen WHERE id_departemen = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    private function generateId() {
        $stmt = $this->db->query("SELECT id_departemen FROM tbl_departemen ORDER BY id_departemen DESC LIMIT 1");
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastNum = $last ? (int)substr($last['id_departemen'], 3) : 0;
        $newNum = $lastNum + 1;
        return 'DEP' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }
    public function create($data) {
        $id = $this->generateId();
        $stmt = $this->db->prepare("INSERT INTO tbl_departemen (id_departemen, departemen) VALUES (?, ?)");
        return $stmt->execute([$id, $data['departemen']]);
    }
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE tbl_departemen SET departemen = ? WHERE id_departemen = ?");
        return $stmt->execute([$data['departemen'], $id]);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tbl_departemen WHERE id_departemen = ?");
        return $stmt->execute([$id]);
    }
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_departemen");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function getAllData($search = '') {
        $sql = "SELECT * FROM tbl_departemen 
                WHERE id_departemen LIKE :search OR departemen LIKE :search 
                ORDER BY id_departemen ASC";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
