<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$permissions = [
			['module' => 'ADMIN', 'name' => 'CREATE', 'slug' => 'admin:create', 'developer_only' => 0, 'status' => 'ACTIVE'],
			['module' => 'ADMIN', 'name' => 'READ', 'slug' => 'admin:read', 'developer_only' => 0, 'status' => 'ACTIVE'],
			['module' => 'ADMIN', 'name' => 'UPDATE', 'slug' => 'admin:update', 'developer_only' => 0, 'status' => 'ACTIVE'],
			['module' => 'ADMIN', 'name' => 'DELETE', 'slug' => 'admin:delete', 'developer_only' => 0, 'status' => 'ACTIVE'],
			['module' => 'STATION', 'name' => 'CREATE', 'slug' => 'station:create', 'developer_only' => 0, 'status' => 'ACTIVE'],
			['module' => 'STATION', 'name' => 'READ', 'slug' => 'station:read', 'developer_only' => 0, 'status' => 'ACTIVE'],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
    }
}
