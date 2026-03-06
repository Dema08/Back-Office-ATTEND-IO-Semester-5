<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertemuan', function (Blueprint $table) {
            // Tambah dosen_id kalau belum ada
            if (!Schema::hasColumn('pertemuan', 'dosen_id')) {
                $table->foreignId('dosen_id')
                    ->nullable()
                    ->after('kelas_id')
                    ->constrained('dosen')
                    ->nullOnDelete();
            }

            // Tambah mata_kuliah_id kalau belum ada
            if (!Schema::hasColumn('pertemuan', 'mata_kuliah_id')) {
                $table->foreignId('mata_kuliah_id')
                    ->nullable()
                    ->after('dosen_id')
                    ->constrained('mata_kuliah')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pertemuan', function (Blueprint $table) {
            if (Schema::hasColumn('pertemuan', 'mata_kuliah_id')) {
                $table->dropForeign(['mata_kuliah_id']);
                $table->dropColumn('mata_kuliah_id');
            }

            if (Schema::hasColumn('pertemuan', 'dosen_id')) {
                $table->dropForeign(['dosen_id']);
                $table->dropColumn('dosen_id');
            }
        });
    }
};
