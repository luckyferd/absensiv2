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
        Schema::create('absensi_murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('users');
            $table->foreignId('jadwal_id')->constrained('jadwal_kelas');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Alpha', 'Izin']);
            $table->string('kode_unik');
            $table->time('waktu_absen'); // waktu murid melakukan absen
            $table->string('file_surat')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->unique(['murid_id', 'jadwal_id', 'tanggal']); // Supaya 1x absen/hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_murids');
        
    }
};
