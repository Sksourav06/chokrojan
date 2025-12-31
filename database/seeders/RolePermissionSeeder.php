<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure tables are clear before seeding (optional, but good practice)
        // Note: Spatie recommends running permissions first.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();

        try {
            // --- 1. Define and Create Permissions (10 Permissions) ---
            $permissions = [
                'Can Book Ticket',
                'Can Book Vip Ticket',
                'Can Issue Ticket',
                'Can Cancel Booked Ticket',
                'Can Cancel Issued Ticket',
                'Can Cancel All',
                'Can Set Goods Charge',
                'Can View Other Tickets',
                'Can Set Discount',
                'Can Set Callerman Commission',
            ];

            $permissionModels = [];
            foreach ($permissions as $permission) {
                $permissionModels[] = Permission::firstOrCreate(['name' => $permission]);
            }

            $allPermissionNames = collect($permissionModels)->pluck('name')->toArray();

            // --- 2. Define and Create Roles ---

            $roles = [
                // Role 1: Super Admin (Gets all permissions)
                'Super Admin',
                'Admin',
                'Counter Master',
                'Counter Manager',
                'Accountant',
                'Manager',
                'Supervisor',
            ];

            foreach ($roles as $roleName) {
                Role::firstOrCreate(['name' => $roleName]);
            }

            // --- 3. Assign All Permissions to Super Admin ---

            // Find the Super Admin role model
            $superAdminRole = Role::findByName('Super Admin');

            // Give all defined permissions to the Super Admin role
            $superAdminRole->syncPermissions($allPermissionNames);

            // You can assign specific permissions to other roles here if needed.
            // Example:
            // $counterManagerRole = Role::findByName('Counter Manager');
            // $counterManagerRole->givePermissionTo(['Can Book Ticket', 'Can Issue Ticket', 'Can View Other Tickets']);

            DB::commit();

            $this->command->info('✅ Roles and Permissions seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding roles and permissions: ' . $e->getMessage());
        }
    }
}