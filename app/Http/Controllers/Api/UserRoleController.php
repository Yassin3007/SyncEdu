<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Assign roles to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function assignRoles(Request $request, $userId)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = User::findOrFail($userId);
        $roles = Role::whereIn('id', $request->roles)->get();

        $user->syncRoles($roles);

        return response()->json([
            'status' => true,
            'message' => 'Roles assigned successfully',
            'data' => new UserResource($user->load('roles', 'permissions'))
        ]);
    }

    /**
     * Get user's roles and permissions.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function getUserRolesAndPermissions($userId)
    {
        $user = User::with('roles', 'permissions')->findOrFail($userId);

        return response()->json([
            'status' => true,
            'message' => 'User roles and permissions retrieved successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Remove a role from user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function removeRole(Request $request, $userId)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->role_id);

        $user->removeRole($role);

        return response()->json([
            'status' => true,
            'message' => 'Role removed from user successfully',
            'data' => new UserResource($user->load('roles', 'permissions'))
        ]);
    }
}
