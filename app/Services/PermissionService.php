<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionService
{
    /**
     * Get permissions with filters
     */
    public function getPermissions($filters = [])
    {
        $query = Permission::with(['roles'])
            ->orderBy('module', 'asc')
            ->orderBy('name', 'asc');

        // Apply search filter
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply module filter
        if (isset($filters['module']) && $filters['module']) {
            $query->where('module', $filters['module']);
        }

        // Apply type filter
        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Create new permission (backward compatibility)
     */
    public function createPermission($data)
    {
        // Convert old format to new format for compatibility
        $newData = [
            'name' => $data['key'] ?? $data['name'],
            'display_name' => $data['name'],
            'description' => $data['description'] ?? null,
            'module' => $data['module'],
            'action' => $data['action'] ?? 'manage',
            'resource' => $data['resource'] ?? null,
            'is_active' => ($data['status'] ?? 'active') === 'active',
            'priority' => $data['priority'] ?? 0,
        ];

        $user = auth()->user() ?? new \App\Models\User();
        
        return $this->createPermissionNew($newData, $user);
    }

    /**
     * Get permission by ID
     */
    public function getPermission($id)
    {
        $permission = Permission::with(['roles'])->findOrFail($id);
        
        // Add additional data
        $permission->load(['auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return $permission;
    }

    /**
     * Update permission
     */
    public function updatePermission($id, $data)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);

            // Check if system permission can be modified
            if ($permission->type === 'system' && isset($data['type']) && $data['type'] !== 'system') {
                throw new \Exception('System permissions cannot be changed to custom type');
            }

            $permission->update([
                'name' => $data['name'] ?? $permission->name,
                'key' => $data['key'] ?? $permission->key,
                'description' => $data['description'] ?? $permission->description,
                'module' => $data['module'] ?? $permission->module,
                'type' => $data['type'] ?? $permission->type,
                'status' => $data['status'] ?? $permission->status,
            ]);

            // Log activity
            $this->logActivity($permission, 'update', 'Permission updated successfully');

            DB::commit();

            return $permission->load('roles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete permission
     */
    public function deletePermission($id)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::findOrFail($id);

            // Check if permission can be deleted
            if ($permission->type === 'system') {
                throw new \Exception('System permissions cannot be deleted');
            }

            // Check if permission is assigned to roles
            if ($permission->roles()->count() > 0) {
                throw new \Exception('Cannot delete permission assigned to roles');
            }

            // Log activity before deletion
            $this->logActivity($permission, 'delete', 'Permission deleted successfully');

            // Delete permission
            $permission->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get permission modules
     */
    public function getModules()
    {
        $modules = Permission::select('module')
            ->distinct()
            ->where('status', 'active')
            ->orderBy('module')
            ->get()
            ->map(function ($permission) {
                return [
                    'name' => $permission->module,
                    'description' => $this->getModuleDescription($permission->module),
                    'icon' => $this->getModuleIcon($permission->module),
                    'roles' => Role::whereHas('permissions', function ($query) use ($permission) {
                        $query->where('module', $permission->module);
                    })->count(),
                    'status' => 'active'
                ];
            });

        return $modules;
    }

    /**
     * Generate permission key
     */
    public function generatePermissionKey($name, $module)
    {
        $baseKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $name));
        $key = "{$module}.{$baseKey}";
        $counter = 1;
        
        while (Permission::where('key', $key)->exists()) {
            $key = "{$module}.{$baseKey}.{$counter}";
            $counter++;
        }
        
        return $key;
    }

    /**
     * Validate permission key
     */
    public function validatePermissionKey($key, $excludeId = null)
    {
        $query = Permission::where('key', $key);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStats()
    {
        return [
            'total' => Permission::count(),
            'active' => Permission::where('status', 'active')->count(),
            'custom' => Permission::where('type', 'custom')->count(),
            'system' => Permission::where('type', 'system')->count(),
            'modules' => Permission::distinct('module')->count(),
            'assigned' => Permission::has('roles')->count(),
            'unassigned' => Permission::doesntHave('roles')->count(),
        ];
    }

    /**
     * Get module description
     */
    private function getModuleDescription($module)
    {
        $descriptions = [
            'users' => 'User management and administration',
            'roles' => 'Role management and permissions',
            'permissions' => 'Permission management',
            'dashboard' => 'Dashboard and analytics',
            'activity' => 'Activity logs and monitoring',
            'logs' => 'System logs and debugging',
            'notifications' => 'Notification management',
            'settings' => 'System settings and configuration',
            'support' => 'Support and help system',
        ];

        return $descriptions[$module] ?? 'Module for ' . ucfirst($module);
    }

    /**
     * Get module icon
     */
    private function getModuleIcon($module)
    {
        $icons = [
            'users' => 'users',
            'roles' => 'shield-check',
            'permissions' => 'key',
            'dashboard' => 'chart-bar',
            'activity' => 'activity',
            'logs' => 'document-text',
            'notifications' => 'bell',
            'settings' => 'cog',
            'support' => 'question-mark-circle',
        ];

        return $icons[$module] ?? 'cube';
    }

    /**
     * Log activity
     */
    private function logActivity($permission, $event, $description)
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Permission::class,
            'subject_id' => $permission->id,
            'event' => $event,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'url' => request()->url(),
                'method' => request()->method(),
            ],
        ]);
    }

    /**
     * Create a new permission.
     */
    public function createPermissionNew(array $data, User $createdBy): Permission
    {
        DB::beginTransaction();

        try {
            $permission = Permission::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
                'module' => $data['module'] ?? null,
                'action' => $data['action'],
                'resource' => $data['resource'] ?? null,
                'is_system' => false,
                'is_active' => $data['is_active'] ?? true,
                'priority' => $data['priority'] ?? 0,
            ]);

            $this->logAuditEvent('permission', 'create', $createdBy, "Permission '{$permission->display_name}' created", $permission);

            DB::commit();

            return $permission;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing permission (new style).
     */
    public function updatePermissionNew(Permission $permission, array $data, User $updatedBy): Permission
    {
        if ($permission->is_system) {
            throw new \Exception('System permissions cannot be modified');
        }

        DB::beginTransaction();

        try {
            $oldData = $permission->toArray();

            $permission->update([
                'display_name' => $data['display_name'] ?? $permission->display_name,
                'description' => $data['description'] ?? $permission->description,
                'module' => $data['module'] ?? $permission->module,
                'action' => $data['action'] ?? $permission->action,
                'resource' => $data['resource'] ?? $permission->resource,
                'is_active' => $data['is_active'] ?? $permission->is_active,
                'priority' => $data['priority'] ?? $permission->priority,
            ]);

            $this->logAuditEvent('permission', 'update', $updatedBy, "Permission '{$permission->display_name}' updated", $permission, $oldData);

            DB::commit();

            return $permission;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a permission (new style).
     */
    public function deletePermissionNew(Permission $permission, User $deletedBy): bool
    {
        if ($permission->is_system) {
            throw new \Exception('System permissions cannot be deleted');
        }

        if ($permission->roles()->exists()) {
            throw new \Exception('Cannot delete permission that is assigned to roles');
        }

        DB::beginTransaction();

        try {
            $permissionName = $permission->display_name;
            
            $permission->delete();

            $this->logAuditEvent('permission', 'delete', $deletedBy, "Permission '{$permissionName}' deleted");

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all permissions grouped by module.
     */
    public function getPermissionsGroupedByModule(): Collection
    {
        return Permission::getGroupedByModule();
    }

    /**
     * Get all permissions.
     */
    public function getAllPermissions(): Collection
    {
        return Permission::with(['roles'])
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get active permissions.
     */
    public function getActivePermissions(): Collection
    {
        return Permission::active()
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get system permissions.
     */
    public function getSystemPermissions(): Collection
    {
        return Permission::system()
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get custom permissions.
     */
    public function getCustomPermissions(): Collection
    {
        return Permission::custom()
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get permissions by module.
     */
    public function getPermissionsByModule(string $module): Collection
    {
        return Permission::byModule($module)
            ->active()
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get permissions by action.
     */
    public function getPermissionsByAction(string $action): Collection
    {
        return Permission::byAction($action)
            ->active()
            ->orderBy('module')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get permissions by resource.
     */
    public function getPermissionsByResource(string $resource): Collection
    {
        return Permission::byResource($resource)
            ->active()
            ->orderBy('module')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get all available modules.
     */
    public function getAvailableModules(): Collection
    {
        return Permission::getModules();
    }

    /**
     * Get all available actions.
     */
    public function getAvailableActions(): Collection
    {
        return Permission::getActions();
    }

    /**
     * Get all available resources.
     */
    public function getAvailableResources(): Collection
    {
        return Permission::getResources();
    }

    /**
     * Check if user has permission.
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        return $user->hasAllPermissions($permissions);
    }

    /**
     * Get user permissions.
     */
    public function getUserPermissions(User $user): Collection
    {
        if (!$user->role) {
            return collect();
        }

        return $user->role->permissions()
            ->active()
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get role permissions.
     */
    public function getRolePermissions(Role $role): Collection
    {
        return $role->permissions()
            ->active()
            ->orderBy('module')
            ->orderBy('priority')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get roles with specific permission.
     */
    public function getRolesWithPermission(string $permission): Collection
    {
        return Role::whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission)
                ->where('is_active', true);
        })
        ->with(['permissions'])
        ->orderBy('display_name')
        ->get();
    }

    /**
     * Get permission statistics.
     */
    public function getPermissionStatistics(): array
    {
        $totalPermissions = Permission::count();
        $activePermissions = Permission::active()->count();
        $systemPermissions = Permission::system()->count();
        $customPermissions = Permission::custom()->count();
        $permissionsWithRoles = Permission::has('roles')->count();
        $modules = Permission::getModules()->count();

        return [
            'total' => $totalPermissions,
            'active' => $activePermissions,
            'system' => $systemPermissions,
            'custom' => $customPermissions,
            'with_roles' => $permissionsWithRoles,
            'modules' => $modules,
        ];
    }

    /**
     * Create system permissions.
     */
    public function createSystemPermissions(): void
    {
        $systemPermissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'users', 'action' => 'view', 'resource' => 'users', 'priority' => 1],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'users', 'action' => 'create', 'resource' => 'users', 'priority' => 2],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'users', 'action' => 'edit', 'resource' => 'users', 'priority' => 3],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'users', 'action' => 'delete', 'resource' => 'users', 'priority' => 4],
            ['name' => 'users.activate', 'display_name' => 'Activate Users', 'module' => 'users', 'action' => 'activate', 'resource' => 'users', 'priority' => 5],
            ['name' => 'users.deactivate', 'display_name' => 'Deactivate Users', 'module' => 'users', 'action' => 'deactivate', 'resource' => 'users', 'priority' => 6],

            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'module' => 'roles', 'action' => 'view', 'resource' => 'roles', 'priority' => 1],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'module' => 'roles', 'action' => 'create', 'resource' => 'roles', 'priority' => 2],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'module' => 'roles', 'action' => 'edit', 'resource' => 'roles', 'priority' => 3],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'module' => 'roles', 'action' => 'delete', 'resource' => 'roles', 'priority' => 4],
            ['name' => 'roles.assign', 'display_name' => 'Assign Roles', 'module' => 'roles', 'action' => 'assign', 'resource' => 'roles', 'priority' => 5],

            // Permission Management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'module' => 'permissions', 'action' => 'view', 'resource' => 'permissions', 'priority' => 1],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'module' => 'permissions', 'action' => 'create', 'resource' => 'permissions', 'priority' => 2],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'module' => 'permissions', 'action' => 'edit', 'resource' => 'permissions', 'priority' => 3],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'module' => 'permissions', 'action' => 'delete', 'resource' => 'permissions', 'priority' => 4],

            // Audit & Security
            ['name' => 'audit.view', 'display_name' => 'View Audit Logs', 'module' => 'audit', 'action' => 'view', 'resource' => 'audit_logs', 'priority' => 1],
            ['name' => 'security.view', 'display_name' => 'View Security Events', 'module' => 'security', 'action' => 'view', 'resource' => 'security_events', 'priority' => 1],
            ['name' => 'security.resolve', 'display_name' => 'Resolve Security Events', 'module' => 'security', 'action' => 'resolve', 'resource' => 'security_events', 'priority' => 2],

            // System Administration
            ['name' => 'system.settings', 'display_name' => 'Manage System Settings', 'module' => 'system', 'action' => 'settings', 'resource' => 'system', 'priority' => 1],
            ['name' => 'system.backup', 'display_name' => 'Manage Backups', 'module' => 'system', 'action' => 'backup', 'resource' => 'system', 'priority' => 2],
            ['name' => 'system.maintenance', 'display_name' => 'System Maintenance', 'module' => 'system', 'action' => 'maintenance', 'resource' => 'system', 'priority' => 3],
        ];

        foreach ($systemPermissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_system' => true, 'is_active' => true])
            );
        }
    }

    /**
     * Log audit event.
     */
    private function logAuditEvent(string $eventType, string $action, User $user, string $description, $resource = null, array $oldData = []): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'event' => $eventType,
            'action' => $action,
            'resource_type' => $resource ? get_class($resource) : null,
            'resource_id' => $resource ? $resource->id : null,
            'description' => $description,
            'old_values' => $oldData,
            'new_values' => $resource ? $resource->toArray() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Get audit logs with filtering.
     */
    public function getAuditLogs($filters = [])
    {
        $query = \App\Models\PermissionAuditLog::with(['user', 'permission'])
            ->orderBy('occurred_at', 'desc');

        // Apply filters
        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['event_type']) && $filters['event_type']) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['module']) && $filters['module']) {
            $query->where('permission_module', $filters['module']);
        }

        if (isset($filters['severity']) && $filters['severity']) {
            $query->where('severity', $filters['severity']);
        }

        if (isset($filters['requires_attention']) && $filters['requires_attention']) {
            $query->where('requires_attention', true);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('occurred_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['ip_address']) && $filters['ip_address']) {
            $query->where('ip_address', $filters['ip_address']);
        }

        return $query->paginate($filters['per_page'] ?? 50);
    }

    /**
     * Get role-permission matrix.
     */
    public function getRolePermissionMatrix($filters = [])
    {
        $rolesQuery = \App\Models\Role::with('permissions');
        $permissionsQuery = \App\Models\Permission::where('status', 'active');

        // Apply filters
        if (isset($filters['role_type']) && $filters['role_type']) {
            $rolesQuery->where('type', $filters['role_type']);
        }

        if (isset($filters['module']) && $filters['module']) {
            $permissionsQuery->where('module', $filters['module']);
        }

        if (isset($filters['permission_type']) && $filters['permission_type']) {
            $permissionsQuery->where('type', $filters['permission_type']);
        }

        $roles = $rolesQuery->get();
        $permissions = $permissionsQuery->get();

        // Build matrix
        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            
            $roleData = [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'type' => $role->type,
                'permissions' => []
            ];

            foreach ($permissions as $permission) {
                $roleData['permissions'][$permission->id] = [
                    'has_permission' => in_array($permission->id, $rolePermissions),
                    'is_inherited' => false, // Could implement inheritance logic here
                    'granted_at' => null, // Could get from pivot table
                    'granted_by' => null
                ];
            }

            $matrix[] = $roleData;
        }

        return [
            'roles' => $matrix,
            'permissions' => $permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name ?? $permission->name,
                    'module' => $permission->module,
                    'type' => $permission->type,
                    'description' => $permission->description
                ];
            })->toArray(),
            'metadata' => [
                'total_roles' => count($matrix),
                'total_permissions' => $permissions->count(),
                'generated_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Update role-permission matrix.
     */
    public function updateRolePermissionMatrix($changes)
    {
        $results = [];
        
        foreach ($changes as $change) {
            try {
                $role = \App\Models\Role::findOrFail($change['role_id']);
                $permission = \App\Models\Permission::findOrFail($change['permission_id']);
                
                if ($change['action'] === 'grant') {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                        
                        // Log audit trail
                        \App\Models\PermissionAuditLog::createEntry([
                            'event_type' => 'permission_granted',
                            'action' => 'role_permission_granted',
                            'resource_type' => 'role',
                            'resource_id' => $role->id,
                            'resource_name' => $role->name,
                            'subject_type' => 'role',
                            'subject_id' => $role->id,
                            'subject_name' => $role->name,
                            'permission_id' => $permission->id,
                            'permission_name' => $permission->name,
                            'permission_module' => $permission->module,
                            'description' => "Permission '{$permission->name}' granted to role '{$role->name}'",
                            'severity' => 'medium'
                        ]);
                        
                        $results[] = [
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                            'action' => 'granted',
                            'success' => true
                        ];
                    }
                } elseif ($change['action'] === 'revoke') {
                    if ($role->hasPermissionTo($permission)) {
                        $role->revokePermissionTo($permission);
                        
                        // Log audit trail
                        \App\Models\PermissionAuditLog::createEntry([
                            'event_type' => 'permission_revoked',
                            'action' => 'role_permission_revoked',
                            'resource_type' => 'role',
                            'resource_id' => $role->id,
                            'resource_name' => $role->name,
                            'subject_type' => 'role',
                            'subject_id' => $role->id,
                            'subject_name' => $role->name,
                            'permission_id' => $permission->id,
                            'permission_name' => $permission->name,
                            'permission_module' => $permission->module,
                            'description' => "Permission '{$permission->name}' revoked from role '{$role->name}'",
                            'severity' => 'medium'
                        ]);
                        
                        $results[] = [
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                            'action' => 'revoked',
                            'success' => true
                        ];
                    }
                }
            } catch (\Exception $e) {
                $results[] = [
                    'role_id' => $change['role_id'] ?? null,
                    'permission_id' => $change['permission_id'] ?? null,
                    'action' => $change['action'] ?? null,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Get permission templates.
     */
    public function getPermissionTemplates($filters = [])
    {
        $query = \App\Models\PermissionTemplate::with(['creator', 'updater']);

        // Apply filters
        if (isset($filters['category']) && $filters['category']) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['is_system']) && $filters['is_system'] !== null) {
            $query->where('is_system', $filters['is_system']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('usage_count', 'desc')
                    ->orderBy('display_name')
                    ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Create permission template.
     */
    public function createPermissionTemplate($data)
    {
        $template = \App\Models\PermissionTemplate::create($data);
        
        // Log audit trail
        \App\Models\PermissionAuditLog::createEntry([
            'event_type' => 'template_created',
            'action' => 'permission_template_created',
            'resource_type' => 'permission_template',
            'resource_id' => $template->id,
            'resource_name' => $template->name,
            'description' => "Permission template '{$template->display_name}' created",
            'metadata' => [
                'template_id' => $template->id,
                'permissions_count' => count($data['permissions']),
                'category' => $template->category
            ],
            'severity' => 'low'
        ]);
        
        return $template;
    }

    /**
     * Apply permission template.
     */
    public function applyPermissionTemplate($data)
    {
        $template = \App\Models\PermissionTemplate::findOrFail($data['template_id']);
        
        if ($data['target_type'] === 'role') {
            $target = \App\Models\Role::findOrFail($data['target_id']);
            return $template->applyToRole($target, $data['mode']);
        } elseif ($data['target_type'] === 'user') {
            $target = \App\Models\User::findOrFail($data['target_id']);
            return $template->applyToUser($target, $data['mode']);
        }
        
        throw new \Exception('Invalid target type');
    }

    /**
     * Export audit logs.
     */
    public function exportAuditLogs($filters = [])
    {
        $query = \App\Models\PermissionAuditLog::with(['user', 'permission']);

        // Apply same filters as getAuditLogs
        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['event_type']) && $filters['event_type']) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('occurred_at', [$filters['start_date'], $filters['end_date']]);
        }

        $logs = $query->orderBy('occurred_at', 'desc')->get();
        
        $exportData = $logs->map(function ($log) {
            return $log->toComplianceReport();
        });

        // Log the export action
        \App\Models\PermissionAuditLog::createEntry([
            'event_type' => 'data_exported',
            'action' => 'audit_logs_exported',
            'resource_type' => 'audit_log',
            'description' => 'Permission audit logs exported',
            'metadata' => [
                'exported_records' => $logs->count(),
                'filters' => $filters,
                'export_format' => 'json'
            ],
            'severity' => 'high' // High because it's data access
        ]);

        return [
            'export_id' => uniqid('export_'),
            'generated_at' => now()->toISOString(),
            'total_records' => $logs->count(),
            'filters_applied' => $filters,
            'data' => $exportData
        ];
    }

    /**
     * Generate compliance report.
     */
    public function generateComplianceReport($filters = [])
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30);
        $endDate = $filters['end_date'] ?? now();

        // Get audit log statistics
        $auditStats = \App\Models\PermissionAuditLog::whereBetween('occurred_at', [$startDate, $endDate])
            ->selectRaw('
                event_type,
                severity,
                COUNT(*) as count,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT ip_address) as unique_ips
            ')
            ->groupBy(['event_type', 'severity'])
            ->get();

        // Get high-risk activities
        $highRiskActivities = \App\Models\PermissionAuditLog::whereBetween('occurred_at', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('severity', 'high')
                      ->orWhere('severity', 'critical')
                      ->orWhere('requires_attention', true);
            })
            ->with(['user', 'permission'])
            ->orderBy('occurred_at', 'desc')
            ->limit(100)
            ->get();

        // Get user activity summary
        $userActivity = \App\Models\PermissionAuditLog::whereBetween('occurred_at', [$startDate, $endDate])
            ->selectRaw('
                user_id,
                user_name,
                user_email,
                COUNT(*) as total_actions,
                COUNT(DISTINCT DATE(occurred_at)) as active_days,
                MAX(occurred_at) as last_activity
            ')
            ->whereNotNull('user_id')
            ->groupBy(['user_id', 'user_name', 'user_email'])
            ->orderBy('total_actions', 'desc')
            ->limit(50)
            ->get();

        // Log report generation
        \App\Models\PermissionAuditLog::createEntry([
            'event_type' => 'report_generated',
            'action' => 'compliance_report_generated',
            'resource_type' => 'compliance_report',
            'description' => 'HIPAA compliance report generated',
            'metadata' => [
                'report_period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'filters' => $filters,
                'statistics_count' => $auditStats->count(),
                'high_risk_count' => $highRiskActivities->count()
            ],
            'severity' => 'medium'
        ]);

        return [
            'report_id' => uniqid('compliance_'),
            'generated_at' => now()->toISOString(),
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'statistics' => $auditStats,
            'high_risk_activities' => $highRiskActivities->map(function ($log) {
                return $log->toComplianceReport();
            }),
            'user_activity_summary' => $userActivity,
            'compliance_status' => [
                'total_events' => $auditStats->sum('count'),
                'unique_users' => $auditStats->sum('unique_users'),
                'high_risk_events' => $highRiskActivities->count(),
                'requires_attention' => $highRiskActivities->where('requires_attention', true)->count()
            ]
        ];
    }

    /**
     * Get all descendant roles.
     */
    public function getDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }
        
        return $descendants;
    }

    /**
     * Get all ancestor roles.
     */
    public function getAncestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        
        if ($this->parent) {
            $ancestors->push($this->parent);
            $ancestors = $ancestors->merge($this->parent->getAncestors());
        }
        
        return $ancestors;
    }


} 