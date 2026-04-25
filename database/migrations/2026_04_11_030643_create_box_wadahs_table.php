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
        Schema::create('box_wadah', function (Blueprint $table) {
    $table->id();
    $table->integer('jumlah_box');
    $table->enum('status_box', ['tersedia','dipakai','rusak'])->default('tersedia');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('box_wadahs');
    }
};
