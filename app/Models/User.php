<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role_id',
        'status',
        'bio',
        'avatar',
        'last_login_at',
        'last_active_at',
        'preferences',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        // Enterprise fields
        'address', 'city', 'country', 'profile_photo',
        'education', 'specialties', 'certificates', 'work_experience',
        'languages', 'linkedin', 'twitter', 'facebook', 'instagram', 'website',
        'publications', 'awards', 'references', 'insurances', 'calendar_settings',
        'kvkk_approved', 'profile_public', 'admin_notes', 'documents',
        // Enhanced phone fields
        'phone_country', 'phone_e164', 'phone_type', 'phone_verified', 
        'phone_verified_at', 'phone_verification_code', 'phone_verification_expires_at', 
        'phone_metadata'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'email_verified' => 'boolean',
        'last_login_at' => 'datetime',
        'last_active_at' => 'datetime',
        'locked_until' => 'datetime',
        'preferences' => 'array',
        'two_factor_recovery_codes' => 'array',
        // Enterprise fields
        'education' => 'array',
        'specialties' => 'array',
        'certificates' => 'array',
        'work_experience' => 'array',
        'languages' => 'array',
        'publications' => 'array',
        'awards' => 'array',
        'references' => 'array',
        'insurances' => 'array',
        'calendar_settings' => 'array',
        'documents' => 'array',
        'kvkk_approved' => 'boolean',
        'profile_public' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * Get the user's full name.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }



    /**
     * Get the user's sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the user's audit logs.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the user's security events.
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission)
    {
        // Check direct permissions
        if ($this->hasPermissionTo($permission)) {
            return true;
        }

        // Check role permissions
        if ($this->role && $this->role->hasPermissionTo($permission)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole(['admin', 'super-admin']) || $this->hasPermission('*');
    }

    /**
     * Get user's active sessions.
     */
    public function getActiveSessions()
    {
        return $this->sessions()->where('status', 'active')->get();
    }

    /**
     * Terminate all user sessions except current.
     */
    public function terminateOtherSessions($currentTokenId = null)
    {
        $query = $this->sessions()->where('status', 'active');
        
        if ($currentTokenId) {
            $query->where('id', '!=', $currentTokenId);
        }
        
        $query->update(['status' => 'terminated']);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get user's activity summary.
     */
    public function getActivitySummary()
    {
        return [
            'total_logins' => $this->auditLogs()->where('event', 'login')->count(),
            'last_activity' => $this->auditLogs()->latest()->first()?->created_at,
            'active_sessions' => $this->getActiveSessions()->count(),
        ];
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for pending users.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for recent users.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if user is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Check if user has 2FA enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret);
    }

    /**
     * Get user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get user's role name.
     */
    public function getRoleNameAttribute(): ?string
    {
        return $this->role?->display_name;
    }
}
