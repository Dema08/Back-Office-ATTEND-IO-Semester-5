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
    Schema::create('laporan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dosen_id')->constrained('dosen');
        $table->foreignId('kelas_id')->constrained('kelas');
        $table->foreignId('pertemuan_id')->constrained('pertemuan');
        $table->string('status_kehadiran')->nullable();   // misal: 'normal', 'kurang 75%'
        $table->integer('total_poin_fokus')->default(0);
        $table->string('file_excel', 255)->nullable();    // path atau URL file
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
