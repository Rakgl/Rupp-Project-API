<?php
namespace App\Http\Controllers\Api\V1\Admin\Security;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Api\V1\Admin\Security\RegisterRequest;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\Api\V1\Admin\Security\RoleResource;
use App\Http\Resources\Api\V1\Admin\Security\UserResource;
use App\Http\Resources\Api\V1\Admin\Security\PermissionResource;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // register
    public function register(RegisterRequest $request)
    {
        $request['status'] = 1;
        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'status' => 1,
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $role = Role::find(2);
        if ($role) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }

        $data = $this->getTokenAndRefreshToken($request->username, $request->password);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
