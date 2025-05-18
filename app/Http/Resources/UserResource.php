<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->whenLoaded('role')),
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'active' => (bool) $this->active,
            'salary' => $this->when($this->userHasPermissionToViewFinancial($request), $this->salary),
            'employment_status' => $this->employment_status,
            'permissions' => $this->when($request->routeIs('users.show'), $this->getPermissionNames()),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Check if the authenticated user has permission to view financial information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function userHasPermissionToViewFinancial(Request $request): bool
    {
        $user = $request->user();

        // Admin can see everything
        if ($user && ($user->hasRole('admin') || $user->hasPermissionTo('view-financial'))) {
            return true;
        }

        // Users can see their own salary
        if ($user && $user->id === $this->id) {
            return true;
        }

        return false;
    }
}
