<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Konsultasi;
use App\Models\Nakes;
use App\Models\Notifikasi;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Events\MessageSent;
class ChatController extends Controller
{
    // =========================================================================
    // CHATBOT RESPONSES (Rule-based — extensible untuk AI di masa depan)
    // =========================================================================

    /**
     * Template respons chatbot berdasarkan keyword.
     * Bisa di-upgrade ke OpenAI/Gemini API nanti.
     */
    private function generateBotReply(string $pesan): string
    {
        $pesan = strtolower($pesan);

        // Rule-based keyword matching
        $rules = [
            // Salam
            ['keywords' => ['halo', 'hai', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'hi', 'hello'],
             'reply'    => 'Halo! 👋 Saya HI!-CARE Bot, asisten kesehatan digital Anda. Ada yang bisa saya bantu terkait pengobatan ARV atau kesehatan Anda hari ini?'],

            // ARV umum
            ['keywords' => ['arv', 'antiretroviral', 'obat arv'],
             'reply'    => '💊 ARV (Antiretroviral) adalah obat yang digunakan untuk mengendalikan virus HIV. Penting untuk diminum setiap hari pada waktu yang sama. Apakah Anda ingin tahu lebih detail tentang jenis ARV atau efek sampingnya?'],

            // Efek samping / mual
            ['keywords' => ['mual', 'muntah', 'efek samping', 'pusing', 'sakit kepala'],
             'reply'    => '🤢 Mual dan pusing adalah efek samping umum ARV, terutama di awal pengobatan. Tips:\n\n• Minum obat setelah makan, bukan perut kosong\n• Hindari makanan berlemak sebelum minum obat\n• Coba minum sebelum tidur agar efek samping terasa saat tidur\n• Minum air putih yang cukup\n\nJika efek samping berlanjut lebih dari 2 minggu, sebaiknya konsultasi dengan tenaga kesehatan Anda.'],

            // Jadwal minum obat
            ['keywords' => ['jadwal', 'waktu minum', 'kapan minum', 'jam minum', 'lupa minum'],
             'reply'    => '⏰ Tips Jadwal Minum ARV:\n\n• Pilih waktu yang konsisten setiap hari (misal: jam 8 pagi)\n• Gunakan fitur Alarm di aplikasi HI!-CARE\n• Jika lupa, segera minum saat ingat (kecuali sudah mendekati dosis berikutnya)\n• JANGAN menggandakan dosis\n\nGunakan tab "Alarm" di aplikasi untuk mengatur pengingat otomatis! 🔔'],

            // Kepatuhan
            ['keywords' => ['kepatuhan', 'adherence', 'patuh', 'disiplin', 'rutin'],
             'reply'    => '📊 Kepatuhan minum obat sangat penting! Target kepatuhan ARV adalah ≥95% agar virus tetap tertekan. Anda bisa memantau skor kepatuhan Anda di halaman Dashboard.\n\nTips meningkatkan kepatuhan:\n• Pasang alarm harian\n• Simpan obat di tempat yang mudah terlihat\n• Gunakan kotak obat mingguan\n• Catat di diary harian HI!-CARE'],

            // CD4 / viral load
            ['keywords' => ['cd4', 'viral load', 'vl', 'lab', 'pemeriksaan'],
             'reply'    => '🔬 Pemeriksaan rutin yang penting:\n\n• **CD4**: Mengukur kekuatan sistem imun. Target: >500 sel/mm³\n• **Viral Load**: Mengukur jumlah virus. Target: Tidak terdeteksi (<50 kopi/mL)\n\nPemeriksaan biasanya dilakukan setiap 6 bulan. Konsultasikan jadwal pemeriksaan Anda dengan tenaga kesehatan.'],

            // Refill obat
            ['keywords' => ['refill', 'ambil obat', 'stok obat', 'habis', 'resep'],
             'reply'    => '💊 Untuk refill obat ARV:\n\n1. Gunakan fitur "Refill Obat" di aplikasi\n2. Ajukan permintaan minimal 1 minggu sebelum obat habis\n3. Bawa kartu pasien saat mengambil obat\n\nAnda bisa mengajukan refill melalui menu di Dashboard!'],

            // Terima kasih
            ['keywords' => ['terima kasih', 'makasih', 'thanks', 'thank you'],
             'reply'    => 'Sama-sama! 😊 Senang bisa membantu. Jangan ragu untuk bertanya kapan saja. Kesehatan Anda adalah prioritas kami. Tetap semangat dan jaga kepatuhan minum obat! 💪'],

            // Konsultasi
            ['keywords' => ['konsultasi', 'dokter', 'nakes', 'bidan', 'perawat', 'konselor'],
             'reply'    => '👨‍⚕️ Untuk konsultasi langsung dengan tenaga kesehatan:\n\n1. Gunakan fitur "Booking" di halaman Chat\n2. Pilih jadwal nakes yang tersedia\n3. Tunggu konfirmasi booking\n4. Nakes akan mengambil alih chat ini saat jam konsultasi\n\nAtau Anda bisa menunggu nakes mengambil alih chat ini secara langsung.'],
        ];

        foreach ($rules as $rule) {
            foreach ($rule['keywords'] as $keyword) {
                if (str_contains($pesan, $keyword)) {
                    return $rule['reply'];
                }
            }
        }

        // Default response
        return '🤖 Terima kasih atas pertanyaan Anda. Saat ini saya belum bisa menjawab pertanyaan tersebut secara spesifik.\n\nAnda bisa:\n• Bertanya tentang ARV, efek samping, jadwal minum obat, atau kepatuhan\n• Menggunakan fitur "Booking" untuk konsultasi langsung dengan tenaga kesehatan\n\nTenaga kesehatan akan segera mengambil alih percakapan ini jika diperlukan.';
    }

