<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('username', 'admin')->first();
        $developer =  User::where('username', 'developer')->first();

        $roleAdmin = Role::where('name', 'Super Admin')->first();
        $roleDeveloper =  Role::where('name', 'Developer')->first();

        DB::table('user_roles')->insert([
            [
                'id' => Str::uuid(),
                'user_id' => $admin->id,
                'role_id' => $roleAdmin->id,
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $developer->id,
                'role_id' => $roleDeveloper->id,
            ],
        ]);
    }
}