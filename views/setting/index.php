<?php
// 1. Load Koneksi dan Class
if (session_status() === PHP_SESSION_NONE) session_start();
$userSetting = new UserSetting(); // Konsisten dengan class lain yang menggunakan Singleton DB

// 2. Ambil data user (Ganti ADM001 dengan ID user yang sedang login jika perlu)
$userId = Session::get('user_id');

if (!$userId) {
    header('Location: index.php?page=login');
    exit;
}

$userData = $userSetting->getUserById($userId);

if (!$userData) {
    die("User tidak ditemukan.");
}

// 3. Proses Update Profil
$error = '';
if (isset($_POST['simpan'])) {
    $nama_user_baru = $_POST['nama_user'];
    $nama_lengkap_baru = $_POST['nama_lengkap'];
    $password_input = $_POST['password_baru'];
    
    // Logika Password: Jika diisi, maka hash. Jika kosong, biarkan null.
    $password_final = null;
    if (!empty($password_input)) {
        $password_final = password_hash($password_input, PASSWORD_DEFAULT);
    }

    // Logika Upload Foto
    $foto_final = null; 
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "assets/uploads/";
        $nama_file = time() . "_" . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $nama_file)) {
            $foto_final = $nama_file;
        }
    }

    // Validasi Duplikat Username
    if ($userSetting->isUsernameExists($nama_user_baru, $userId)) {
        $error = "Username '$nama_user_baru' sudah digunakan oleh pengguna lain!";
    } else {
        // Eksekusi Update
        if ($userSetting->updateProfile($userId, $nama_user_baru, $nama_lengkap_baru, $password_final, $foto_final)) {
            // Update session agar nama di header langsung berubah tanpa logout
            Session::set('nama', $nama_lengkap_baru);
            header('Location: ?page=setting&msg=updated');
            exit;
        }
    }
}

$msg = '';
$msgType = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
    $msg = 'Profil berhasil diperbarui!';
    $msgType = 'success';
}
    
include __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="card" style="max-width: 700px; margin: 20px auto;">
    <h2 style="margin-top: 0; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-circle" style="color: #3498db;"></i> Pengaturan Profil
    </h2>

        <?php if ($msg): ?>
            <div class="alert success" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?= $msg ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 20px 0;">

        <form method="POST" enctype="multipart/form-data">
            <div style="display: flex; align-items: center; gap: 20px; background: rgba(52, 152, 219, 0.1); padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid rgba(52, 152, 219, 0.2);">
                <img src="<?= BASE_URL ?>assets/uploads/<?= !empty($userData['foto']) ? $userData['foto'] : 'default.png' ?>" 
                     style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                <div>
                    <p style="margin: 0; font-size: 14px; color: #95a5a6;">ID User: <strong style="color: #ecf0f1;"><?= $userData['id_user'] ?></strong></p>
                    <p style="margin: 8px 0; font-size: 16px; color: #ecf0f1;">Username: <strong><?= $userData['nama_user'] ?></strong></p>
                    <span style="background: linear-gradient(135deg, rgba(52, 152, 219, 0.3), rgba(41, 128, 185, 0.2)); color: #3498db; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; border: 1px solid rgba(52, 152, 219, 0.3);">
                        <i class="fas fa-shield-alt" style="margin-right: 4px;"></i><?= $userData['role'] ?>
                    </span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #ecf0f1;">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($userData['nama']) ?>" 
                       style="width: 100%; padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3); border-radius: 8px; background: rgba(52, 152, 219, 0.05); color: #ecf0f1; box-sizing: border-box; font-family: inherit;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #ecf0f1;">Nama User / Username</label>
                <input type="text" name="nama_user" value="<?= $userData['nama_user'] ?>" 
                       style="width: 100%; padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3); border-radius: 8px; background: rgba(52, 152, 219, 0.05); color: #ecf0f1; box-sizing: border-box; font-family: inherit;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #ecf0f1;">Password Baru</label>
                <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ganti"
                       style="width: 100%; padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3); border-radius: 8px; background: rgba(52, 152, 219, 0.05); color: #ecf0f1; box-sizing: border-box; font-family: inherit;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #ecf0f1;">Ganti Foto Profil</label>
                <input type="file" name="foto" style="color: #bdc3c7; border: 1px dashed rgba(52, 152, 219, 0.3); padding: 12px; border-radius: 8px; width: 100%; background: rgba(52, 152, 219, 0.05); box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 15px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                <button type="submit" name="simpan" 
                        style="flex: 2; background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 15px; transition: all 0.3s ease;">
                    <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Perubahan
                </button>
                
                <a href="index.php?page=dashboard" 
                   style="flex: 1; background: rgba(52, 152, 219, 0.1); color: #bdc3c7; text-decoration: none; text-align: center; padding: 14px; border-radius: 8px; font-weight: bold; font-size: 15px; border: 1px solid rgba(52, 152, 219, 0.2); transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left" style="margin-right: 6px;"></i> Kembali
                </a>
            </div>
        </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>