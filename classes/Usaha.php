<?php
class Usaha {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Ambil data usaha (hanya satu record, karena hanya untuk identitas)
    public function getData() {
        $stmt = $this->db->query("SELECT * FROM tbl_usaha LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah data usaha (pertama kali)
    public function insert($data) {
        $sql = "INSERT INTO tbl_usaha (nama, alamat, nomor_telepon, fax, email, npwp, bank, noaccount, atasnama, pimpinan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nama'], $data['alamat'], $data['nomor_telepon'], $data['fax'], $data['email'],
            $data['npwp'], $data['bank'], $data['noaccount'], $data['atasnama'], $data['pimpinan']
        ]);
    }

    // Update data usaha (jika sudah ada)
    public function update($data) {
        $sql = "UPDATE tbl_usaha SET nama=?, alamat=?, nomor_telepon=?, fax=?, email=?, npwp=?, bank=?, noaccount=?, atasnama=?,
                pimpinan=? WHERE id_usaha=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nama'], $data['alamat'], $data['nomor_telepon'], $data['fax'], $data['email'],
            $data['npwp'], $data['bank'], $data['noaccount'], $data['atasnama'], $data['pimpinan'], $data['id_usaha']
        ]);
    }

    // Cek apakah sudah ada data
    public function hasData() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_usaha");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
?>