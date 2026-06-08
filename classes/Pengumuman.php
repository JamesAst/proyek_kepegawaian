<?php
class Pengumuman {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    public function getAll($limit = 10) {
        $sql = "SELECT p.*, u.nama as pembuat 
                FROM tbl_pengumuman p 
                LEFT JOIN tbl_user u ON p.id_user = u.id_user 
                ORDER BY p.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO tbl_pengumuman (judul, isi, tipe, id_user) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['judul'],
            $data['isi'],
            $data['tipe'],
            Session::get('user_id')
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tbl_pengumuman WHERE id_pengumuman = ?");
        return $stmt->execute([$id]);
    }

    public function getLatest() {
        $stmt = $this->db->query("SELECT * FROM tbl_pengumuman ORDER BY created_at DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>