<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Semester::create([
            'nama' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        Semester::create([
            'nama' => 'Genap',
            'tahun_ajaran' => '2025/2026',
            'is_active' => false,
        ]);
    }
}
