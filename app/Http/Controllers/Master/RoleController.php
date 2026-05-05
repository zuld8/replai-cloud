<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mastter\AssignPermissionRequest;
use App\Http\Requests\Mastter\RoleRequest;
use App\Models\ModulFiture;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Master\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class RoleController extends Controller
{

    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService     = $roleService;
    }


    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getData($request);
        return view('master.role.index', ['page' => 'Daftar Role'], compact('roles'));
    }

    /**
     * Store a newly created role
     */
    public function store(RoleRequest $request)
    {
        $role = $this->roleService->createData($request);

        return response()->json([
            'message'   => 'Role berhasil dibuat',
            'data'      => [
                'id'        => $role->id,
                'name'      => $role->name,
            ],
        ], 201);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['permissions.modulFitures', 'users']);

        $modules = ModulFiture::with('permissions')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('master.role.permission', ['page' => 'Daftar Permission'], compact('role', 'modules'));
    }

    /**
     * Update the specified role
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $this->roleService->updateData($request, $role);
        $role->fresh();

        return response()->json([
            'message'   => 'Role berhasil di perbaharui',
            'data'      => [
                'id'        => $role->id,
                'name'      => $role->name,
            ],
        ], 201);
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role): JsonResponse
    {

        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Role tidak dapat dihapus karena masih digunakan oleh user',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role berhasil dihapus',
        ]);
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        $permissions = Permission::whereIn('id', $request->permission_ids)->get();
        $role->syncPermissions($permissions);

        $role->load('permissions.modulFitures');

        return response()->json([
            'message' => 'Permission berhasil di-assign ke role'
        ]);
    }


    /**
     * Add single or multiple permissions to role
     */
    public function addPermission(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|uuid|exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        // Spatie support array, gak perlu loop
        $role->givePermissionTo($permissions);

        $role->load('permissions');

        return response()->json([
            'message' => count($request->permission_ids) > 1 ? 'Permissions berhasil ditambahkan' : 'Permission berhasil ditambahkan',
        ]);
    }

    /**
     * Remove single or multiple permissions from role
     */
    public function removePermission(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|uuid|exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids)->get();

        // Spatie support array, gak perlu loop
        $role->revokePermissionTo($permissions);

        $role->load('permissions');

        return response()->json([
            'message' => count($request->permission_ids) > 1  ? 'Permissions berhasil dihapus' : 'Permission berhasil dihapus',
        ]);
    }

    public function getPermissions(Role $role): JsonResponse
    {
        $modules = ModulFiture::with('permissions')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'role_name' => $role->name,
            'modules' => $modules,
            'role_permissions' => $rolePermissions
        ]);
    }
}
