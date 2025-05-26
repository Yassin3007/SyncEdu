<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::with('permissions')->get();

        return RoleResource::collection($roles);

    }


    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id',$request->permissions)->pluck('name')->toArray(); ;
            $role->syncPermissions($permissions);
        }

        return  new RoleResource($role);
    }


    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return  new RoleResource($role);

    }


    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id',$request->permissions)->pluck('name')->toArray(); ;
            $role->syncPermissions($permissions);
        }

        return  new RoleResource($role);

    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
