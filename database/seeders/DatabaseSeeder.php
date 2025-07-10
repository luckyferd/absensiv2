<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            SemesterSeeder::class,
        ]);
        // Seed roles terlebih dahulu
        $this->call(RoleSeeder::class);

        // Ambil role yang sudah dibuat
        $adminRole = Role::where('name', 'Admin')->first();
        $guruRole = Role::where('name', 'Guru')->first();
        $siswaRole = Role::where('name', 'Siswa')->first();

        // Buat user admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        // Buat user guru
        User::factory()->create([
            'name' => 'Guru Contoh',
            'email' => 'guru@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $guruRole->id,
        ]);

        // Buat user siswa
        User::factory()->create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $siswaRole->id,
        ]);
    }
}
