<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RoleService
{
    /**
     * Get roles with filters
     */
    public function getRoles($filters = [])
    {
        $query = Role::with(['permissions', 'users'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // CRITICAL FIX: Return all roles for frontend dropdown, not paginated
        if (isset($filters['per_page']) && $filters['per_page'] >= 100) {
            return $query->get();
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Create new role
     */
    public function createRole($data)
    {
        DB::beginTransaction();

        try {
            $role = Role::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'] ?? $data['name'], // Use name as fallback
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'status' => $data['status'],
                'guard_name' => 'web', // Add required guard_name
            ]);

            // Assign permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->permissions()->attach($data['permissions']);
            }

            // Log activity (legacy AuditLog)
            $this->logActivity($role, 'create', 'Role created successfully');

            // Enterprise audit trail (PermissionAuditLog)
            $resourceName = $role->display_name ?: $role->name;

            \App\Models\PermissionAuditLog::createEntry([
                'event_type' => 'role_created',
                'action' => 'role_created',
                'resource_type' => 'role',
                'resource_id' => $role->id,
                'resource_name' => $resourceName,
                'description' => "Role '{$resourceName}' created",
                'severity' => 'low'
            ]);

            DB::commit();

            return $role->load(['permissions', 'users']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get role by ID
     */
    public function getRole($id)
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        
        // Add additional data
        $role->load(['auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return $role;
    }

    /**
     * Update role
     */
    public function updateRole($id, $data)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);

            // Check if system role can be modified
            if ($role->type === 'system' && isset($data['type']) && $data['type'] !== 'system') {
                throw new \Exception('System roles cannot be changed to custom type');
            }

            $role->update([
                'name' => $data['name'] ?? $role->name,
                'description' => $data['description'] ?? $role->description,
                'type' => $data['type'] ?? $role->type,
                'status' => $data['status'] ?? $role->status,
            ]);

            // Log activity
            $this->logActivity($role, 'update', 'Role updated successfully');

            DB::commit();

            return $role->load(['permissions', 'users']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete role
     */
    public function deleteRole($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);

            // Check if role can be deleted
            if ($role->type === 'system') {
                throw new \Exception('System roles cannot be deleted');
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                throw new \Exception('Cannot delete role with assigned users');
            }

            // Log activity before deletion
            $this->logActivity($role, 'delete', 'Role deleted successfully');

            // Delete role
            $role->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        return [
            'role' => $role,
            'permissions' => $role->permissions,
            'available_permissions' => Permission::where('status', 'active')->get(),
        ];
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions($id, $permissions)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);

            // Sync permissions
            $role->permissions()->sync($permissions);

            // Log activity
            $this->logActivity($role, 'permissions_assigned', 'Permissions assigned to role');

            DB::commit();

            return [
                'role' => $role->load('permissions'),
                'message' => 'Permissions assigned successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Duplicate role
     */
    public function duplicateRole($id)
    {
        DB::beginTransaction();

        try {
            $originalRole = Role::with('permissions')->findOrFail($id);
            
            $newRole = Role::create([
                'name' => $originalRole->name . ' (Copy)',
                'display_name' => $originalRole->display_name . ' (Copy)',
                'description' => $originalRole->description,
                'type' => 'custom', // Always custom when duplicated
                'status' => 'active',
                'guard_name' => 'web',
            ]);

            // Copy permissions
            $newRole->permissions()->attach($originalRole->permissions->pluck('id'));

            // Log activity
            $this->logActivity($newRole, 'duplicate', 'Role duplicated from ' . $originalRole->name);

            DB::commit();

            return $newRole->load(['permissions', 'users']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get role statistics
     */
    public function getRoleStats()
    {
        return [
            'total' => Role::count(),
            'active' => Role::where('status', 'active')->count(),
            'custom' => Role::where('type', 'custom')->count(),
            'system' => Role::where('type', 'system')->count(),
            'with_users' => Role::has('users')->count(),
            'without_users' => Role::doesntHave('users')->count(),
        ];
    }

    /**
     * Log activity
     */
    private function logActivity($role, $event, $description)
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'subject_type' => Role::class,
                'subject_id' => $role->id,
                'event' => $event,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'url' => request()->url(),
                    'method' => request()->method(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log audit failure silently - don't break the main operation
            \Log::warning('Failed to log audit activity: ' . $e->getMessage());
        }
    }

    /**
     * Get role hierarchy tree.
     */
    public function getRoleHierarchy(): Collection
    {
        return Role::with(['permissions'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all roles with their permissions.
     */
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with(['permissions'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active roles.
     */
    public function getActiveRoles(): Collection
    {
        return Role::where('status', 'active')
            ->with(['permissions'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get system roles.
     */
    public function getSystemRoles(): Collection
    {
        return Role::where('type', 'system')
            ->with(['permissions'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get custom roles.
     */
    public function getCustomRoles(): Collection
    {
        return Role::where('type', 'custom')
            ->with(['permissions'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get users with specific role.
     */
    public function getUsersWithRole(Role $role): Collection
    {
        return $role->users()
            ->with(['role'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get role statistics.
     */
    public function getRoleStatistics(): array
    {
        $totalRoles = Role::count();
        $activeRoles = Role::where('status', 'active')->count();
        $systemRoles = Role::where('type', 'system')->count();
        $customRoles = Role::where('type', 'custom')->count();
        $rolesWithUsers = Role::has('users')->count();

        return [
            'total' => $totalRoles,
            'active' => $activeRoles,
            'system' => $systemRoles,
            'custom' => $customRoles,
            'with_users' => $rolesWithUsers,
        ];
    }
} 