<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities
     */
    public function index(Request $request): JsonResponse
    {
        $activities = AuditLog::with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('event', 'like', "%{$search}%");
            })
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->event, function ($query, $event) {
                $query->where('event', $event);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]
        ]);
    }

    /**
     * Export activities
     */
    public function export(Request $request)
    {
        // Implementation for exporting activities
        $activities = AuditLog::with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('event', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Return JSON for now, can be enhanced with Excel export
        return response()->json([
            'message' => 'Export functionality not implemented yet',
            'data' => $activities
        ]);
    }
} 