    // =========================================================================
    // GET MESSAGES — Ambil riwayat chat per sesi konsultasi
    // =========================================================================

    /**
     * GET /api/chat/{konsultasiId}/messages
     * Ambil semua pesan dalam satu sesi konsultasi.
     */
    public function getMessages($konsultasiId)
    {
        $user = auth()->user();

        $konsultasi = Konsultasi::with(['nakes.user', 'pasien.user'])->find($konsultasiId);

        if (!$konsultasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi konsultasi tidak ditemukan'
            ], 404);
        }

        // Validasi akses: hanya pasien pemilik atau nakes yang ditunjuk
        $pasien = Pasien::where('user_id', $user->id)->first();
        $nakes = Nakes::where('user_id', $user->id)->first();

        $isOwner = ($pasien && $konsultasi->pasien_id === $pasien->id)
                || ($nakes && $konsultasi->nakes_id === $nakes->id);

        if (!$isOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke sesi chat ini'
            ], 403);
        }

        $messages = Chat::where('konsultasi_id', $konsultasiId)
            ->with(['nakes.user'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'sender'     => $msg->sender, // pasien, nakes, bot
                    'pesan'      => $msg->pesan,
                    'nakes_nama' => $msg->sender === 'nakes' ? ($msg->nakes?->user?->nama ?? $msg->nakes?->nama ?? 'Nakes') : null,
                    'waktu'      => $msg->created_at->format('H:i'),
                    'tanggal'    => $msg->created_at->format('Y-m-d'),
                    'created_at' => $msg->created_at,
                ];
            });

       return response()->json([
            'status' => 'success',
            'data' => [
                'konsultasi' => [
                    'id'          => $konsultasi->id,
                    'chat_status' => $konsultasi->chat_status,
                    'status'      => $konsultasi->status,
                    'nakes_nama'  => $konsultasi->nakes?->user?->nama ?? $konsultasi->nakes?->nama ?? 'Nakes',
                    'nakes_profesi' => $konsultasi->nakes?->profesi ?? '-',
                    'pasien_nama'  => $konsultasi->pasien?->user?->nama ?? $konsultasi->pasien?->master?->nama ?? 'Pasien',
                    
                    // --- GANTI BARIS INI (Pakai variabel $nakes yang sudah ada) ---
                    'current_role' => $nakes ? 'nakes' : 'pasien',
                ],
                'messages' => $messages,
            ]
        ]);
    }

    // =========================================================================
    // SEND MESSAGE — Kirim pesan (pasien/nakes), auto-reply jika mode bot
    // =========================================================================

    /**
     * POST /api/chat/send
     * Body: { konsultasi_id, pesan }
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'konsultasi_id' => 'required|exists:konsultasi,id',
            'pesan'         => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        $konsultasi = Konsultasi::with(['nakes', 'pasien'])->find($request->konsultasi_id);

        // Tentukan sender berdasarkan role user
        $pasien = Pasien::where('user_id', $user->id)->first();
        $nakes = Nakes::where('user_id', $user->id)->first();

        if ($pasien && $konsultasi->pasien_id === $pasien->id) {
            $senderType = 'pasien';
        } elseif ($nakes && $konsultasi->nakes_id === $nakes->id) {
            $senderType = 'nakes';
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke sesi chat ini'
            ], 403);
        }

        // Simpan pesan pengirim
        $chat = Chat::create([
            'pasien_id'      => $konsultasi->pasien_id,
            'nakes_id'       => $konsultasi->nakes_id,
            'konsultasi_id'  => $konsultasi->id,
            'sender'         => $senderType,
            'pesan'          => $request->pesan,
        ]);
        broadcast(new MessageSent($chat))->toOthers();
        $botReply = null;

        // Jika chat_status == 'bot' DAN pengirim adalah pasien → auto-reply chatbot
        if ($konsultasi->chat_status === 'bot' && $senderType === 'pasien') {
            $botMessage = $this->generateBotReply($request->pesan);

            $botReply = Chat::create([
                'pasien_id'      => $konsultasi->pasien_id,
                'nakes_id'       => $konsultasi->nakes_id,
                'konsultasi_id'  => $konsultasi->id,
                'sender'         => 'bot',
                'pesan'          => $botMessage,
            ]);
        }

        // Jika chat_status == 'nakes' → buat notifikasi untuk pihak lain
        if ($konsultasi->chat_status === 'nakes') {
            $targetUserId = null;

            if ($senderType === 'pasien') {
                // Notifikasi ke nakes
                $targetUserId = $konsultasi->nakes?->user_id;
            } else {
                // Notifikasi ke pasien
                $targetUserId = $konsultasi->pasien?->user_id;
            }

            if ($targetUserId) {
                Notifikasi::create([
                    'user_id' => $targetUserId,
                    'judul'   => 'Pesan Baru',
                    'pesan'   => $senderType === 'pasien'
                        ? 'Pasien mengirim pesan baru di sesi konsultasi.'
                        : 'Nakes membalas pesan Anda.',
                    'tipe'    => 'chat',
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Pesan berhasil dikirim',
            'data'    => [
                'chat'      => $chat,
                'bot_reply' => $botReply,
            ]
        ]);
    }

    // =========================================================================
    // TAKE OVER CHAT — Nakes ambil alih dari chatbot (FR-T03)
    // =========================================================================

    /**
     * POST /api/chat/{konsultasiId}/takeover
     * Mengubah chat_status dari 'bot' ke 'nakes'.
     */
    public function takeOverChat($konsultasiId)
    {
        $user = auth()->user();
        $nakes = Nakes::where('user_id', $user->id)->first();

        if (!$nakes) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Hanya nakes yang bisa mengambil alih chat.'
            ], 403);
        }

        $konsultasi = Konsultasi::where('id', $konsultasiId)
            ->where('nakes_id', $nakes->id)
            ->first();

        if (!$konsultasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konsultasi tidak ditemukan atau bukan milik Anda'
            ], 404);
        }

        if ($konsultasi->chat_status === 'nakes') {
            return response()->json([
                'status' => 'info',
                'message' => 'Chat sudah dalam mode nakes'
            ]);
        }

        // Ubah status chat dari bot ke nakes
        $konsultasi->update([
            'chat_status' => 'nakes',
            'status'      => 'diterima',
        ]);

        // Kirim pesan sistem bahwa nakes sudah mengambil alih
        Chat::create([
            'pasien_id'      => $konsultasi->pasien_id,
            'nakes_id'       => $konsultasi->nakes_id,
            'konsultasi_id'  => $konsultasi->id,
            'sender'         => 'bot',
            'pesan'          => '🔔 Tenaga kesehatan telah mengambil alih percakapan ini. Anda sekarang berkomunikasi langsung dengan nakes.',
        ]);

        // Notifikasi ke pasien
        $targetUserId = $konsultasi->pasien?->user_id;
        if ($targetUserId) {
            Notifikasi::create([
                'user_id' => $targetUserId,
                'judul'   => 'Nakes Mengambil Alih Chat',
                'pesan'   => 'Tenaga kesehatan telah mengambil alih percakapan Anda. Chatbot dinonaktifkan.',
                'tipe'    => 'chat',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil alih chat dari bot',
            'data'    => $konsultasi
        ]);
    }

    // =========================================================================
    // GET ACTIVE CHATS — Nakes: Lihat semua sesi chat aktif
    // =========================================================================

    /**
     * GET /api/nakes/active-chats
     * Menampilkan semua konsultasi yang memiliki chat, untuk nakes yang login.
     */
    public function getActiveChats()
    {
        $user = auth()->user();
        $nakes = Nakes::where('user_id', $user->id)->first();

        if (!$nakes) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data nakes tidak ditemukan'
            ], 404);
        }

        $konsultasiList = Konsultasi::where('nakes_id', $nakes->id)
            ->whereIn('status', ['pending', 'diterima', 'dijadwalkan'])
            ->with([
                'pasien.user:id,nama',
                'pasien.master:id,no_reg_hiv,nama',
                'latestChat',
            ])
            ->withCount('chats')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($k) {
                return [
                    'id'              => $k->id,
                    'pasien_nama'     => $k->pasien?->user?->nama ?? $k->pasien?->master?->nama ?? 'Pasien',
                    'pasien_id'       => $k->pasien_id,
                    'status'          => $k->status,
                    'chat_status'     => $k->chat_status,
                    'tanggal'         => $k->tanggal,
                    'waktu'           => $k->waktu,
                    'chats_count'     => $k->chats_count,
                    'last_message'    => $k->latestChat?->pesan ?? 'Belum ada pesan',
                    'last_sender'     => $k->latestChat?->sender ?? null,
                    'last_message_at' => $k->latestChat?->created_at?->format('H:i') ?? '',
                    'updated_at'      => $k->updated_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $konsultasiList
        ]);
    }
}
