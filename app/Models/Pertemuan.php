<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    use HasFactory;

    protected $table = 'pertemuan';

    protected $fillable = [
        'kelas_id',
        'dosen_id',
        'mata_kuliah_id',
        'minggu_ke',
        'acara_ke',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
    ];

    protected $dates = [
        'tanggal',
    ];

    // ============== RELASI ==============

    // Pertemuan milik satu kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Pertemuan milik satu dosen (dipilih saat mulai pertemuan)
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    // Pertemuan terkait satu mata kuliah (dipilih saat mulai pertemuan)
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    // Relasi kehadiran (kalau nanti mau dipakai)
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'pertemuan_id');
    }
}
