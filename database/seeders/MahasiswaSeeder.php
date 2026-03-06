<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // 5 mahasiswa khusus golongan C (wajib ada)
        $mhsGolCFixed = [
            [
                'nim'           => 'E41231272',
                'nama'          => 'dema',
                'email'         => 'E41231272@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231328',
                'nama'          => 'muqid',
                'email'         => 'E41231328@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231326',
                'nama'          => 'yusron',
                'email'         => 'E41231326@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231216',
                'nama'          => 'nandita',
                'email'         => 'E41231216@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231177',
                'nama'          => 'edwin',
                'email'         => 'E41231177@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
        ];

        // Tambahan 5 dummy supaya golongan C total 10
        $mhsGolCExtra = [
            [
                'nim'           => 'E41233001',
                'nama'          => 'Mahasiswa C1',
                'email'         => 'E41233001@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41233002',
                'nama'          => 'Mahasiswa C2',
                'email'         => 'E41233002@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41233003',
                'nama'          => 'Mahasiswa C3',
                'email'         => 'E41233003@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41233004',
                'nama'          => 'Mahasiswa C4',
                'email'         => 'E41233004@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41233005',
                'nama'          => 'Mahasiswa C5',
                'email'         => 'E41233005@attendio.test',
                'golongan'      => 'C',
                'jenis_kelamin' => 'L',
            ],
        ];

        // 10 mahasiswa golongan A
        $mhsGolA = [
            [
                'nim'           => 'E41231001',
                'nama'          => 'Mahasiswa A1',
                'email'         => 'E41231001@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231002',
                'nama'          => 'Mahasiswa A2',
                'email'         => 'E41231002@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231003',
                'nama'          => 'Mahasiswa A3',
                'email'         => 'E41231003@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231004',
                'nama'          => 'Mahasiswa A4',
                'email'         => 'E41231004@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231005',
                'nama'          => 'Mahasiswa A5',
                'email'         => 'E41231005@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231006',
                'nama'          => 'Mahasiswa A6',
                'email'         => 'E41231006@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231007',
                'nama'          => 'Mahasiswa A7',
                'email'         => 'E41231007@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231008',
                'nama'          => 'Mahasiswa A8',
                'email'         => 'E41231008@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41231009',
                'nama'          => 'Mahasiswa A9',
                'email'         => 'E41231009@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41231010',
                'nama'          => 'Mahasiswa A10',
                'email'         => 'E41231010@attendio.test',
                'golongan'      => 'A',
                'jenis_kelamin' => 'P',
            ],
        ];

        // 10 mahasiswa golongan B
        $mhsGolB = [
            [
                'nim'           => 'E41232001',
                'nama'          => 'Mahasiswa B1',
                'email'         => 'E41232001@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41232002',
                'nama'          => 'Mahasiswa B2',
                'email'         => 'E41232002@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41232003',
                'nama'          => 'Mahasiswa B3',
                'email'         => 'E41232003@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41232004',
                'nama'          => 'Mahasiswa B4',
                'email'         => 'E41232004@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41232005',
                'nama'          => 'Mahasiswa B5',
                'email'         => 'E41232005@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41232006',
                'nama'          => 'Mahasiswa B6',
                'email'         => 'E41232006@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41232007',
                'nama'          => 'Mahasiswa B7',
                'email'         => 'E41232007@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41232008',
                'nama'          => 'Mahasiswa B8',
                'email'         => 'E41232008@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'P',
            ],
            [
                'nim'           => 'E41232009',
                'nama'          => 'Mahasiswa B9',
                'email'         => 'E41232009@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'L',
            ],
            [
                'nim'           => 'E41232010',
                'nama'          => 'Mahasiswa B10',
                'email'         => 'E41232010@attendio.test',
                'golongan'      => 'B',
                'jenis_kelamin' => 'P',
            ],
        ];

        $all = array_merge($mhsGolA, $mhsGolB, $mhsGolCFixed, $mhsGolCExtra);

        foreach ($all as $m) {
            Mahasiswa::updateOrCreate(
                ['nim' => $m['nim']],
                $m
            );
        }
    }
}
