<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_events', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable();
            $table->string('nim')->nullable();
            $table->string('predicted_label')->nullable();
            $table->float('confidence')->nullable();
            $table->string('image_path')->nullable(); // path file gambar (kalau dikirim)
            $table->json('raw_payload')->nullable();  // payload mentah dari Python/IoT
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_events');
    }
};
