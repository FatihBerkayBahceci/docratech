<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PermissionAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'action',
        'resource_type',
        'resource_id',
        'resource_name',
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'subject_id',
        'subject_type',
        'subject_name',
        'permission_id',
        'permission_name',
        'permission_module',
        'old_values',
        'new_values',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'request_id',
        'severity',
        'status',
        'requires_attention',
        'retention_until',
        'occurred_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'requires_attention' => 'boolean',
        'occurred_at' => 'datetime',
        'retention_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'occurred_at',
        'retention_until',
    ];

    // Prevent updates to maintain audit integrity
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->occurred_at) {
                $model->occurred_at = now();
            }
            
            // Set retention period (7 years for HIPAA compliance)
            if (!$model->retention_until) {
                $model->retention_until = now()->addYears(7);
            }
            
            // Auto-populate user information if authenticated
            if (auth()->check()) {
                $user = auth()->user();
                $model->user_id = $user->id;
                $model->user_name = $user->name;
                $model->user_email = $user->email;
                $model->user_role = $user->role?->name;
            }
            
            // Auto-populate request information
            if (request()) {
                $model->ip_address = request()->ip();
                $model->user_agent = request()->userAgent();
                if (request()->hasSession()) {
                    $model->session_id = request()->session()->getId();
                }
                $model->request_id = request()->header('X-Request-ID') ?? uniqid();
            }
        });
        
        // Prevent updates and deletes to maintain audit trail integrity
        static::updating(function ($model) {
            return false;
        });
        
        static::deleting(function ($model) {
            return false;
        });
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the permission that was affected.
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Get the subject of the action (polymorphic).
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Scope for recent audit logs.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for critical events.
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for events requiring attention.
     */
    public function scopeRequiresAttention($query)
    {
        return $query->where('requires_attention', true);
    }

    /**
     * Scope by event type.
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by IP address.
     */
    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('occurred_at', [$startDate, $endDate]);
    }

    /**
     * Scope by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('permission_module', $module);
    }

    /**
     * Get changes summary.
     */
    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        $oldValues = $this->old_values;
        $newValues = $this->new_values;

        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Get risk level based on event type and context.
     */
    public function getRiskLevelAttribute()
    {
        $highRiskEvents = [
            'permission_granted',
            'role_assigned',
            'admin_access_granted',
            'bulk_permission_change'
        ];

        $criticalEvents = [
            'super_admin_created',
            'security_permission_granted',
            'audit_log_accessed'
        ];

        if (in_array($this->action, $criticalEvents)) {
            return 'critical';
        }

        if (in_array($this->action, $highRiskEvents)) {
            return 'high';
        }

        return $this->severity;
    }

    /**
     * Format for export/reporting.
     */
    public function toComplianceReport()
    {
        return [
            'id' => $this->id,
            'timestamp' => $this->occurred_at->format('Y-m-d H:i:s T'),
            'event_type' => $this->event_type,
            'action' => $this->action,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user_name,
                'email' => $this->user_email,
                'role' => $this->user_role
            ],
            'subject' => [
                'type' => $this->subject_type,
                'id' => $this->subject_id,
                'name' => $this->subject_name
            ],
            'permission' => [
                'id' => $this->permission_id,
                'name' => $this->permission_name,
                'module' => $this->permission_module
            ],
            'changes' => $this->changes_summary,
            'request_info' => [
                'ip_address' => $this->ip_address,
                'user_agent' => $this->user_agent,
                'session_id' => $this->session_id
            ],
            'severity' => $this->severity,
            'risk_level' => $this->risk_level,
            'description' => $this->description
        ];
    }

    /**
     * Create audit log entry.
     */
    public static function createEntry(array $data)
    {
        // Automatically capture request information
        $request = request();
        
        $auditData = array_merge([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'request_id' => $request->header('X-Request-ID') ?: uniqid('req_'),
            'occurred_at' => now(),
        ], $data);

        // Auto-populate user information from auth
        if (auth()->check() && !isset($auditData['user_id'])) {
            $user = auth()->user();
            $auditData['user_id'] = $user->id;
            $auditData['user_name'] = $user->name;
            $auditData['user_email'] = $user->email;
            $auditData['user_role'] = $user->role?->name ?? 'unknown';
        }

        return static::create($auditData);
    }

    /**
     * Check if this log entry requires immediate attention.
     */
    public function requiresAttention(): bool
    {
        return $this->requires_attention || 
               in_array($this->severity, ['high', 'critical']) ||
               $this->event_type === 'permission_revoked';
    }

    /**
     * Mark as resolved.
     */
    public function resolve($resolvedBy = null): bool
    {
        return $this->update([
            'requires_attention' => false,
            'metadata' => array_merge($this->metadata ?? [], [
                'resolved_by' => $resolvedBy?->name ?? auth()->user()?->name,
                'resolved_at' => now()->toISOString(),
            ]),
        ]);
    }
} 