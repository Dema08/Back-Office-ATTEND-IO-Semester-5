<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'golongan',
        'mata_kuliah_id',
        'dosen_id',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // ✅ Cocokkan mahasiswa berdasarkan kolom 'golongan' (bukan kelas_id)
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'golongan', 'golongan');
    }
}
