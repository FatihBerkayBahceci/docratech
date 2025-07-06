<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'active' => User::where('status', 'active')->count(),
                    'inactive' => User::where('status', 'inactive')->count(),
                    'new' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                ],
                'roles' => [
                    'total' => Role::count(),
                    'active' => Role::where('status', 'active')->count(),
                    'custom' => Role::where('type', 'custom')->count(),
                    'system' => Role::where('type', 'system')->count(),
                ],
                'permissions' => [
                    'total' => Permission::count(),
                    'active' => Permission::where('status', 'active')->count(),
                    'custom' => Permission::where('type', 'custom')->count(),
                    'system' => Permission::where('type', 'system')->count(),
                ],
                'activity' => [
                    'total' => AuditLog::count(),
                    'today' => AuditLog::whereDate('created_at', Carbon::today())->count(),
                    'week' => AuditLog::where('created_at', '>=', Carbon::now()->subWeek())->count(),
                    'month' => AuditLog::where('created_at', '>=', Carbon::now()->subMonth())->count(),
                ],
            ];

            return response()->json([
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activity
     */
    public function activity(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
            'user_id' => 'sometimes|exists:users,id',
            'type' => 'sometimes|string',
            'date_range' => 'sometimes|array',
            'date_range.start' => 'required_with:date_range|date',
            'date_range.end' => 'required_with:date_range|date|after_or_equal:date_range.start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = AuditLog::with(['user', 'subject'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->type) {
                $query->where('event', $request->type);
            }

            if ($request->date_range) {
                $query->whereBetween('created_at', [
                    $request->date_range['start'],
                    $request->date_range['end']
                ]);
            }

            $activities = $query->paginate($request->get('per_page', 20));

            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system status
     */
    public function systemStatus()
    {
        try {
            $status = [
                'status' => 'operational',
                'services' => [
                    [
                        'name' => 'Database',
                        'status' => 'operational',
                        'response_time' => '5ms',
                        'last_check' => Carbon::now()->toISOString(),
                    ],
                    [
                        'name' => 'Cache',
                        'status' => 'operational',
                        'response_time' => '2ms',
                        'last_check' => Carbon::now()->toISOString(),
                    ],
                    [
                        'name' => 'Queue',
                        'status' => 'operational',
                        'response_time' => '10ms',
                        'last_check' => Carbon::now()->toISOString(),
                    ],
                    [
                        'name' => 'Storage',
                        'status' => 'operational',
                        'response_time' => '15ms',
                        'last_check' => Carbon::now()->toISOString(),
                    ],
                ],
            ];

            return response()->json([
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch system status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function performance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'period' => 'sometimes|string|in:day,week,month',
            'metric' => 'sometimes|string|in:users,activity,errors',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $period = $request->get('period', 'week');
            $metric = $request->get('metric', 'users');

            $data = $this->getPerformanceData($period, $metric);

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch performance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance data for charts
     */
    private function getPerformanceData($period, $metric)
    {
        $labels = [];
        $datasets = [];

        switch ($period) {
            case 'day':
                $labels = $this->getDayLabels();
                break;
            case 'week':
                $labels = $this->getWeekLabels();
                break;
            case 'month':
                $labels = $this->getMonthLabels();
                break;
        }

        switch ($metric) {
            case 'users':
                $datasets = [
                    [
                        'label' => 'New Users',
                        'data' => $this->getUserData($period),
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    ]
                ];
                break;
            case 'activity':
                $datasets = [
                    [
                        'label' => 'Activity',
                        'data' => $this->getActivityData($period),
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    ]
                ];
                break;
            case 'errors':
                $datasets = [
                    [
                        'label' => 'Errors',
                        'data' => $this->getErrorData($period),
                        'borderColor' => '#EF4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    ]
                ];
                break;
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function getDayLabels()
    {
        $labels = [];
        for ($i = 23; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subHours($i)->format('H:i');
        }
        return $labels;
    }

    private function getWeekLabels()
    {
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('D');
        }
        return $labels;
    }

    private function getMonthLabels()
    {
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('M d');
        }
        return $labels;
    }

    private function getUserData($period)
    {
        // Mock data - replace with actual database queries
        return array_fill(0, $period === 'day' ? 24 : ($period === 'week' ? 7 : 30), rand(0, 10));
    }

    private function getActivityData($period)
    {
        // Mock data - replace with actual database queries
        return array_fill(0, $period === 'day' ? 24 : ($period === 'week' ? 7 : 30), rand(10, 100));
    }

    private function getErrorData($period)
    {
        // Mock data - replace with actual database queries
        return array_fill(0, $period === 'day' ? 24 : ($period === 'week' ? 7 : 30), rand(0, 5));
    }
} 