<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20)->unique();
            $table->string('nama', 100);

            // email boleh kosong, tapi tetap unique kalau diisi
            $table->string('email', 100)->nullable()->unique();

            // jenis_kelamin belum dipakai di form, jadi buat nullable dulu
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            // golongan diisi dari dropdown kelas
            $table->string('golongan', 20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
