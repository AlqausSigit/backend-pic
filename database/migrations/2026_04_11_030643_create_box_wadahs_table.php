<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('box_wadah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_box')->unique();
            $table->enum('status', [
                'tersedia',
                'dipakai',
                'dicuci',
                'rusak'
            ])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('box_wadah');
    }
};
