<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pertemuan;
use App\Models\Kelas;

class LaporanKehadiranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pertemuan;
    public $kelas;
    public $csvContent;
    public $filename;

    /**
     * @param Pertemuan $pertemuan
     * @param Kelas     $kelas
     * @param string    $csvContent
     * @param string    $filename
     */
    public function __construct(Pertemuan $pertemuan, Kelas $kelas, string $csvContent, string $filename)
    {
        $this->pertemuan  = $pertemuan;
        $this->kelas      = $kelas;
        $this->csvContent = $csvContent;
        $this->filename   = $filename;
    }

    public function build()
    {
        return $this->subject('Laporan Kehadiran - '.$this->kelas->mataKuliah->nama_matkul)
            ->view('emails.laporan_kehadiran')
            ->attachData($this->csvContent, $this->filename, [
                'mime' => 'text/csv',
            ]);
    }
}
