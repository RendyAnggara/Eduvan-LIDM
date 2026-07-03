<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UmumNotification extends Notification
{
    use Queueable;

    protected $judul;
    protected $pesan;
    protected $tipe;

    public function __construct($judul, $pesan, $tipe)
    {
        $this->judul = $judul;
        $this->pesan = $pesan;
        $this->tipe  = $tipe;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->judul,
            'message' => $this->pesan,
            'type'    => $this->tipe,
            'tanggal' => now()->format('d M Y')
        ];
    }
}
