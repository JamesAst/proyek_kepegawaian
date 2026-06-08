<?php
class GeminiBot {
    private $db;
    private $apiKey = 'AIzaSyCH0jiEvN3L6__LeI-P227TeF8XOrCbBvQ'; // Ganti dengan API Key Anda dari Google AI Studio

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAIResponse($prompt) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Tambahkan timeout agar tidak gantung
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return "Koneksi Server Error: " . $err;
        }

        $result = json_decode($response, true);

        // Cek apakah ada jawaban sukses
        if (!empty($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } 
        
        // JIKA ERROR, TAMPILKAN PESAN DARI GOOGLE
        if (isset($result['error']['message'])) {
            return "Google API Error: " . $result['error']['message'];
        }

        return "Maaf, Gemini sedang tidak bisa menjawab saat ini.";
    }

    public function saveReply($userId, $reply) {
        $stmt = $this->db->prepare("INSERT INTO tbl_chat (id_pengirim, id_penerima, pesan, is_read) VALUES ('GEMINI', ?, ?, 1)");
        return $stmt->execute([$userId, $reply]);
    }
}
?>