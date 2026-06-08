<?php
require_once 'config/config.php';

$chat = new Chat();
$action = $_GET['action'] ?? '';
$userId = Session::get('user_id');
$db = Database::getInstance()->getConnection();

if (!$userId) exit;

if ($action == 'list_users') {
    // Tambahkan Bot di urutan paling atas
    echo '<div class="user-item" onclick="startChat(\'BOT\', \'FAQ\')" style="background: rgba(52, 152, 219, 0.1); border-left: 3px solid #3498db;">';
    echo '<strong>FAQ</strong>';
    echo '<small>FAQ</small>';
    echo '</div>';

    $users = $chat->getUsers($userId);
    foreach ($users as $u) {
        // Filter agar tidak menampilkan 'Virtual Assistant' atau 'FAQ' dari database
        // agar tidak terjadi duplikat dengan bot yang sudah kita pasang di atas.
        if (in_array(strtolower($u['nama']), ['virtual assistant', 'faq'])) continue;

        echo '<div class="user-item" onclick="startChat(\''.$u['id_user'].'\', \''.htmlspecialchars($u['nama']).'\')">';
        echo '<div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">';
        echo '<strong>'.htmlspecialchars($u['nama']).'</strong>';
        if ($u['unread_count'] > 0) {
            echo '<span class="unread-badge">'.$u['unread_count'].'</span>';
        }
        echo '</div>';
        echo '<small>'.htmlspecialchars($u['role']).'</small>';
        echo '</div>';
    }
}

if ($action == 'get_faqs') {
    $bot = new ChatBot();
    $questions = $bot->getFaqQuestions();
    foreach ($questions as $key => $q) {
        // Gunakan q['key'] sebagai nilai yang dikirim, dan q['question'] sebagai teks yang ditampilkan
        echo '<button class="faq-button" onclick="sendFaqQuestion(\''.$q['key'].'\', \''.htmlspecialchars($q['question']).'\')">'.htmlspecialchars($q['question']).'</button>';
    }
}

if ($action == 'send' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = trim($_POST['pesan']);
    $to = $_POST['target_id'] ?? '';
    if (!empty($msg) && !empty($to)) {
        // Jika target adalah Bot, pesan yang dikirim adalah FAQ key
        if ($to === 'BOT') {
            $bot = new ChatBot();
            $userQuestion = $bot->faqs[$msg]['question'] ?? $msg; // Ambil teks pertanyaan dari key
            $chat->sendMessage($userId, $to, $userQuestion); // Simpan pertanyaan user
            $response = $bot->getResponse($msg);
            $bot->saveBotReply($userId, $response);
        } else {
            $chat->sendMessage($userId, $to, $msg);
        }
    }
} 

if ($action == 'delete_msg' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $msgId = $_POST['id_chat'] ?? '';
    if (!empty($msgId)) {
        // Hanya izinkan menghapus jika pengirimnya adalah user yang sedang login
        $stmt = $db->prepare("DELETE FROM tbl_chat WHERE id_chat = ? AND id_pengirim = ?");
        $stmt->execute([$msgId, $userId]);
        echo 'success';
        exit;
    }
}

if ($action == 'clear_chat' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $target = $_POST['target_id'] ?? '';
    if (!empty($target)) {
        // Menghapus seluruh percakapan dua arah antara user dan target (termasuk BOT)
        $stmt = $db->prepare("DELETE FROM tbl_chat WHERE (id_pengirim = ? AND id_penerima = ?) OR (id_pengirim = ? AND id_penerima = ?)");
        $stmt->execute([$userId, $target, $target, $userId]);
        echo 'success';
        exit;
    }
}

if ($action == 'fetch') {
    $target = $_GET['target_id'] ?? '';

    // Tandai pesan sebagai terbaca saat membuka percakapan (kecuali BOT)
    if ($target && $target !== 'BOT') {
        $db->prepare("UPDATE tbl_chat SET is_read = 1 WHERE id_pengirim = ? AND id_penerima = ? AND is_read = 0")
           ->execute([$target, $userId]);
    }

    $messages = $chat->getMessages($userId, $target);

    // Tambahkan tombol Hapus Semua di bagian atas jika percakapan tidak kosong
    if (!empty($messages) && !empty($target)) {
        echo '<div class="clear-chat-container">';
        echo '<button class="clear-chat-header-btn" onclick="clearChatConversation(\''.$target.'\')">🗑️ Bersihkan Semua Pesan</button>';
        echo '</div>';
    }

    foreach ($messages as $m) {
        $isSelf = ($m['id_pengirim'] == $userId) ? 'self' : 'others';
        
        if ($m['id_pengirim'] === 'BOT') {
            $senderName = 'FAQ';
        } else {
            $senderName = $m['nama_pengirim'];
        }
        
        $msgId = isset($m['id_chat']) ? $m['id_chat'] : null;
        echo '<div class="msg ' . $isSelf . '">';
        if ($isSelf === 'self' && $msgId) {
            echo '<button class="delete-msg-btn" onclick="deleteSingleMessage(\'' . $msgId . '\')" title="Hapus pesan">✕</button>';
        }
        echo '<span class="msg-user">' . htmlspecialchars($senderName) . '</span>';
        echo htmlspecialchars($m['pesan']);
        echo '</div>';
    }
}
?>
