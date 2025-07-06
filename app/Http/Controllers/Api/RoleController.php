<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        // Note: Middleware will be applied in routes/api.php instead
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        try {
            // Get roles from service with simplified parameters
            $params = [];
            if ($request->has('page')) $params['page'] = $request->get('page');
            if ($request->has('per_page')) $params['per_page'] = $request->get('per_page');
            if ($request->has('search')) $params['search'] = $request->get('search');
            if ($request->has('type')) $params['type'] = $request->get('type');
            if ($request->has('status')) $params['status'] = $request->get('status');
            
            $roles = $this->roleService->getRoles($params);
            
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'sometimes|nullable|string|max:1000',
            'type' => 'required|string|in:system,custom',
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = $this->roleService->createRole($request->all());
            
            return response()->json([
                'message' => 'Role created successfully',
                'data' => $role
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role
     */
    public function show($id)
    {
        try {
            $role = $this->roleService->getRole($id);
            
            return response()->json([
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Role not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $id,
            'description' => 'sometimes|nullable|string|max:1000',
            'type' => 'sometimes|required|string|in:system,custom',
            'status' => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = $this->roleService->updateRole($id, $request->all());
            
            return response()->json([
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        try {
            $this->roleService->deleteRole($id);
            
            return response()->json([
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role permissions
     */
    public function permissions($id)
    {
        try {
            $permissions = $this->roleService->getRolePermissions($id);
            
            return response()->json([
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch role permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->roleService->assignPermissions($id, $request->permissions);
            
            return response()->json([
                'message' => 'Permissions assigned successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role hierarchy.
     */
    public function hierarchy(): JsonResponse
    {
        $hierarchy = $this->roleService->getRoleHierarchy();

        return response()->json([
            'success' => true,
            'data' => [
                'hierarchy' => $hierarchy,
            ],
        ]);
    }

    /**
     * Get active roles.
     */
    public function active(): JsonResponse
    {
        $roles = $this->roleService->getActiveRoles();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles,
            ],
        ]);
    }

    /**
     * Get system roles.
     */
    public function system(): JsonResponse
    {
        $roles = $this->roleService->getSystemRoles();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles,
            ],
        ]);
    }

    /**
     * Get custom roles.
     */
    public function custom(): JsonResponse
    {
        $roles = $this->roleService->getCustomRoles();

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $roles,
            ],
        ]);
    }

    /**
     * Get users with specific role.
     */
    public function users(Role $role): JsonResponse
    {
        $users = $this->roleService->getUsersWithRole($role);

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
            ],
        ]);
    }

    /**
     * Get role statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->roleService->getRoleStatistics();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $statistics,
            ],
        ]);
    }
}
