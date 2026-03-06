<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = [
            [
                'nip'        => '197001011234567890',
                'nama_dosen' => 'Dr. Budi Santoso',
                'email'      => 'budi.dosen@attendio.test',
            ],
            [
                'nip'        => '198002023456789012',
                'nama_dosen' => 'Dr. Siti Aminah',
                'email'      => 'siti.dosen@attendio.test',
            ],
            [
                'nip'        => '197512123456789011',
                'nama_dosen' => 'Dr. Agus Prasetyo',
                'email'      => 'agus.dosen@attendio.test',
            ],
            [
                'nip'        => '198811223456789033',
                'nama_dosen' => 'Dr. Rina Kartika',
                'email'      => 'rina.dosen@attendio.test',
            ],
        ];

        foreach ($dosen as $d) {
            Dosen::updateOrCreate(
                ['email' => $d['email']],
                $d
            );
        }
    }
}
