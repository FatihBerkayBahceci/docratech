<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        $this->middleware('auth:sanctum');
        // Temporarily remove permission checks for super admin
        // $this->middleware('permission:permissions.view')->only(['index', 'show']);
        // $this->middleware('permission:permissions.create')->only(['store']);
        // $this->middleware('permission:permissions.edit')->only(['update']);
        // $this->middleware('permission:permissions.delete')->only(['destroy']);
    }

    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'module' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:system,custom',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permissions = $this->permissionService->getPermissions($request->all());
            
            return response()->json($permissions);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:permissions',
            'description' => 'sometimes|nullable|string|max:1000',
            'module' => 'required|string|max:255',
            'type' => 'required|string|in:system,custom',
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permission = $this->permissionService->createPermission($request->all());
            
            return response()->json([
                'message' => 'Permission created successfully',
                'data' => $permission
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified permission
     */
    public function show($id)
    {
        try {
            $permission = $this->permissionService->getPermission($id);
            
            return response()->json([
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Permission not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'key' => 'sometimes|required|string|max:255|unique:permissions,key,' . $id,
            'description' => 'sometimes|nullable|string|max:1000',
            'module' => 'sometimes|required|string|max:255',
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
            $permission = $this->permissionService->updatePermission($id, $request->all());
            
            return response()->json([
                'message' => 'Permission updated successfully',
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy($id)
    {
        try {
            $this->permissionService->deletePermission($id);
            
            return response()->json([
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permission modules
     */
    public function modules()
    {
        try {
            $modules = $this->permissionService->getModules();
            
            return response()->json([
                'data' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions grouped by module.
     */
    public function groupedByModule(): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsGroupedByModule();

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get active permissions.
     */
    public function active(): JsonResponse
    {
        $permissions = $this->permissionService->getActivePermissions();

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get system permissions.
     */
    public function system(): JsonResponse
    {
        $permissions = $this->permissionService->getSystemPermissions();

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get custom permissions.
     */
    public function custom(): JsonResponse
    {
        $permissions = $this->permissionService->getCustomPermissions();

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get permissions by module.
     */
    public function byModule(string $module): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsByModule($module);

        return response()->json([
            'success' => true,
            'data' => [
                'module' => $module,
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get permissions by action.
     */
    public function byAction(string $action): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsByAction($action);

        return response()->json([
            'success' => true,
            'data' => [
                'action' => $action,
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get permissions by resource.
     */
    public function byResource(string $resource): JsonResponse
    {
        $permissions = $this->permissionService->getPermissionsByResource($resource);

        return response()->json([
            'success' => true,
            'data' => [
                'resource' => $resource,
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get available actions.
     */
    public function actions(): JsonResponse
    {
        $actions = $this->permissionService->getAvailableActions();

        return response()->json([
            'success' => true,
            'data' => [
                'actions' => $actions,
            ],
        ]);
    }

    /**
     * Get available resources.
     */
    public function resources(): JsonResponse
    {
        $resources = $this->permissionService->getAvailableResources();

        return response()->json([
            'success' => true,
            'data' => [
                'resources' => $resources,
            ],
        ]);
    }

    /**
     * Get roles with specific permission.
     */
    public function roles(Permission $permission): JsonResponse
    {
        $roles = $this->permissionService->getRolesWithPermission($permission->name);

        return response()->json([
            'success' => true,
            'data' => [
                'permission' => $permission,
                'roles' => $roles,
            ],
        ]);
    }

    /**
     * Get permission statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->permissionService->getPermissionStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has permission.
     */
    public function checkUserPermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $hasPermission = $this->permissionService->userHasPermission($user, $request->permission);

        return response()->json([
            'success' => true,
            'data' => [
                'permission' => $request->permission,
                'has_permission' => $hasPermission,
            ],
        ]);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function checkUserAnyPermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $hasAnyPermission = $this->permissionService->userHasAnyPermission($user, $request->permissions);

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $request->permissions,
                'has_any_permission' => $hasAnyPermission,
            ],
        ]);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function checkUserAllPermissions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $hasAllPermissions = $this->permissionService->userHasAllPermissions($user, $request->permissions);

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $request->permissions,
                'has_all_permissions' => $hasAllPermissions,
            ],
        ]);
    }

    /**
     * Get user permissions.
     */
    public function userPermissions(Request $request): JsonResponse
    {
        $user = $request->user();
        $permissions = $this->permissionService->getUserPermissions($user);

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * Get permission audit logs.
     */
    public function auditLogs(Request $request): JsonResponse
    {
        try {
            $logs = $this->permissionService->getAuditLogs($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role-permission matrix.
     */
    public function roleMatrix(Request $request): JsonResponse
    {
        try {
            $matrix = $this->permissionService->getRolePermissionMatrix($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $matrix,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch role-permission matrix',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update role-permission matrix.
     */
    public function updateRoleMatrix(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'changes' => 'required|array',
            'changes.*.role_id' => 'required|exists:roles,id',
            'changes.*.permission_id' => 'required|exists:permissions,id',
            'changes.*.action' => 'required|in:grant,revoke',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->permissionService->updateRolePermissionMatrix($request->changes);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Role-permission matrix updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role-permission matrix',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permission templates.
     */
    public function templates(Request $request): JsonResponse
    {
        try {
            $templates = $this->permissionService->getPermissionTemplates($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $templates,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch permission templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create permission template.
     */
    public function createTemplate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permission_templates,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template = $this->permissionService->createPermissionTemplate($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $template,
                'message' => 'Permission template created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply permission template.
     */
    public function applyTemplate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:permission_templates,id',
            'target_type' => 'required|in:role,user',
            'target_id' => 'required|integer',
            'mode' => 'required|in:replace,add,remove',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->permissionService->applyPermissionTemplate($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Permission template applied successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply permission template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export audit logs.
     */
    public function exportAuditLogs(Request $request): JsonResponse
    {
        try {
            $export = $this->permissionService->exportAuditLogs($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $export,
                'message' => 'Audit logs exported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export audit logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get compliance report.
     */
    public function complianceReport(Request $request): JsonResponse
    {
        try {
            $report = $this->permissionService->generateComplianceReport($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Compliance report generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate compliance report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
