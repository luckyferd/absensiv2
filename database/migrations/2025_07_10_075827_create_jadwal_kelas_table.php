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
        Schema::create('jadwal_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('guru_id')->constrained('users');
            $table->string('hari'); // Contoh: Senin
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('deskripsi')->nullable();
            $table->string('kode_unik')->unique(); // ⬅️ Tambah kolom ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_kelas');
    }
};
