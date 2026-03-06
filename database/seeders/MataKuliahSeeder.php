<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $mataKuliah = [
            [
                'nama_matkul' => 'Pengolahan Citra dan Visi',
                'kode_matkul' => 'PCV3101',
                'sks'         => 3,
            ],
            [
                'nama_matkul' => 'Sistem Cerdas',
                'kode_matkul' => 'SC3102',
                'sks'         => 3,
            ],
            [
                'nama_matkul' => 'Sistem Tertanam',
                'kode_matkul' => 'ST3103',
                'sks'         => 3,
            ],
            [
                'nama_matkul' => 'Jaringan Komputer',
                'kode_matkul' => 'JK3104',
                'sks'         => 3,
            ],
            [
                'nama_matkul' => 'Manajemen Proyek TI',
                'kode_matkul' => 'MP3105',
                'sks'         => 2,
            ],
        ];

        foreach ($mataKuliah as $mk) {
            MataKuliah::updateOrCreate(
                ['kode_matkul' => $mk['kode_matkul']],
                $mk
            );
        }
    }
}
