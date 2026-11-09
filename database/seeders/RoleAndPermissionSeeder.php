<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $stafPermissions = ['lihat-mahasiswa', 'tambah-mahasiswa', 'edit-mahasiswa'];
        $adminPermissions = [...$stafPermissions, 'hapus-mahasiswa'];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'Admin'])->syncPermissions($adminPermissions);
        Role::firstOrCreate(['name' => 'Staf'])->syncPermissions($stafPermissions);
    }
}
