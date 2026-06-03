<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('email')->nullable()->unique()->after('username');

            $table->string('foto')->nullable()->after('password');

            $table->string('nis')->nullable()->unique()->after('role');

            $table->foreignId('kelas_id')
                  ->nullable()
                  ->after('nis')
                  ->constrained('kelas')
                  ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['kelas_id']);

            $table->dropColumn([
                'email',
                'foto',
                'nis',
                'kelas_id'
            ]);
        });
    }
};