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

        return response()->json([
            'status' => true,
            'message' => 'Roles retrieved successfully',
            'data' => RoleResource::collection($roles)
        ]);
    }


    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully',
            'data' => new RoleResource($role->load('permissions'))
        ], 201);
    }


    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Role retrieved successfully',
            'data' => new RoleResource($role)
        ]);
    }


    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully',
            'data' => new RoleResource($role->load('permissions'))
        ]);
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}
