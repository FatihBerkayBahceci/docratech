<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PhoneController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/users/{user}/profile', [UserController::class, 'publicProfile']);

// Phone validation (public - no auth required for basic validation)
Route::post('/phone/validate', [PhoneController::class, 'validatePhoneNumber']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/sessions', [AuthController::class, 'sessions']);
    Route::delete('/auth/sessions/{sessionId}', [AuthController::class, 'revokeSession']);
    Route::delete('/auth/sessions', [AuthController::class, 'revokeAllSessions']);
    Route::post('/auth/2fa/enable', [AuthController::class, 'enableTwoFactor']);
    Route::post('/auth/2fa/disable', [AuthController::class, 'disableTwoFactor']);
    Route::post('/auth/2fa/verify', [AuthController::class, 'verifyTwoFactor']);
    Route::post('/auth/2fa/recovery', [AuthController::class, 'verifyRecoveryCode']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/activity', [DashboardController::class, 'activity']);
        Route::get('/system-status', [DashboardController::class, 'systemStatus']);
        Route::get('/performance', [DashboardController::class, 'performance']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/bulk-update', [UserController::class, 'bulkUpdate']);
        Route::get('/export', [UserController::class, 'export']);
        Route::post('/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::delete('/{user}/remove-role', [UserController::class, 'removeRole']);
        Route::post('/{user}/activate', [UserController::class, 'activate']);
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate']);
        Route::get('/{user}/sessions', [UserController::class, 'sessions']);
        Route::get('/{user}/audit-logs', [UserController::class, 'auditLogs']);
        Route::get('/{user}/security-events', [UserController::class, 'securityEvents']);
        Route::get('/statistics', [UserController::class, 'statistics']);
        
        // Quick Actions
        Route::post('/{id}/send-email', [UserController::class, 'sendEmail']);
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::get('/{id}/login-history', [UserController::class, 'loginHistory']);
    });

    // Role routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::get('/{id}/permissions', [RoleController::class, 'permissions']);
        Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions']);
        Route::get('/{id}/users', [RoleController::class, 'users']);
        Route::get('/hierarchy', [RoleController::class, 'hierarchy']);
        Route::get('/active', [RoleController::class, 'active']);
        Route::get('/system', [RoleController::class, 'system']);
        Route::get('/custom', [RoleController::class, 'custom']);
        Route::get('/statistics', [RoleController::class, 'statistics']);
    });

    // Permission routes
    Route::prefix('permissions')->group(function () {
        // List and create routes
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        
        // Enterprise Features - Specific named routes BEFORE wildcard routes
        Route::get('/modules', [PermissionController::class, 'modules']);
        Route::get('/statistics', [PermissionController::class, 'statistics']);
        Route::get('/grouped-by-module', [PermissionController::class, 'groupedByModule']);
        Route::get('/active', [PermissionController::class, 'active']);
        Route::get('/system', [PermissionController::class, 'system']);
        Route::get('/custom', [PermissionController::class, 'custom']);
        Route::get('/actions', [PermissionController::class, 'actions']);
        Route::get('/resources', [PermissionController::class, 'resources']);
        Route::get('/user-permissions', [PermissionController::class, 'userPermissions']);
        
        // Audit Trail
        Route::get('/audit-logs', [PermissionController::class, 'auditLogs']);
        Route::get('/audit-logs/export', [PermissionController::class, 'exportAuditLogs']);
        Route::get('/compliance-report', [PermissionController::class, 'complianceReport']);
        
        // Role Matrix
        Route::get('/role-matrix', [PermissionController::class, 'roleMatrix']);
        Route::post('/role-matrix/update', [PermissionController::class, 'updateRoleMatrix']);
        
        // Permission Templates
        Route::get('/templates', [PermissionController::class, 'templates']);
        Route::post('/templates', [PermissionController::class, 'createTemplate']);
        Route::post('/templates/apply', [PermissionController::class, 'applyTemplate']);
        
        // User permission checks
        Route::post('/check-user-permission', [PermissionController::class, 'checkUserPermission']);
        Route::post('/check-user-any-permission', [PermissionController::class, 'checkUserAnyPermission']);
        Route::post('/check-user-all-permissions', [PermissionController::class, 'checkUserAllPermissions']);
        
        // Parametrized routes
        Route::get('/by-module/{module}', [PermissionController::class, 'byModule']);
        Route::get('/by-action/{action}', [PermissionController::class, 'byAction']);
        Route::get('/by-resource/{resource}', [PermissionController::class, 'byResource']);
        Route::get('/{permission}/roles', [PermissionController::class, 'roles']);
        
        // Wildcard routes LAST - they catch everything else
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });

    // Activity routes
    Route::get('/activity', [ActivityController::class, 'index']);
    Route::get('/activity/export', [ActivityController::class, 'export']);

    // Log routes
    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/logs/download', [LogController::class, 'download']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // Setting routes
    Route::get('/settings/general', [SettingController::class, 'general']);
    Route::put('/settings/general', [SettingController::class, 'updateGeneral']);
    Route::get('/settings/security', [SettingController::class, 'security']);
    Route::put('/settings/security', [SettingController::class, 'updateSecurity']);
    Route::get('/settings/notifications', [SettingController::class, 'notifications']);
    Route::put('/settings/notifications', [SettingController::class, 'updateNotifications']);

    // Phone verification routes (protected)
    Route::post('/phone/verify/send', [PhoneController::class, 'sendVerification']);
    Route::post('/phone/verify/confirm', [PhoneController::class, 'confirmVerification']);
    Route::get('/phone/statistics', [PhoneController::class, 'getStatistics']);
});

// Health check route
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});

// SECURITY TEST: This should return 401 without authentication
Route::get('/test-auth', function () {
    return response()->json(['message' => 'This endpoint should be protected!']);
})->middleware('auth:sanctum'); 