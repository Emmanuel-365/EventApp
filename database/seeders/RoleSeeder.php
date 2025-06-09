<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            /** PERMISSIONS ADMIN LIEES AUX ORGANIZERS */
            [
                'name' => 'see-organizer-profile',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'validate-organizer-profile',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'reject-organizer-profile',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'ban-organizer',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'unban-organizer',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],

            /** PERMISSIONS ADMIN LIEES AUX ORGANIZATIONS */


            [
                'name' => 'see-organization',
                'categorie' => 'manage-organization',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'validate-organization',
                'categorie' => 'manage-organization',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'reject-organization',
                'categorie' => 'manage-organization',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'enable-organization',
                'categorie' => 'manage-organization',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'disable-organization',
                'categorie' => 'manage-organization',
                'guard_name' => 'admin',
            ],

            /** PERMISSIONS ADMIN LIEES A LA COMMUNICATION */

            [
                'name' => 'see-messages',
                'categorie' => 'communication',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'reply-messages',
                'categorie' => 'communication',
                'guard_name' => 'admin',
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
