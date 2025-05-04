<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        Permission::firstOrCreate(['name' => 'manage allocation', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage section', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage reallocation', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage arranging', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage rearranging', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage blog', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage comments', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'manage meetingRecords', 'guard_name' => 'api']);

        // Roles
        $staff   = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'api']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'api']);
        $tutor   = Role::firstOrCreate(['name' => 'tutor', 'guard_name' => 'api']);

        // Assign Permissions
        $staff->syncPermissions(['manage allocation', 'manage section', 'manage reallocation']);
        $student->syncPermissions(['manage arranging', 'manage rearranging', 'manage blog', 'manage comments']);
        $tutor->syncPermissions(['manage arranging', 'manage rearranging', 'manage blog', 'manage comments', 'manage meetingRecords']);
    }
}
