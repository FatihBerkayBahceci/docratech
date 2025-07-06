<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
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
     * Default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'guard_name' => 'web',
        'status' => 'active',
        'type' => 'custom',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'permissions_count',
        'users_count',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', User::class);
    }

    /**
     * Get the role's audit logs.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'subject');
    }

    /**
     * Check if role is system role.
     */
    public function isSystem()
    {
        return $this->type === 'system';
    }

    /**
     * Check if role is custom role.
     */
    public function isCustom()
    {
        return $this->type === 'custom';
    }

    /**
     * Check if role is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get role's user count.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get role's permission count.
     */
    public function getPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }

    /**
     * Check if role can be deleted.
     */
    public function canBeDeleted()
    {
        return !$this->isSystem() && $this->users()->count() === 0;
    }

    /**
     * Check if role can be modified.
     */
    public function canBeModified()
    {
        return !$this->isSystem() || $this->name !== 'super-admin';
    }

    /**
     * Duplicate role.
     */
    public function duplicate()
    {
        $newRole = $this->replicate();
        $newRole->name = $this->name . ' (Copy)';
        $newRole->type = 'custom';
        $newRole->status = 'active';
        $newRole->save();

        // Copy permissions
        $newRole->permissions()->attach($this->permissions->pluck('id'));

        return $newRole;
    }

    /**
     * Get role statistics.
     */
    public function getStats()
    {
        return [
            'user_count' => $this->users()->count(),
            'permission_count' => $this->permissions()->count(),
            'is_system' => $this->isSystem(),
            'is_active' => $this->isActive(),
            'can_be_deleted' => $this->canBeDeleted(),
            'can_be_modified' => $this->canBeModified(),
        ];
    }

    /**
     * Scope for system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('type', 'system');
    }

    /**
     * Scope for custom roles.
     */
    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    /**
     * Scope for active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive roles.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for roles with users.
     */
    public function scopeWithUsers($query)
    {
        return $query->has('users');
    }

    /**
     * Scope for roles without users.
     */
    public function scopeWithoutUsers($query)
    {
        return $query->doesntHave('users');
    }

    /**
     * Get the parent role.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    /**
     * Get the child roles.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    /**
     * Grant permission to role.
     */
    public function grantPermission(Permission $permission, ?User $grantedBy = null): void
    {
        $this->givePermissionTo($permission);
        $this->clearPermissionsCache();
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermission(Permission $permission): void
    {
        $this->revokePermissionTo($permission);
        $this->clearPermissionsCache();
    }

    /**
     * Clear permissions cache.
     */
    public function clearPermissionsCache(): void
    {
        $this->update(['permissions_cache' => null]);
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

    /**
     * Check if role is descendant of given role.
     */
    public function isDescendantOf(Role $role): bool
    {
        return $this->getAncestors()->contains('id', $role->id);
    }

    /**
     * Check if role is ancestor of given role.
     */
    public function isAncestorOf(Role $role): bool
    {
        return $this->getDescendants()->contains('id', $role->id);
    }
}
