<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->has('guard'), function ($query) use ($request) {
                $query->where('guard_name', $request->get('guard'));
            })
            ->select(['id', 'name', 'guard_name', 'created_at', 'updated_at'])
            ->withCount('users')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'description' => 'nullable|string|max:500',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'guard_name' => $validated['guard_name'] ?? 'web',
            ]);

            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            return $role;
        });

        return response()->json([
            'success' => true,
            'data' => $role->load('permissions'),
            'message' => 'Role created successfully',
        ], 201);
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions,
                'users_count' => $role->users_count,
                'created_at' => $role->created_at->toIso8601String(),
                'updated_at' => $role->updated_at->toIso8601String(),
            ],
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'description' => 'nullable|string|max:500',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        DB::transaction(function () use ($role, $validated) {
            $role->update([
                'name' => $validated['name'] ?? $role->name,
                'description' => $validated['description'] ?? $role->description,
                'guard_name' => $validated['guard_name'] ?? $role->guard_name,
            ]);

            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }
        });

        return response()->json([
            'success' => true,
            'data' => $role->fresh()->load('permissions'),
            'message' => 'Role updated successfully',
        ]);
    }

    public function destroy(Role $role)
    {
        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot delete role with assigned users',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'mode' => 'nullable|in:sync,add,remove',
        ]);

        $mode = $validated['mode'] ?? 'sync';
        $permissions = Permission::whereIn('id', $validated['permissions'])->get();

        DB::transaction(function () use ($role, $permissions, $mode) {
            match ($mode) {
                'add' => $role->givePermissionTo($permissions),
                'remove' => $role->revokePermissionTo($permissions),
                default => $role->syncPermissions($permissions),
            };
        });

        return response()->json([
            'success' => true,
            'data' => $role->fresh()->permissions,
            'message' => 'Permissions updated successfully',
        ]);
    }
}