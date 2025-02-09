<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use App\Traits\Status;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isRole = Role::where('private', Status::$status_active)->first();

            $allPermissions = Permission::all()->pluck('id')->toArray();
            if (!$isRole){
                // new Super admin role
                $role = new Role();
                $role->name = 'Super private admin';
                $role->private = Status::$status_active;
                $role->save();

                foreach ($allPermissions as $permission){
                    $modelPermission = Permission::find($permission);
                    $menu = Menu::find($modelPermission->menu_id);
                    if ($menu){
                        $model = new RolePermission();
                        $model->role_id = $role->id;
                        $model->permission_id = $permission;
                        $model->permission_name = $menu->name.'-'.$modelPermission->name;
                        $model->save();
                    }
                }
            }

            $defaultRole = new Role();
            $defaultRole->name = 'Admin';
            $defaultRole->save();

            foreach ($allPermissions as $permission){
                $modelPermission = Permission::find($permission);
                $menu = Menu::find($modelPermission->menu_id);
                if ($menu){
                    $model = new RolePermission();
                    $model->role_id = $defaultRole->id;
                    $model->permission_id = $permission;
                    $model->permission_name = $menu->name.'-'.$modelPermission->name;
                    $model->save();
                }
            }

    }
}
