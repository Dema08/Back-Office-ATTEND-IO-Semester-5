<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'nip',
        'nama_dosen',
        'email',
    ];

    // Dosen mengajar banyak kelas
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    // Dosen punya banyak laporan
    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}
