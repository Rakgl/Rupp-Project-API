<?php

namespace App\Http\Controllers\Api\V1\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
	public function index()
    {
		$data = Permission::active()
				->notDelete()
				->select('id', 'module', 'name', 'slug')
				->get()
				->groupBy('module');
				
        $permissions = [];
        foreach($data as $key => $items){
            $permissions [] = [
                'module' => $key,
                'name' => $key,
                'slug' => $key,
                'permissions' => $items
            ];
        }
        return response()->json($permissions);
    }

    // Get permission by role
    public function permissionsByRole($roleId) 
	{
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Role not found."
            ]);
        }

        $permissions = [];
        $rolePermissions = RolePermission::where('role_id',  $roleId)->orderBy('created_at', 'desc')->get();

        if(count($rolePermissions) > 0){
            foreach ($rolePermissions as $rolePermission) {
                $permissions[] = [
                    'id' => $rolePermission->permission->id,
                    'module' => $rolePermission->permission->module,
                    'name' => $rolePermission->permission->name,
                    'slug' => $rolePermission->permission->slug,
                ];
            }
        }	
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    // Update role permission
    public function updateRolePermission(Request $request) {
        $permissionIds = $request->permission_ids;
        RolePermission::where('role_id',  $request->role_id)->delete();
        
        $rolePermission = [];
        foreach($permissionIds as $permissionId){
            $rolePermission[] = [
                'id' => Str::uuid()->toString(),
                'role_id' => $request->role_id,
                'permission_id' => $permissionId
            ];
        }
        RolePermission::insert($rolePermission);

        return response()->json([
            'success' => true,
            'message' => "Role permission have been updated.",
            'data' => $this->permissionsByRole($request->role_id)->getData(true)
        ]);
    }
}
