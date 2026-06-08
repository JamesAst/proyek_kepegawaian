<?php
class ChatBot {
    private $db;
    public $faqs = [
        'salam' => [
            'question' => 'Halo, saya ingin bertanya.',
            'answer' => 'Halo! Saya asisten virtual Anda. Saya siap membantu dengan pertanyaan seputar sistem kepegawaian. Silakan pilih pertanyaan di bawah.'
        ],
        'ajukan_cuti' => [
            'question' => 'Bagaimana cara mengajukan cuti?',
            'answer' => 'Untuk mengajukan cuti, silakan navigasi ke menu "Transaksi" > "Cuti", lalu klik tombol "Tambah Pengajuan". Isi formulir yang tersedia dan kirim.'
        ],
        'status_cuti' => [
            'question' => 'Bagaimana melihat status cuti saya?',
            'answer' => 'Anda dapat melihat status pengajuan cuti Anda di menu "Transaksi" > "Cuti". Status akan diperbarui setelah disetujui oleh HRD/Manajer.'
        ],
        'sisa_cuti' => [
            'question' => 'Berapa sisa jatah cuti saya?',
            'answer' => 'Sisa jatah cuti Anda dapat dilihat di halaman profil Anda atau di menu "Transaksi" > "Cuti" pada kolom "Sisa Cuti".'
        ],
        'jam_kerja_kantor' => [
            'question' => 'Berapa jam kerja kantor?',
            'answer' => 'Jam kerja standar kantor adalah Senin hingga Jumat, pukul 08:00 pagi sampai 17:00 sore. Untuk detail lebih lanjut, silakan cek kebijakan perusahaan.'
        ],
        'info_gaji' => [
            'question' => 'Bagaimana cara melihat detail gaji?',
            'answer' => 'Detail gaji Anda dapat diakses melalui menu "Profil" Anda. Untuk pertanyaan spesifik, silakan hubungi bagian keuangan atau HRD.'
        ],
        'lupa_password' => [
            'question' => 'Saya lupa password, bagaimana cara meresetnya?',
            'answer' => 'Jika Anda lupa password, silakan hubungi administrator sistem atau HRD untuk bantuan reset password. Mohon jangan bagikan password Anda kepada siapapun.'
        ],
        'kontak_hrd' => [
            'question' => 'Bagaimana cara menghubungi HRD/Admin?',
            'answer' => 'Anda dapat menghubungi HRD melalui email hrd@perusahaan.com atau telepon di nomor (021) 1234-5678 selama jam kerja.'
        ],
        'tentang_sistem' => [
            'question' => 'Apa saja fitur utama sistem ini?',
            'answer' => 'Sistem ini menyediakan fitur manajemen pegawai, presensi, cuti, pengumuman, dan chat antar pegawai. Kami terus berupaya meningkatkan fungsionalitasnya!'
        ],
        'tambah_pegawai' => [
            'question' => 'Bagaimana cara menambah data pegawai baru?',
            'answer' => 'Untuk menambah pegawai baru, navigasi ke menu "Master" > "Pegawai", lalu klik tombol "Tambah". Isi semua data yang diperlukan dan simpan.'
        ],
        'edit_pegawai' => [
            'question' => 'Bagaimana cara mengedit data pegawai?',
            'answer' => 'Di menu "Master" > "Pegawai", cari pegawai yang ingin diedit, lalu klik ikon pensil (edit) di kolom "Aksi". Ubah data dan simpan.'
        ],
        'hapus_pegawai' => [
            'question' => 'Bagaimana cara menghapus data pegawai?',
            'answer' => 'Di menu "Master" > "Pegawai", cari pegawai yang ingin dihapus, lalu klik ikon tempat sampah (hapus) di kolom "Aksi". Konfirmasi penghapusan.'
        ],
        'ajukan_izin' => [
            'question' => 'Bagaimana cara mengajukan izin?',
            'answer' => 'Untuk mengajukan izin, silakan navigasi ke menu "Transaksi" > "Izin", lalu klik tombol "Tambah Pengajuan". Isi formulir yang tersedia dan kirim.'
        ],
        'status_izin' => [
            'question' => 'Bagaimana melihat status izin saya?',
            'answer' => 'Anda dapat melihat status pengajuan izin Anda di menu "Transaksi" > "Izin". Status akan diperbarui setelah disetujui oleh HRD/Manajer.'
        ],
        'laporan_pegawai' => [
            'question' => 'Bagaimana cara mencetak laporan data pegawai?',
            'answer' => 'Untuk mencetak laporan data pegawai, navigasi ke menu "Master" > "Pegawai", lalu klik tombol "Print". Anda juga bisa memfilter data sebelum mencetak.'
        ],
        'laporan_cuti' => [
            'question' => 'Bagaimana cara mencetak laporan cuti?',
            'answer' => 'Laporan cuti dapat dicetak melalui menu "Laporan" > "Cuti" (jika tersedia) atau dari halaman "Transaksi" > "Cuti" dengan opsi print.'
        ],
        'pengumuman_terbaru' => [
            'question' => 'Di mana saya bisa melihat pengumuman terbaru?',
            'answer' => 'Pengumuman terbaru dapat dilihat di menu "Papan Pengumuman" atau mungkin ditampilkan di dashboard utama Anda.'
        ],
        'ubah_profil' => [
            'question' => 'Bagaimana cara mengubah informasi profil saya?',
            'answer' => 'Anda bisa mengubah informasi profil pribadi Anda (seperti alamat, telepon) di menu "Setting" > "Profil".'
        ],
        'tentang_perusahaan' => [
            'question' => 'Informasi tentang perusahaan (PT. Maju Sendirian)?',
            'answer' => 'Informasi detail tentang perusahaan seperti alamat, kontak, dan pimpinan dapat dilihat di menu "Master" > "Usaha" (untuk Admin/HRD).'
        ]
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getResponse($faqKey) {
        // Langsung cari berdasarkan kunci FAQ yang dipilih
        if (isset($this->faqs[$faqKey])) {
            return $this->faqs[$faqKey]['answer'];
        }
        
        return "Maaf, pertanyaan yang Anda pilih tidak valid. Silakan pilih dari daftar pertanyaan yang tersedia.";
    }

    public function getFaqQuestions() {
        // Mengembalikan daftar pertanyaan untuk ditampilkan di UI
        return array_map(function($key, $faq) { return ['key' => $key, 'question' => $faq['question']]; }, array_keys($this->faqs), $this->faqs);
    }

    public function saveBotReply($userId, $reply) {
        $stmt = $this->db->prepare("INSERT INTO tbl_chat (id_pengirim, id_penerima, pesan, is_read) VALUES ('BOT', ?, ?, 1)");
        return $stmt->execute([$userId, $reply]);
    }
}
?>