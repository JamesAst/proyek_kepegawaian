<!-- Chat Widget HTML -->
<div class="chat-widget">
    <div class="chat-header" onclick="toggleChat()">
        <button id="btn-back-chat" onclick="event.stopPropagation(); showUserList()"><i class="fas fa-arrow-left"></i></button>
        <span id="chat-window-title" style="flex: 1;"><i class="fas fa-comments"></i> Chat</span>
        <i class="fas fa-chevron-up" id="chat-icon"></i>
    </div>
    <div class="chat-body" id="chat-window">
        <!-- View 1: Daftar User -->
        <div id="view-user-list" style="height: 100%; display: flex; flex-direction: column;">
            <div style="padding: 10px; background: #1a1a2e; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <input type="text" id="chat-search-input" placeholder="Cari rekan kerja..." onkeyup="filterUsers()" style="width: 100%; padding: 6px 10px; border: 1px solid rgba(52,152,219,0.3); border-radius: 5px; font-size: 0.85rem; margin: 0; outline: none; background: #16213e; color: #ecf0f1;">
            </div>
            <div id="user-list-content" style="flex: 1; overflow-y: auto;">
                <!-- User list dimuat di sini -->
            </div>
        </div>

        <!-- View 2: Area Percakapan -->
        <div id="view-conversation" style="display: none; flex-direction: column; height: 100%;">
            <div class="chat-messages" id="chat-content">
                <!-- Pesan dan daftar FAQ akan dimuat di sini via AJAX -->
            </div>
            <!-- Container untuk tombol FAQ -->
            <div id="faq-container" class="faq-container" style="display: none;"></div>
            <!-- Input area untuk chat biasa, disembunyikan untuk bot -->
            <form class="chat-input-area" id="chat-form" style="display: flex;">
                <input type="hidden" id="target-user-id">
                <input type="text" id="chat-input" placeholder="Ketik pesan..." autocomplete="off">
                <button type="submit" class="btn-primary"><i class="fas fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
let currentTargetId = null;
let chatPolling = null;

function toggleChat() {
    const chatWindow = document.getElementById('chat-window');
    const icon = document.getElementById('chat-icon');
    if (chatWindow.style.display === 'flex') {
        chatWindow.style.display = 'none';
        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        if(chatPolling) clearInterval(chatPolling);
    } else {
        chatWindow.style.display = 'flex';
        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        fetchUserList();
        startPolling();
    }
}

function fetchUserList() {
    fetch('ajax_chat.php?action=list_users')
        .then(res => res.text())
        .then(html => {
            document.getElementById('user-list-content').innerHTML = html;
            filterUsers(); // Pastikan filter tetap berlaku jika list di-refresh saat mengetik
        });
}

function startChat(userId, userName) {
    currentTargetId = userId;
    document.getElementById('target-user-id').value = userId;
    document.getElementById('chat-window-title').innerText = userName;
    document.getElementById('view-user-list').style.display = 'none';
    document.getElementById('view-conversation').style.display = 'flex';
    document.getElementById('btn-back-chat').style.display = 'block';
    
    // Sembunyikan input teks jika target adalah BOT
    if (userId === 'BOT') {
        document.getElementById('chat-form').style.display = 'none';
        document.getElementById('faq-container').style.display = 'flex';
        loadFaqButtons();
    } else {
        document.getElementById('chat-form').style.display = 'flex';
        document.getElementById('faq-container').style.display = 'none';
    }
    
    fetchMessages(true); // Paksa scroll ke bawah saat pertama buka chat
    startPolling();
}

function loadFaqButtons() {
    fetch('ajax_chat.php?action=get_faqs')
        .then(res => res.text())
        .then(html => {
            document.getElementById('faq-container').innerHTML = html;
        });
}

function sendFaqQuestion(faqKey, faqText) {
    const formData = new FormData();
    formData.append('pesan', faqKey); // Kirim key FAQ sebagai pesan
    formData.append('target_id', currentTargetId);
    fetch('ajax_chat.php?action=send', { method: 'POST', body: formData })
    .then(() => { fetchMessages(); }); // Refresh chat untuk melihat pertanyaan dan jawaban bot
}

