<?php
class User {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }
    
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_user WHERE nama_user = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['password'])) {
            Session::set('user_id', $user['id_user']);
            Session::set('nama_user', $user['nama_user']);
            Session::set('role', $user['role']);
            Session::set('nama', $user['nama']);
            return true;
        }
        return false;
    }

    // Perhatikan bagian di bawah ini, titik koma (;) jangan sampai tertukar
    public function isLoggedIn() { return Session::get('user_id') !== null; }
    public function logout() { Session::destroy(); }
}
?>