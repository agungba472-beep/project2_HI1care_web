<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat->load(['konsultasi.pasien', 'konsultasi.nakes']);
    }

    public function broadcastOn()
    {
        // Gunakan konsultasi_id sebagai channel — semua pihak dalam satu sesi
        // konsultasi yang sama mendengarkan channel yang sama.
        return new PrivateChannel('konsultasi.' . $this->chat->konsultasi_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'chat' => [
                'id'            => $this->chat->id,
                'konsultasi_id' => $this->chat->konsultasi_id,
                'sender'        => $this->chat->sender,
                'pesan'         => $this->chat->pesan,
                'nakes_nama'    => $this->chat->sender === 'nakes'
                    ? ($this->chat->nakes?->user?->nama ?? $this->chat->nakes?->nama ?? 'Nakes')
                    : null,
                'waktu'         => $this->chat->created_at->format('H:i'),
                'created_at'    => $this->chat->created_at,
            ],
        ];
    }
}