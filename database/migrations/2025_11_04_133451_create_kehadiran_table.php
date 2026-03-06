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
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pertemuan_id')
                ->constrained('pertemuan')
                ->onDelete('cascade');

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->onDelete('cascade');

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswa')
                ->onDelete('cascade');

            // 0 = tidak hadir, 1 = hadir
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0 = tidak hadir, 1 = hadir');

            $table->integer('poin_fokus')->default(0);
            $table->timestamps();

            // 1 mhs hanya 1 record per pertemuan
            $table->unique(['pertemuan_id', 'mahasiswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
