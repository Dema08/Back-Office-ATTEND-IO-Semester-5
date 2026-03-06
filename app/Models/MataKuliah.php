<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'nama_matkul',
        'kode_matkul',
        'sks',
    ];

    // Satu mata kuliah punya banyak kelas
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
