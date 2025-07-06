<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'permissions',
        'metadata',
        'is_system',
        'is_active',
        'usage_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'metadata' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
        
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this template.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the permissions included in this template.
     */
    public function getPermissionsAttribute()
    {
        $permissionIds = $this->attributes['permissions'] ?? [];
        if (is_string($permissionIds)) {
            $permissionIds = json_decode($permissionIds, true);
        }
        
        return Permission::whereIn('id', $permissionIds)->get();
    }

    /**
     * Get permission IDs array.
     */
    public function getPermissionIdsAttribute()
    {
        $permissionIds = $this->attributes['permissions'] ?? [];
        if (is_string($permissionIds)) {
            $permissionIds = json_decode($permissionIds, true);
        }
        
        return $permissionIds;
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for system templates.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope for custom templates.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for popular templates.
     */
    public function scopePopular($query, $minUsage = 1)
    {
        return $query->where('usage_count', '>=', $minUsage)->orderBy('usage_count', 'desc');
    }

    /**
     * Apply template to a role.
     */
    public function applyToRole(Role $role, $mode = 'replace')
    {
        $permissionIds = $this->permission_ids;
        
        if ($mode === 'replace') {
            $role->permissions()->sync($permissionIds);
        } elseif ($mode === 'add') {
            $role->permissions()->attach($permissionIds);
        } elseif ($mode === 'remove') {
            $role->permissions()->detach($permissionIds);
        }
        
        $this->increment('usage_count');
        
        // Log audit trail
        PermissionAuditLog::createEntry([
            'event_type' => 'template_applied',
            'action' => 'apply_template_to_role',
            'resource_type' => 'role',
            'resource_id' => $role->id,
            'resource_name' => $role->name,
            'subject_type' => 'role',
            'subject_id' => $role->id,
            'subject_name' => $role->name,
            'description' => "Applied template '{$this->display_name}' to role '{$role->name}' in {$mode} mode",
            'metadata' => [
                'template_id' => $this->id,
                'template_name' => $this->name,
                'mode' => $mode,
                'permissions_count' => count($permissionIds),
                'permissions' => $permissionIds
            ],
            'severity' => 'medium'
        ]);
        
        return $role;
    }

    /**
     * Apply template to a user.
     */
    public function applyToUser(User $user, $mode = 'replace')
    {
        $permissionIds = $this->permission_ids;
        
        if ($mode === 'replace') {
            $user->permissions()->sync($permissionIds);
        } elseif ($mode === 'add') {
            $user->permissions()->attach($permissionIds);
        } elseif ($mode === 'remove') {
            $user->permissions()->detach($permissionIds);
        }
        
        $this->increment('usage_count');
        
        // Log audit trail
        PermissionAuditLog::createEntry([
            'event_type' => 'template_applied',
            'action' => 'apply_template_to_user',
            'resource_type' => 'user',
            'resource_id' => $user->id,
            'resource_name' => $user->name,
            'subject_type' => 'user',
            'subject_id' => $user->id,
            'subject_name' => $user->name,
            'description' => "Applied template '{$this->display_name}' to user '{$user->name}' in {$mode} mode",
            'metadata' => [
                'template_id' => $this->id,
                'template_name' => $this->name,
                'mode' => $mode,
                'permissions_count' => count($permissionIds),
                'permissions' => $permissionIds
            ],
            'severity' => 'medium'
        ]);
        
        return $user;
    }

    /**
     * Get template statistics.
     */
    public function getStats()
    {
        return [
            'permissions_count' => count($this->permission_ids),
            'usage_count' => $this->usage_count,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Create a copy of this template.
     */
    public function duplicate($newName = null)
    {
        $newName = $newName ?: $this->name . ' (Copy)';
        
        return static::create([
            'name' => $newName,
            'display_name' => $this->display_name . ' (Copy)',
            'description' => $this->description,
            'category' => 'custom',
            'permissions' => $this->permission_ids,
            'metadata' => $this->metadata,
            'is_system' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Check if template can be deleted.
     */
    public function canBeDeleted()
    {
        return !$this->is_system;
    }

    /**
     * Check if template can be modified.
     */
    public function canBeModified()
    {
        return !$this->is_system;
    }

    /**
     * Get system templates.
     */
    public static function getSystemTemplates()
    {
        return [
            [
                'name' => 'admin_full_access',
                'display_name' => 'Administrator Full Access',
                'description' => 'Complete administrative access to all system functions',
                'category' => 'system',
                'permissions' => ['*'],
                'metadata' => ['danger_level' => 'high'],
                'is_system' => true
            ],
            [
                'name' => 'doctor_basic',
                'display_name' => 'Doctor Basic',
                'description' => 'Basic permissions for medical doctors',
                'category' => 'medical',
                'permissions' => [
                    'patients.view',
                    'patients.create',
                    'patients.edit',
                    'appointments.view',
                    'appointments.create',
                    'appointments.edit',
                    'medical_records.view',
                    'medical_records.create',
                    'medical_records.edit',
                    'prescriptions.view',
                    'prescriptions.create',
                    'prescriptions.edit'
                ],
                'metadata' => ['role_type' => 'medical'],
                'is_system' => true
            ],
            [
                'name' => 'nurse_basic',
                'display_name' => 'Nurse Basic',
                'description' => 'Basic permissions for nurses',
                'category' => 'medical',
                'permissions' => [
                    'patients.view',
                    'appointments.view',
                    'appointments.create',
                    'appointments.edit',
                    'medical_records.view',
                    'vitals.view',
                    'vitals.create',
                    'vitals.edit'
                ],
                'metadata' => ['role_type' => 'medical'],
                'is_system' => true
            ],
            [
                'name' => 'receptionist_basic',
                'display_name' => 'Receptionist Basic',
                'description' => 'Basic permissions for receptionists',
                'category' => 'administrative',
                'permissions' => [
                    'patients.view',
                    'patients.create',
                    'patients.edit',
                    'appointments.view',
                    'appointments.create',
                    'appointments.edit',
                    'appointments.cancel',
                    'billing.view',
                    'reports.basic'
                ],
                'metadata' => ['role_type' => 'administrative'],
                'is_system' => true
            ],
            [
                'name' => 'viewer_readonly',
                'display_name' => 'View Only Access',
                'description' => 'Read-only access to basic information',
                'category' => 'system',
                'permissions' => [
                    'dashboard.view',
                    'patients.view',
                    'appointments.view',
                    'reports.basic'
                ],
                'metadata' => ['role_type' => 'readonly'],
                'is_system' => true
            ]
        ];
    }
} 