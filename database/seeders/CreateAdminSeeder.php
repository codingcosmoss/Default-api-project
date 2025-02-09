<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Traits\Status;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('private', Status::$status_active)
        ->first();

        $admin = new User(); 
        $admin->name = 'Super Admin'; 
        $admin->phone = 998999999999; 
        $admin->password = Hash::make('12345678'); 
        $admin->role_id = $role->id; 
        $admin->save(); 
    }
}
