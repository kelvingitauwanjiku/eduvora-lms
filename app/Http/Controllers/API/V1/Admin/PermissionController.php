<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->has('guard'), function ($query) use ($request) {
                $query->where('guard_name', $request->get('guard'));
            })
            ->when($request->has('group'), function ($query) use ($request) {
                $query->where('group', $request->get('group'));
            })
            ->select(['id', 'name', 'description', 'group', 'guard_name', 'created_at'])
            ->withCount('roles')
            ->paginate($request->get('per_page', 15));

        $groupedPermissions = Permission::select('group')
            ->distinct()
            ->whereNotNull('group')
            ->pluck('group');

        return response()->json([
            'success' => true,
            'data' => $permissions,
            'groups' => $groupedPermissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
            'description' => 'nullable|string|max:500',
            'group' => 'nullable|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $permission = DB::transaction(function () use ($validated) {
            $permission = Permission::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'group' => $validated['group'] ?? null,
                'guard_name' => $validated['guard_name'] ?? 'web',
            ]);

            if (!empty($validated['roles'])) {
                $roles = Role::whereIn('id', $validated['roles'])->get();
                foreach ($roles as $role) {
                    $role->givePermissionTo($permission);
                }
            }

            return $permission;
        });

        return response()->json([
            'success' => true,
            'data' => $permission->load('roles'),
            'message' => 'Permission created successfully',
        ], 201);
    }

    public function show(Permission $permission)
    {
        $permission->load(['roles', 'users:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $permission->description,
                'group' => $permission->group,
                'guard_name' => $permission->guard_name,
                'roles' => $permission->roles,
                'users_count' => $permission->users_count,
                'created_at' => $permission->created_at->toIso8601String(),
                'updated_at' => $permission->updated_at->toIso8601String(),
            ],
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'description' => 'nullable|string|max:500',
            'group' => 'nullable|string|max:255',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'data' => $permission->fresh(),
            'message' => 'Permission updated successfully',
        ]);
    }

    public function destroy(Permission $permission)
    {
        if ($permission->users_count > 0) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot delete permission assigned to users',
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully',
        ]);
    }

    public function assignToRoles(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'mode' => 'nullable|in:sync,add,remove',
        ]);

        $mode = $validated['mode'] ?? 'sync';
        $roles = Role::whereIn('id', $validated['roles'])->get();

        DB::transaction(function () use ($permission, $roles, $mode) {
            match ($mode) {
                'add' => $permission->assignRole($roles),
                'remove' => $permission->removeRole($roles),
                default => $permission->syncRoles($roles),
            };
        });

        return response()->json([
            'success' => true,
            'data' => $permission->fresh()->roles,
            'message' => 'Roles updated successfully',
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.name' => 'required|string|max:255',
            'permissions.*.description' => 'nullable|string|max:500',
            'permissions.*.group' => 'nullable|string|max:255',
            'permissions.*.guard_name' => 'nullable|string|max:255',
        ]);

        $created = [];
        $errors = [];

        DB::transaction(function () use ($request, &$created, &$errors) {
            foreach ($request->input('permissions') as $index => $permData) {
                $exists = Permission::where('name', $permData['name'])->exists();
                
                if ($exists) {
                    $errors[$index] = "Permission '{$permData['name']}' already exists";
                    continue;
                }

                $permission = Permission::create([
                    'name' => $permData['name'],
                    'description' => $permData['description'] ?? null,
                    'group' => $permData['group'] ?? null,
                    'guard_name' => $permData['guard_name'] ?? 'web',
                ]);

                $created[] = $permission;
            }
        });

        return response()->json([
            'success' => true,
            'data' => [
                'created' => $created,
                'errors' => $errors,
            ],
            'message' => count($created) . ' permissions created',
        ]);
    }

    public function groups(Request $request)
    {
        $groups = Permission::select('group')
            ->distinct()
            ->whereNotNull('group')
            ->orderBy('group')
            ->get()
            ->map(function ($permission) {
                $count = Permission::where('group', $permission->group)->count();
                return [
                    'name' => $permission->group,
                    'count' => $count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }
}