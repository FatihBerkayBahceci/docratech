<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Get general settings
     */
    public function general(): JsonResponse
    {
        return response()->json([
            'data' => [
                'app_name' => 'DocraTech Medical Platform',
                'app_version' => '1.0.0',
                'timezone' => 'UTC',
                'language' => 'en',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
            ]
        ]);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'General settings updated successfully',
            'data' => $request->all()
        ]);
    }

    /**
     * Get security settings
     */
    public function security(): JsonResponse
    {
        return response()->json([
            'data' => [
                'password_min_length' => 8,
                'password_require_uppercase' => true,
                'password_require_numbers' => true,
                'password_require_symbols' => false,
                'session_lifetime' => 120,
                'two_factor_enabled' => false,
            ]
        ]);
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Security settings updated successfully',
            'data' => $request->all()
        ]);
    }

    /**
     * Get notification settings
     */
    public function notifications(): JsonResponse
    {
        return response()->json([
            'data' => [
                'email_notifications' => true,
                'browser_notifications' => true,
                'marketing_emails' => false,
                'security_alerts' => true,
            ]
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Notification settings updated successfully',
            'data' => $request->all()
        ]);
    }
} 