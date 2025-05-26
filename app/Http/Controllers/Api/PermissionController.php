<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        $permissions = Permission::all();

        return PermissionResource::collection($permissions) ;
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return  new PermissionResource($permission);
    }


    public function show(Permission $permission)
    {
        return  new PermissionResource($permission);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        return new PermissionResource($permission);
    }


    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
