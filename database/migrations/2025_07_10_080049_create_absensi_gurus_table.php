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
        Schema::create('absensi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users');
            $table->foreignId('jadwal_id')->constrained('jadwal_kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Alpha', 'Izin']);
            $table->timestamps();
            $table->unique(['guru_id', 'jadwal_id', 'tanggal']); // Prevent duplikat absensi di hari sama
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_gurus');
    }
};
