<?php

namespace App\Models;
use App\Models\RfidTag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'email',
        'golongan',
        'jenis_kelamin',
    ];

    // Opsional: biar bisa akses $mhs->kelas
    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'golongan', 'golongan');
    }

public function rfidTags()
{
    return $this->hasMany(RfidTag::class);
}

// Kalau cukup 1 kartu per mahasiswa:
public function rfidTag()
{
    return $this->hasOne(RfidTag::class);
}
}