function showUserList() {
    currentTargetId = null;
    if(chatPolling) clearInterval(chatPolling);
    document.getElementById('chat-window-title').innerHTML = '<i class="fas fa-comments"></i> Chat';
    document.getElementById('view-user-list').style.display = 'flex'; // Gunakan flex agar scroll list aktif
    document.getElementById('view-conversation').style.display = 'none';
    document.getElementById('btn-back-chat').style.display = 'none';
    document.getElementById('faq-container').style.display = 'none';
    document.getElementById('chat-search-input').value = ''; // Reset pencarian saat kembali ke list
    fetchUserList();
    startPolling(); // Mulai polling lagi untuk update badge notifikasi
}

function filterUsers() {
    const filter = document.getElementById('chat-search-input').value.toLowerCase();
    const items = document.querySelectorAll('#user-list-content .user-item');
    
    items.forEach(item => {
        const text = item.textContent || item.innerText;
        // Munculkan jika teks nama/role mengandung kata kunci pencarian
        item.style.display = text.toLowerCase().includes(filter) ? "" : "none";
    });
}

function scrollToBottom() {
    const content = document.getElementById('chat-content');
    content.scrollTop = content.scrollHeight;
}

function startPolling() {
    if(chatPolling) clearInterval(chatPolling);
    chatPolling = setInterval(() => {
        if (currentTargetId) {
            fetchMessages();
        } else {
            fetchUserList();
        }
    }, 3000);
}

function fetchMessages() {
    if (!currentTargetId) return;
    fetch(`ajax_chat.php?action=fetch&target_id=${currentTargetId}`)
        .then(response => response.text())
        .then(html => {
            const content = document.getElementById('chat-content');
            content.innerHTML = html;
            scrollToBottom();
        });
}

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    if (!input.value || !currentTargetId) return;

    // Hanya kirim pesan jika bukan BOT
    if (currentTargetId !== 'BOT') {
        const formData = new FormData();
        formData.append('pesan', input.value);
        formData.append('target_id', currentTargetId);

        fetch('ajax_chat.php?action=send', { method: 'POST', body: formData })
        .then(() => {
            input.value = '';
            fetchMessages();
        });
    }
});

// Fungsi Jam Real-time
function updateClock() {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const dayName = days[now.getDay()];
    const date = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();
    
    const timeString = `${hours}:${minutes}:${seconds}`;
    const dateString = `${dayName}, ${date} ${monthName} ${year}`;

    const clockElement = document.getElementById('clock');
    if (clockElement) {
        clockElement.querySelector('.clock-time').textContent = timeString;
        clockElement.querySelector('.clock-date').textContent = dateString;
    }
}

setInterval(updateClock, 1000);
updateClock(); // Panggil langsung agar tidak menunggu 1 detik pertama
</script>
</main>
<footer>
    &copy; <?= date('Y') ?> - Sistem Kepegawaian | All rights reserved.
</footer>
</body>
</html>

<script>
/**
 * Fungsi untuk menghapus satu pesan
 */
function deleteSingleMessage(id) {
    if (confirm('Hapus pesan ini secara permanen?')) {
        const formData = new FormData();
        formData.append('id_chat', id);
        fetch('ajax_chat.php?action=delete_msg', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            if (text.trim() === 'success') fetchMessages();
            else alert('Gagal menghapus pesan: ' + text);
        })
        .catch(() => alert('Terjadi kesalahan pada server.'));
    }
}

/**
 * Fungsi untuk menghapus seluruh percakapan
 */
function clearChatConversation(targetId) {
    if (confirm('Bersihkan semua pesan dalam percakapan ini?')) {
        const formData = new FormData();
        formData.append('target_id', targetId);
        fetch('ajax_chat.php?action=clear_chat', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            if (text.trim() === 'success') fetchMessages();
            else alert('Gagal membersihkan percakapan.');
        })
        .catch(() => alert('Terjadi kesalahan pada server.'));
    }
}
</script>
