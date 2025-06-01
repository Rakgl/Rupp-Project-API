<?php

namespace App\Http\Controllers\Api\V1\Admin\Security;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Resources\Api\V1\Admin\Security\RoleResource;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Admin\Security\RoleRequest;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Http\Resources\Api\V1\Admin\Security\Role\RoleIndexResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    // Get role with pagination
    public function index(Request $request)
    {
        $data = Role::where(function ($q) use ($request) {
            if($request->search) {
				$q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->search) . '%'])
                 ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            }

            if ($request->name) {
                $q->where('name', 'like', '%' . $request->name . '%');
            }

            if($request->status){
                $q->where('status', $request->status);
            }
        })
        ->with('permissions')
        ->exceptRoot()
        ->notDelete()->orderBy('created_at', 'desc')
        ->paginate($request->per_page);

        $resource = RoleIndexResource::collection($data)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => [
                'current_page' => $resource['meta']['current_page'],
                'last_page' => $resource['meta']['last_page'],
				'per_page' => $resource['meta']['per_page'], // ensure your resource includes this
                'total' => $resource['meta']['total'],
            ]
        ]);
    }

    // Create new role
    public function store(RoleRequest $request)
    {
        Role::create([
            'name' => $request->name,
            'description' => $request->description,
			'created_by' => auth()->user()->id,
			'status' => $request->status
        ]);
		
        return response()->json([
            'success' => true,
            'message' =>  "Role have been created."
        ]);
    }

    // Get role by id
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Role not found."
            ]);
        }

        return response()->json([
            'data' => new RoleIndexResource($role)
        ]);
    }

    // Update role by id
    public function update(RoleRequest $request, $id)
    {
		Log::info($request->all());
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "Role not found."
            ]);
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status, 
			'updated_by' => auth()->user()->id,
			'update_num' => $role->update_num + 1
        ]);

        return response()->json([
            'success' => true,
            'data' => new RoleIndexResource($role),
            'message' => "Role have been updated."
        ]);
    }

    // Delete role
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => "User not found."
            ]);
        }

        $role->update([
            'status' => Helper::deleted(),
			'updated_by' => auth()->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Role have been deleted."
        ]);
    }

    // get active role
    public function active(Request $request)
    {
        $data = Role::where(function ($q) use ($request) {
            if($request->search) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                 ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            }
        })
        ->exceptRoot()
        ->active()
        ->notDelete()->orderBy('created_at', 'desc')
        ->paginate($request->per_page);

        $resource = RoleIndexResource::collection($data)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => [
                'current_page' => $resource['meta']['current_page'],
                'last_page' => $resource['meta']['last_page'],
                'total' => $resource['meta']['total'],
            ]
        ]);
    }

	public function audit($id)
	{
		$role = Role::find($id);

		if (!$role) {
			return response()->json([
				'success' => false,
				'message' => "Role not found."
			]);
		}
		$audits = $role->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}
}
