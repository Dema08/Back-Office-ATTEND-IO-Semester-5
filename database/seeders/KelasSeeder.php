<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Dosen;
use App\Models\MataKuliah;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $dosen      = Dosen::all();
        $mataKuliah = MataKuliah::all();

        if ($dosen->isEmpty() || $mataKuliah->isEmpty()) {
            $this->command?->warn('Seeder Kelas dilewati karena Dosen / MataKuliah belum ada.');
            return;
        }

        // Ambil beberapa dosen & matkul
        $dosen1 = $dosen[0];
        $dosen2 = $dosen[1] ?? $dosen[0];
        $dosen3 = $dosen[2] ?? $dosen[0];

        $mkPCV = $mataKuliah->firstWhere('kode_matkul', 'PCV3101') ?? $mataKuliah[0];
        $mkSC  = $mataKuliah->firstWhere('kode_matkul', 'SC3102') ?? $mataKuliah[1] ?? $mataKuliah[0];
        $mkST  = $mataKuliah->firstWhere('kode_matkul', 'ST3103') ?? $mataKuliah[2] ?? $mataKuliah[0];

        $kelas = [
            // PCV: A, B, C
            [
                'mata_kuliah_id' => $mkPCV->id,
                'dosen_id'       => $dosen1->id,
                'golongan'       => 'A',
            ],
            [
                'mata_kuliah_id' => $mkPCV->id,
                'dosen_id'       => $dosen1->id,
                'golongan'       => 'B',
            ],
            [
                'mata_kuliah_id' => $mkPCV->id,
                'dosen_id'       => $dosen1->id,
                'golongan'       => 'C',
            ],

            // Sistem Cerdas: C
            [
                'mata_kuliah_id' => $mkSC->id,
                'dosen_id'       => $dosen2->id,
                'golongan'       => 'C',
            ],

            // Sistem Tertanam: A & B
            [
                'mata_kuliah_id' => $mkST->id,
                'dosen_id'       => $dosen3->id,
                'golongan'       => 'A',
            ],
            [
                'mata_kuliah_id' => $mkST->id,
                'dosen_id'       => $dosen3->id,
                'golongan'       => 'B',
            ],
        ];

        foreach ($kelas as $k) {
            Kelas::updateOrCreate(
                [
                    'mata_kuliah_id' => $k['mata_kuliah_id'],
                    'dosen_id'       => $k['dosen_id'],
                    'golongan'       => $k['golongan'],
                ],
                $k
            );
        }
    }
}
