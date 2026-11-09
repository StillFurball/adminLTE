<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        User::factory()->create(['name' => 'Admin Demo', 'email' => 'admin@example.com'])->assignRole('Admin');
        User::factory()->create(['name' => 'Staf Demo', 'email' => 'staf@example.com'])->assignRole('Staf');

        Mahasiswa::factory(60)->recycle(Dosen::factory(10)->create())->create();
        Tugas::factory(20)->create();
    }
}
