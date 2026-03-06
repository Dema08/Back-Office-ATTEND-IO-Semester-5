<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin utama
        User::updateOrCreate(
            ['email' => 'adminattendio@gmail.com'],
            [
                'name'     => 'Admin ATTEND-IO',
                'password' => 'attendio2025', // di-hash otomatis oleh cast 'hashed'
            ]
        );
    }
}
