<?php
class Chat {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    public function sendMessage($from, $to, $message) {
        $stmt = $this->db->prepare("INSERT INTO tbl_chat (id_pengirim, id_penerima, pesan) VALUES (?, ?, ?)");
        return $stmt->execute([$from, $to, $message]);
    }

    public function getMessages($me, $other, $limit = 30) {
        $sql = "SELECT c.*, u.nama as nama_pengirim 
                FROM tbl_chat c 
                LEFT JOIN tbl_user u ON c.id_pengirim = u.id_user 
                WHERE (c.id_pengirim = :me AND c.id_penerima = :other) 
                   OR (c.id_pengirim = :other AND c.id_penerima = :me)
                ORDER BY c.waktu DESC, c.id_chat DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':me', $me);
        $stmt->bindValue(':other', $other);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_reverse($result);
    }

    public function getUsers($excludeId) {
        // Update aktivitas user yang sedang mengakses daftar ini
        $this->db->prepare("UPDATE tbl_user SET last_seen = NOW() WHERE id_user = ?")->execute([$excludeId]);

        // Mengambil user beserta jumlah pesan yang belum dibaca dari mereka
        // Status online dianggap aktif jika last_seen kurang dari 1 menit yang lalu
        $sql = "SELECT u.id_user, u.nama, u.role, 
                (SELECT COUNT(*) FROM tbl_chat WHERE id_pengirim = u.id_user AND id_penerima = :me AND is_read = 0) as unread_count,
                IF(u.last_seen > NOW() - INTERVAL 1 MINUTE, 1, 0) as is_online,
                (SELECT MAX(waktu) FROM tbl_chat WHERE (id_pengirim = u.id_user AND id_penerima = :me) OR (id_pengirim = :me AND id_penerima = u.id_user)) as last_msg_time
                FROM tbl_user u 
                WHERE u.id_user != :me
                ORDER BY last_msg_time DESC, u.nama ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':me', $excludeId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUnread($me) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tbl_chat WHERE id_penerima = ? AND is_read = 0");
        $stmt->execute([$me]);
        return $stmt->fetchColumn();
    }
}
?>