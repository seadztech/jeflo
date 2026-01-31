<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */
            $permissions = [

                // Dashboard
                'view dashboard',

                // Users & Roles
                'view users',
                'create users',
                'edit users',

                'view roles',
                'create roles',
                'edit roles',

                // Branches
                'view branches',
                'create branches',
                'edit branches',

                // Items & Stock
                'view item types',
                'create item types',
                'edit item types',

                'view items',
                'create items',
                'edit items',

                'view stock changes',

                // POS & Sales
                'access pos',

                'view sales',
                'create sales',

                'view receipts',

                // Transactions & Reports
                'view transactions',
                'view reports',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */

            // Admin – full access
            $admin = Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);
            $admin->syncPermissions(Permission::all());

            // Manager
            $manager = Role::firstOrCreate([
                'name' => 'manager',
                'guard_name' => 'web',
            ]);
            $manager->syncPermissions([
                'view dashboard',

                'view users',

                'view branches',
                'create branches',
                'edit branches',

                'view items',
                'create items',
                'edit items',

                'view item types',

                'view stock changes',

                'view sales',
                'view transactions',

                'view reports',
            ]);

            // Cashier
            $cashier = Role::firstOrCreate([
                'name' => 'cashier',
                'guard_name' => 'web',
            ]);
            $cashier->syncPermissions([
                'view dashboard',

                'access pos',

                'create sales',
                'view sales',

                'view receipts',
            ]);

            // Storekeeper
            $storekeeper = Role::firstOrCreate([
                'name' => 'storekeeper',
                'guard_name' => 'web',
            ]);
            $storekeeper->syncPermissions([
                'view dashboard',

                'view item types',
                'create item types',
                'edit item types',

                'view items',
                'create items',
                'edit items',

                'view stock changes',
            ]);

            // Auditor (Read-only)
            $auditor = Role::firstOrCreate([
                'name' => 'auditor',
                'guard_name' => 'web',
            ]);
            $auditor->syncPermissions([
                'view dashboard',
                'view sales',
                'view transactions',
                'view reports',
            ]);
        });
    }
}
