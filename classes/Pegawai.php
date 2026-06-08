<?php
class Pegawai {
    private $db;
    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
        // Pastikan folder uploads ada
        if (!is_dir('assets/uploads')) {
            mkdir('assets/uploads', 0777, true);
        }
    }

    // Ambil semua data pegawai (join dengan departemen & jabatan)
    public function getAll() {
        $stmt = $this->db->query("SELECT p.*, d.departemen, j.jabatan 
                                  FROM tbl_pegawai p 
                                  LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                                  LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                                  ORDER BY p.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil satu data pegawai berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, d.departemen, j.jabatan 
                                    FROM tbl_pegawai p 
                                    LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                                    LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                                    WHERE p.id_pegawai = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Generate ID otomatis dengan format PGW + TahunBulan + 3 digit nomor urut
    private function generateId() {
        $tahunBulan = date('Ym'); // contoh: 202504
        $prefix = 'PGW' . $tahunBulan;
        $stmt = $this->db->prepare("SELECT id_pegawai FROM tbl_pegawai WHERE id_pegawai LIKE ? ORDER BY id_pegawai DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($last) {
            $lastNum = (int)substr($last['id_pegawai'], strlen($prefix));
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        return $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    // Tambah data pegawai (dengan upload foto)
    public function create($data, $foto) {
        $id = $this->generateId();
        $fotoName = null;
        if ($foto && $foto['error'] == 0) {
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $fotoName = $id . '_' . time() . '.' . $ext;
            $target = 'assets/uploads/' . $fotoName;
            if (!move_uploaded_file($foto['tmp_name'], $target)) {
                $fotoName = null; // gagal upload
            }
        }

        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO tbl_pegawai (id_pegawai, id_departemen, id_jabatan, nama, alamat, telepon, email, gaji, status_pernikahan, jenis_kelamin, status_kerja, jumlah_cuti, jenjang_pendidikan, tgl_mulai_kerja, foto) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $id, $data['id_departemen'], $data['id_jabatan'], $data['nama'], $data['alamat'],
                $data['telepon'], $data['email'], $data['gaji'], $data['status_pernikahan'],
                $data['jenis_kelamin'], $data['status_kerja'], $data['jumlah_cuti'],
                $data['jenjang_pendidikan'], $data['tgl_mulai_kerja'], $fotoName
            ]);

            // Integrasi Akun Login (tbl_user)
            $stmtJab = $this->db->prepare("SELECT jabatan FROM tbl_jabatan WHERE id_jabatan = ?");
            $stmtJab->execute([$data['id_jabatan']]);
            $jab = $stmtJab->fetch(PDO::FETCH_ASSOC);
            $role = strtolower($jab['jabatan'] ?? 'staff');

            $username = $data['username'];
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $sqlUser = "INSERT INTO tbl_user (id_user, nama_user, nama, password, role, foto) VALUES (?, ?, ?, ?, ?, ?)";
            $this->db->prepare($sqlUser)->execute([$id, $username, $data['nama'], $password, $role, $fotoName]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Update data pegawai (dengan upload foto baru jika ada)
    public function update($id, $data, $foto) {
        $fotoName = null;
        $old = $this->getById($id);
        if ($foto && $foto['error'] == 0) {
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $fotoName = $id . '_' . time() . '.' . $ext;
            $target = 'assets/uploads/' . $fotoName;
            if (move_uploaded_file($foto['tmp_name'], $target)) {
                // Hapus foto lama jika ada
                if ($old && $old['foto'] && file_exists('assets/uploads/' . $old['foto'])) {
                    unlink('assets/uploads/' . $old['foto']);
                }
            } else {
                $fotoName = null;
            }
        }

        $this->db->beginTransaction();
        try {
            $sql = "UPDATE tbl_pegawai SET id_departemen=?, id_jabatan=?, nama=?, alamat=?, telepon=?, email=?, gaji=?, status_pernikahan=?, jenis_kelamin=?, status_kerja=?, jumlah_cuti=?, jenjang_pendidikan=?, tgl_mulai_kerja=?";
            $params = [$data['id_departemen'], $data['id_jabatan'], $data['nama'], $data['alamat'], $data['telepon'], $data['email'], $data['gaji'], $data['status_pernikahan'], $data['jenis_kelamin'], $data['status_kerja'], $data['jumlah_cuti'], $data['jenjang_pendidikan'], $data['tgl_mulai_kerja']];
            
            if ($fotoName) {
                $sql .= ", foto=?";
                $params[] = $fotoName;
            }
            $sql .= " WHERE id_pegawai=?";
            $params[] = $id;
            $this->db->prepare($sql)->execute($params);

            // Sinkronisasi Nama dan Foto ke tbl_user jika berubah
            $sqlUser = "UPDATE tbl_user SET nama = ?" . ($fotoName ? ", foto = ?" : "") . " WHERE id_user = ?";
            $paramsUser = $fotoName ? [$data['nama'], $fotoName, $id] : [$data['nama'], $id];
            $this->db->prepare($sqlUser)->execute($paramsUser);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Hapus pegawai (beserta foto)
    public function delete($id) {
        $data = $this->getById($id);
        if ($data && $data['foto'] && file_exists('assets/uploads/' . $data['foto'])) {
            unlink('assets/uploads/' . $data['foto']);
        }
        $this->db->beginTransaction();
        try {
            $this->db->prepare("DELETE FROM tbl_user WHERE id_user = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM tbl_pegawai WHERE id_pegawai = ?")->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Hitung total data (untuk dashboard)
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_pegawai");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Hitung total data dengan pencarian (untuk pagination)
    public function getTotalCount($search = '') {
        $sql = "SELECT COUNT(*) as total FROM tbl_pegawai p 
                LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                WHERE p.id_pegawai LIKE :search OR p.nama LIKE :search OR d.departemen LIKE :search OR j.jabatan LIKE :search";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Ambil data dengan filter, sorting, pagination
    public function getData($search = '', $orderBy = 'p.id_pegawai', $orderDir = 'ASC', $limit = 10, $offset = 0) {
        $allowedColumns = ['p.id_pegawai', 'p.nama', 'd.departemen', 'j.jabatan', 'p.status_kerja', 'p.created_at'];
        if (!in_array($orderBy, $allowedColumns)) $orderBy = 'p.id_pegawai';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT p.*, d.departemen, j.jabatan 
                FROM tbl_pegawai p 
                LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                WHERE p.id_pegawai LIKE :search OR p.nama LIKE :search OR d.departemen LIKE :search OR j.jabatan LIKE :search
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

    // Ambil semua data tanpa pagination (untuk print PDF)
    public function getAllData($search = '') {
        $sql = "SELECT p.*, d.departemen, j.jabatan 
                FROM tbl_pegawai p 
                LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                WHERE p.id_pegawai LIKE :search OR p.nama LIKE :search OR d.departemen LIKE :search OR j.jabatan LIKE :search
                ORDER BY p.id_pegawai ASC";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk dashboard: 5 pegawai terbaru
    public function getLatest($limit = 5) {
        $stmt = $this->db->prepare("SELECT p.id_pegawai, p.nama, p.foto, d.departemen, j.jabatan 
                                    FROM tbl_pegawai p 
                                    LEFT JOIN tbl_departemen d ON p.id_departemen = d.id_departemen
                                    LEFT JOIN tbl_jabatan j ON p.id_jabatan = j.id_jabatan
                                    ORDER BY p.created_at DESC LIMIT ?");
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
