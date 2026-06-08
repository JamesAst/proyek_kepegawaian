<?php
class UserSetting {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_user WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isUsernameExists($username, $excludeId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tbl_user WHERE nama_user = ? AND id_user != ?");
        $stmt->execute([$username, $excludeId]);
        return $stmt->fetchColumn() > 0;
    }


    public function updateProfile($id, $nama_user, $nama, $password_hashed, $foto) {
        $params = [$nama_user, $nama];
        $sql = "UPDATE tbl_user SET nama_user = ?, nama = ?";
        
        // Jika password baru diisi (tidak null), tambahkan ke query
        if ($password_hashed !== null) {
            $sql .= ", password = ?";
            $params[] = $password_hashed;
        }

        // Jika ada foto baru, tambahkan ke query
        if ($foto !== null) {
            $sql .= ", foto = ?";
            $params[] = $foto;
        }

        $sql .= " WHERE id_user = ?";
        $params[] = $id;

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Sinkronisasi Balik ke tbl_pegawai
            $sqlPegawai = "UPDATE tbl_pegawai SET nama = ?" . ($foto !== null ? ", foto = ?" : "") . " WHERE id_pegawai = ?";
            $paramsPegawai = $foto !== null ? [$nama, $foto, $id] : [$nama, $id];
            $this->db->prepare($sqlPegawai)->execute($paramsPegawai);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}