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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();

            // kalau mata kuliah dihapus, kelas yang terkait ikut kehapus
            $table->foreignId('mata_kuliah_id')
                  ->constrained('mata_kuliah')
                  ->onDelete('cascade');

            // kalau mau dosen juga ikut kehapus kelasnya, pakai cascade juga
            $table->foreignId('dosen_id')
                  ->constrained('dosen')
                  ->onDelete('cascade');

            $table->string('golongan', 20);
            $table->string('tahun_ajaran', 9)->nullable(); // contoh: 2025/2026
            $table->tinyInteger('semester')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
