<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SemesterSeeder::class,
            RoleSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        $guruRole = Role::where('name', 'Guru')->first();
        $siswaRole = Role::where('name', 'Siswa')->first();

        // Admin pakai email
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        // Guru pakai email
        User::factory()->create([
            'name' => 'Guru Contoh',
            'email' => 'guru@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $guruRole->id,
        ]);

        // Siswa contoh pakai NISN
        User::factory()->create([
            'name' => 'Siswa Contoh',
            'nisn' => '1234567890',   // 🔑 Ini contoh NISN
            'email' => null,          // Boleh null kalau memang tidak pakai email
            'password' => Hash::make('password123'),
            'role_id' => $siswaRole->id,
        ]);
    }
}
