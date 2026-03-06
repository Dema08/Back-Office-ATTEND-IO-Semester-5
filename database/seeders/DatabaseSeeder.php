<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DosenSeeder::class,
            MataKuliahSeeder::class,
            KelasSeeder::class,
            MahasiswaSeeder::class,
        ]);
    }
}
