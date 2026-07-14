<?php

namespace Modules\ACL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\ACL\Entities\Permission;
use Modules\ACL\Entities\Role;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Order\Enums\OrderListTabs;
use Modules\User\Entities\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [


            [
                'name' => 'settings_edit',
                'title' => 'تنظیمات سایت',
            ],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'title' => $permission['title'],
                    'guard_name' => 'web',
                ]
            );
        }



        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'title' => 'سوپر ادمین',
                'guard_name' => 'web',
            ]
        );

        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);

        $user = User::firstOrCreate(
            ['mobile' => '09358364707'],
            [
                'name' => 'sanaz',
                'password' => Hash::make('12345678'),
                'level' => 'admin',
                'unique_code' => CodeGeneratorHelper::generate(get_class(new User()), 'unique_code'),
            ]
        );
        $user->assignRole($role);
    }
}
