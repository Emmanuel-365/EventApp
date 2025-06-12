<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TenantPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            /** PERMISSIONS EMPLOYEE LIEES AUX EVENTS */
            [
                'name' => 'see-events',
                'categorie' => 'manage-events',
                'guard_name' => 'employee',
            ],
            [
                'name' => 'create-events',
                'categorie' => 'manage-events',
                'guard_name' => 'employee',
            ],
            [
                'name' => 'delete-events',
                'categorie' => 'manage-events',
                'guard_name' => 'employee',
            ],
            [
                'name' => 'update-events',
                'categorie' => 'manage-events',
                'guard_name' => 'employee',
            ],
            [
                'name' => 'restore-events',
                'categorie' => 'manage-events',
                'guard_name' => 'employee',
            ],
            /** PERMISSIONS ADMIN LIEES A LA COMMUNICATION */

            [
                'name' => 'see-messages',
                'categorie' => 'communication',
                'guard_name' => 'employee',
            ],
            [
                'name' => 'reply-messages',
                'categorie' => 'communication',
                'guard_name' => 'employee',
            ],

        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => $perm['guard_name']],
                ['categorie' => $perm['categorie']]
            );
        }
    }
}
