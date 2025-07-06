<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuditController extends Controller
{
    /**
     * Get audit logs.
     */
    public function logs(Request $request): JsonResponse
    {
        $query = AuditLog::with(['user']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by resource type
        if ($request->has('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('occurred_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        // Filter by IP address
        if ($request->has('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        $logs = $query->orderBy('occurred_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ],
            ],
        ]);
    }

    /**
     * Get specific audit log.
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        $auditLog->load(['user']);

        return response()->json([
            'success' => true,
            'data' => [
                'log' => $auditLog,
            ],
        ]);
    }

    /**
     * Get audit logs grouped by event type.
     */
    public function groupedByEventType(): JsonResponse
    {
        $logs = AuditLog::getGroupedByEventType();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
            ],
        ]);
    }

    /**
     * Get audit logs grouped by action.
     */
    public function groupedByAction(): JsonResponse
    {
        $logs = AuditLog::getGroupedByAction();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
            ],
        ]);
    }

    /**
     * Get audit logs grouped by resource type.
     */
    public function groupedByResourceType(): JsonResponse
    {
        $logs = AuditLog::getGroupedByResourceType();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
            ],
        ]);
    }

    /**
     * Get recent audit logs.
     */
    public function recent(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);
        $logs = AuditLog::recent($days)
            ->with(['user'])
            ->orderBy('occurred_at', 'desc')
            ->limit($request->get('limit', 50))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs,
                'period' => $days . ' days',
            ],
        ]);
    }

    /**
     * Get security events.
     */
    public function securityEvents(Request $request): JsonResponse
    {
        $query = SecurityEvent::with(['user', 'resolvedBy']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by resolved status
        if ($request->has('is_resolved')) {
            $query->where('is_resolved', $request->boolean('is_resolved'));
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        // Filter by IP address
        if ($request->has('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        $events = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ],
            ],
        ]);
    }

    /**
     * Get specific security event.
     */
    public function showSecurityEvent(SecurityEvent $securityEvent): JsonResponse
    {
        $securityEvent->load(['user', 'resolvedBy']);

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $securityEvent,
            ],
        ]);
    }

    /**
     * Resolve security event.
     */
    public function resolveSecurityEvent(Request $request, SecurityEvent $securityEvent): JsonResponse
    {
        $validator = $request->validate([
            'resolution_notes' => 'nullable|string',
        ]);

        try {
            $securityEvent->resolve($request->user(), $validator['resolution_notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Security event resolved successfully',
                'data' => [
                    'event' => $securityEvent->load(['user', 'resolvedBy']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve security event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get security events grouped by severity.
     */
    public function securityEventsGroupedBySeverity(): JsonResponse
    {
        $events = SecurityEvent::getGroupedBySeverity();

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events,
            ],
        ]);
    }

    /**
     * Get security events grouped by type.
     */
    public function securityEventsGroupedByType(): JsonResponse
    {
        $events = SecurityEvent::getGroupedByType();

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events,
            ],
        ]);
    }

    /**
     * Get security events grouped by user.
     */
    public function securityEventsGroupedByUser(): JsonResponse
    {
        $events = SecurityEvent::getGroupedByUser();

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events,
            ],
        ]);
    }

    /**
     * Get recent security events.
     */
    public function recentSecurityEvents(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);
        $events = SecurityEvent::recent($days)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($request->get('limit', 50))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events,
                'period' => $days . ' days',
            ],
        ]);
    }

    /**
     * Get critical security events.
     */
    public function criticalSecurityEvents(Request $request): JsonResponse
    {
        $events = SecurityEvent::critical()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ],
            ],
        ]);
    }

    /**
     * Get unresolved security events.
     */
    public function unresolvedSecurityEvents(Request $request): JsonResponse
    {
        $events = SecurityEvent::unresolved()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'events' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ],
            ],
        ]);
    }

    /**
     * Get audit statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalLogs = AuditLog::count();
        $successfulLogs = AuditLog::where('status', 'success')->count();
        $failedLogs = AuditLog::where('status', 'failed')->count();
        $totalSecurityEvents = SecurityEvent::count();
        $unresolvedSecurityEvents = SecurityEvent::unresolved()->count();
        $criticalSecurityEvents = SecurityEvent::critical()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_logs' => $totalLogs,
                    'successful_logs' => $successfulLogs,
                    'failed_logs' => $failedLogs,
                    'total_security_events' => $totalSecurityEvents,
                    'unresolved_security_events' => $unresolvedSecurityEvents,
                    'critical_security_events' => $criticalSecurityEvents,
                ],
            ],
        ]);
    }
}
