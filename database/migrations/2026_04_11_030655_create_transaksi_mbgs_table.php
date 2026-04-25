<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_mbgs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('box_id')->constrained('box_wadah')->cascadeOnDelete();

            $table->integer('jumlah_porsi');
            $table->date('tanggal');
            $table->time('jam_ambil')->nullable();
            $table->time('jam_kembali')->nullable();
            $table->enum('status', ['proses','diambil','dikembalikan']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_mbgs');
    }
};