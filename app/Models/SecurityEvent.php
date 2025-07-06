<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'location',
        'description',
        'details',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user associated with the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who resolved the event.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Check if the event is resolved.
     */
    public function isResolved(): bool
    {
        return $this->is_resolved;
    }

    /**
     * Check if the event is high severity.
     */
    public function isHighSeverity(): bool
    {
        return $this->severity === 'high';
    }

    /**
     * Check if the event is critical severity.
     */
    public function isCriticalSeverity(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Resolve the security event.
     */
    public function resolve(User $resolvedBy, string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy->id,
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Get the time elapsed since the event occurred.
     */
    public function getTimeElapsed(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the time elapsed since the event was resolved.
     */
    public function getResolutionTimeElapsed(): ?string
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->resolved_at->diffForHumans();
    }

    /**
     * Scope to get only unresolved events.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope to get only resolved events.
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope to get events by severity.
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to get events by type.
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope to get events by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get events by IP address.
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to get events within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent events.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get critical events.
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope to get high severity events.
     */
    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    /**
     * Scope to get medium severity events.
     */
    public function scopeMedium($query)
    {
        return $query->where('severity', 'medium');
    }

    /**
     * Scope to get low severity events.
     */
    public function scopeLow($query)
    {
        return $query->where('severity', 'low');
    }

    /**
     * Get events grouped by severity.
     */
    public static function getGroupedBySeverity(): \Illuminate\Support\Collection
    {
        return static::orderBy('created_at', 'desc')
            ->get()
            ->groupBy('severity');
    }

    /**
     * Get events grouped by type.
     */
    public static function getGroupedByType(): \Illuminate\Support\Collection
    {
        return static::orderBy('created_at', 'desc')
            ->get()
            ->groupBy('event_type');
    }

    /**
     * Get events grouped by user.
     */
    public static function getGroupedByUser(): \Illuminate\Support\Collection
    {
        return static::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');
    }
}
