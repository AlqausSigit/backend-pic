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
        Schema::create('menu_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi_mbgs')->onDelete('cascade');
            $table->string('foto');
            $table->string('nama_menu');
            $table->integer('kalori');
            $table->integer('protein');
            $table->integer('lemak');
            $table->integer('karbohidrat');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_harian');
    }
};
