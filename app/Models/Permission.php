<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        'module',
        'action',
        'resource',
        'type',
        'status',
        'guard_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withPivot(['is_granted', 'conditions', 'granted_at', 'granted_by'])
            ->withTimestamps();
    }

    /**
     * Get the full permission name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->module ? "{$this->module}.{$this->name}" : $this->name;
    }

    /**
     * Get the permission's audit logs.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'subject');
    }

    /**
     * Check if permission is system permission.
     */
    public function isSystem()
    {
        return $this->type === 'system';
    }

    /**
     * Check if permission is custom permission.
     */
    public function isCustom()
    {
        return $this->type === 'custom';
    }

    /**
     * Check if permission is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get permission's role count.
     */
    public function getRoleCountAttribute()
    {
        return $this->roles()->count();
    }

    /**
     * Get permission's user count.
     */
    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Check if permission can be deleted.
     */
    public function canBeDeleted()
    {
        return !$this->isSystem() && $this->roles()->count() === 0;
    }

    /**
     * Check if permission can be modified.
     */
    public function canBeModified()
    {
        return !$this->isSystem();
    }

    /**
     * Get permission statistics.
     */
    public function getStats()
    {
        return [
            'role_count' => $this->roles()->count(),
            'user_count' => $this->users()->count(),
            'is_system' => $this->isSystem(),
            'is_active' => $this->isActive(),
            'can_be_deleted' => $this->canBeDeleted(),
            'can_be_modified' => $this->canBeModified(),
        ];
    }

    /**
     * Scope for system permissions.
     */
    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }

    /**
     * Scope for custom permissions.
     */
    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    /**
     * Scope for active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive permissions.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for permissions by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for permissions with roles.
     */
    public function scopeWithRoles($query)
    {
        return $query->has('roles');
    }

    /**
     * Scope for permissions without roles.
     */
    public function scopeWithoutRoles($query)
    {
        return $query->doesntHave('roles');
    }

    /**
     * Get all available modules.
     */
    public static function getModules()
    {
        return static::select('module')
            ->distinct()
            ->where('status', 'active')
            ->orderBy('module')
            ->pluck('module');
    }

    /**
     * Get permissions grouped by module.
     */
    public static function getGroupedByModule()
    {
        return static::with('roles')
            ->where('status', 'active')
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');
    }

    /**
     * Generate permission key.
     */
    public static function generateKey($name, $module)
    {
        $baseKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $name));
        $key = "{$module}.{$baseKey}";
        $counter = 1;
        
        while (static::where('key', $key)->exists()) {
            $key = "{$module}.{$baseKey}.{$counter}";
            $counter++;
        }
        
        return $key;
    }

    /**
     * Validate permission key.
     */
    public static function validateKey($key, $excludeId = null)
    {
        $query = static::where('key', $key);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Get all available actions.
     */
    public static function getActions()
    {
        return static::select('action')
            ->distinct()
            ->where('status', 'active')
            ->whereNotNull('action')
            ->orderBy('action')
            ->pluck('action');
    }

    /**
     * Get all available resources.
     */
    public static function getResources()
    {
        return static::select('resource')
            ->distinct()
            ->where('status', 'active')
            ->whereNotNull('resource')
            ->orderBy('resource')
            ->pluck('resource');
    }

    /**
     * Scope for permissions by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for permissions by resource.
     */
    public function scopeByResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }
}
