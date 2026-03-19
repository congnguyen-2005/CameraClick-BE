<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RolePermissionSeeder extends Seeder
{   
public function run() {
    // Tạo quyền
    Permission::create(['name' => 'manage products']);
    Permission::create(['name' => 'view dashboard']);

    // Tạo vai trò và gán quyền
    $adminRole = Role::create(['name' => 'admin']);
    $adminRole->givePermissionTo(Permission::all());

    $staffRole = Role::create(['name' => 'staff']);
    $staffRole->givePermissionTo('manage products');

    // Gán vai trò cho user ID số 1
    $user = User::find(1);
    $user->assignRole('admin');
}
}