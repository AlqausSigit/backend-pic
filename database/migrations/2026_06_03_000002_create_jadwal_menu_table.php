<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_menu', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_menu');
            $table->integer('kalori')->default(0);
            $table->integer('protein')->nullable();
            $table->string('waste_limit')->default('5%');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_menu');
    }
};
