<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;
use App\Mail\TempPasswordEmail;

class UserService
{
    /**
     * Get users with filters
     */
    public function getUsers($filters = [])
    {
        $query = User::with(['role', 'permissions'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if (isset($filters['role']) && $filters['role']) {
            $query->whereHas('role', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Apply status filter
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Apply date range filter
        if (isset($filters['date_range'])) {
            $query->whereBetween('created_at', [
                $filters['date_range']['start'],
                $filters['date_range']['end']
            ]);
        }

        // Apply sorting
        if (isset($filters['sort_by'])) {
            $sortBy = $filters['sort_by'];
            $sortOrder = $filters['sort_order'] ?? 'asc';
            
            switch ($sortBy) {
                case 'name':
                    $query->orderBy('first_name', $sortOrder)
                          ->orderBy('last_name', $sortOrder);
                    break;
                case 'email':
                    $query->orderBy('email', $sortOrder);
                    break;
                case 'role':
                    $query->join('roles', 'users.role_id', '=', 'roles.id')
                          ->orderBy('roles.name', $sortOrder);
                    break;
                case 'created':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                case 'last_active':
                    $query->orderBy('last_active_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        $paginatedResults = $query->paginate($filters['per_page'] ?? 20);

        // Get statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $newUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        // Format response
        $data = $paginatedResults->items();
        
        // Add computed fields to each user
        foreach ($data as $user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
            $user->isOnline = $user->last_active_at && $user->last_active_at->gt(now()->subMinutes(5));
            $user->lastActive = $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never';
        }

        // CRITICAL: Convert all users to array for proper JSON serialization (including relations)
        $data = array_map(function($user) { return $user->toArray(); }, $data);

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginatedResults->currentPage(),
                'per_page' => $paginatedResults->perPage(),
                'last_page' => $paginatedResults->lastPage(),
                'total' => $paginatedResults->total(),
                'from' => $paginatedResults->firstItem(),
                'to' => $paginatedResults->lastItem(),
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'new_users' => $newUsers,
                'stats' => [
                    'totalGrowth' => $this->calculateGrowth($totalUsers, 'total'),
                    'activeGrowth' => $this->calculateGrowth($activeUsers, 'active'),
                    'inactiveGrowth' => $this->calculateGrowth($inactiveUsers, 'inactive'),
                    'newGrowth' => $this->calculateGrowth($newUsers, 'new'),
                ]
            ]
        ];
    }

    /**
     * Create new user
     */
    public function createUser($data)
    {
        DB::beginTransaction();

        try {
            // Convert string booleans to actual booleans
            $data = $this->convertBooleanFields($data);
            
            // Handle file upload
            $profilePhoto = null;
            if (isset($data['avatar_file']) && $data['avatar_file']) {
                $profilePhoto = $this->handleFileUpload($data['avatar_file'], 'avatars');
            }

            // Decode JSON arrays
            $professionalData = $this->decodeProfessionalArrays($data);

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'role_id' => $data['role_id'],
                'status' => $data['status'],
                'password' => Hash::make($data['password']),
                'bio' => $data['bio'] ?? null,
                // Security settings
                'two_factor_enabled' => $data['two_factor_enabled'] ?? false,
                'email_verified' => $data['email_verified'] ?? false,
                // Location & contact
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'profile_photo' => $profilePhoto,
                // Settings
                'profile_public' => $data['profile_public'] ?? true,
                'kvkk_approved' => $data['kvkk_approved'] ?? false,
                'admin_notes' => $data['admin_notes'] ?? null,
                // Professional information
                ...$professionalData,
            ]);

            // Assign Spatie role if role_id is provided
            if (isset($data['role_id']) && $data['role_id']) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            // Assign permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            // Send welcome email if requested
            if (isset($data['send_welcome_email']) && $data['send_welcome_email']) {
                try {
                    Mail::to($user->email)->send(new WelcomeEmail($user, $data['password']));
                    \Log::info('Welcome email sent successfully to user: ' . $user->email);
                } catch (\Exception $emailException) {
                    // Log email error but don't fail user creation
                    \Log::error('Failed to send welcome email to user: ' . $user->email, [
                        'error' => $emailException->getMessage(),
                        'user_id' => $user->id
                    ]);
                    // You could also store this info to notify admin later
                }
            }

            // Setup 2FA if enabled
            if ($data['two_factor_enabled'] ?? false) {
                $this->setup2FA($user);
            }

            // Log activity
            $this->logActivity($user, 'create', 'User created successfully');

            DB::commit();

            return $user->load(['role', 'permissions']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user by ID
     */
    public function getUser($id)
    {
        $user = User::with(['role', 'permissions'])->findOrFail($id);
        
        // Add additional data
        $user->load(['auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return $user;
    }

    /**
     * Update user
     */
    public function updateUser($id, $data)
    {
        DB::beginTransaction();

        try {
            // Convert string booleans to actual booleans
            $data = $this->convertBooleanFields($data);
            
            $user = User::findOrFail($id);

            // Handle file upload
            $profilePhoto = $user->profile_photo;
            if (isset($data['avatar_file']) && $data['avatar_file']) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    $this->deleteFile($user->profile_photo);
                }
                $profilePhoto = $this->handleFileUpload($data['avatar_file'], 'avatars');
            }

            // Decode JSON arrays
            $professionalData = $this->decodeProfessionalArrays($data);

            // Update basic info
            $updateData = [
                'first_name' => $data['first_name'] ?? $user->first_name,
                'last_name' => $data['last_name'] ?? $user->last_name,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                'role_id' => $data['role_id'] ?? $user->role_id,
                'status' => $data['status'] ?? $user->status,
                'bio' => $data['bio'] ?? $user->bio,
                // Location & contact
                'address' => $data['address'] ?? $user->address,
                'city' => $data['city'] ?? $user->city,
                'country' => $data['country'] ?? $user->country,
                'profile_photo' => $profilePhoto,
                // Settings
                'profile_public' => $data['profile_public'] ?? $user->profile_public,
                'kvkk_approved' => $data['kvkk_approved'] ?? $user->kvkk_approved,
                'admin_notes' => $data['admin_notes'] ?? $user->admin_notes,
                // Professional information
                ...$professionalData,
            ];

            // Handle 2FA toggle
            if (isset($data['two_factor_enabled'])) {
                $updateData['two_factor_enabled'] = $data['two_factor_enabled'];
                
                if ($data['two_factor_enabled'] && !$user->two_factor_enabled) {
                    // Enable 2FA
                    $this->setup2FA($user);
                } elseif (!$data['two_factor_enabled'] && $user->two_factor_enabled) {
                    // Disable 2FA
                    $this->disable2FA($user);
                }
            }

            $user->update($updateData);

            // Update Spatie role if role_id is provided
            if (isset($data['role_id']) && $data['role_id']) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    // Remove existing roles and assign new one
                    $user->syncRoles([$role->name]);
                }
            }

            // Update password if provided
            if (isset($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            // Update permissions if provided
            if (isset($data['permissions'])) {
                $user->permissions()->sync($data['permissions']);
            }

            // Log activity
            $this->logActivity($user, 'update', 'User updated successfully');

            DB::commit();

            return $user->load(['role', 'permissions']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            // Check if user can be deleted
            if ($user->role && $user->role->type === 'system') {
                throw new \Exception('System users cannot be deleted');
            }

            // Log activity before deletion
            $this->logActivity($user, 'delete', 'User deleted successfully');

            // Delete user
            $user->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk update users
     */
    public function bulkUpdateUsers($data)
    {
        DB::beginTransaction();

        try {
            $users = User::whereIn('id', $data['user_ids'])->get();
            $updatedCount = 0;

            foreach ($users as $user) {
                $user->update($data['updates']);
                $updatedCount++;

                // Log activity
                $this->logActivity($user, 'bulk_update', 'User updated via bulk operation');
            }

            DB::commit();

            return [
                'updated_count' => $updatedCount,
                'message' => "Successfully updated {$updatedCount} users"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Export users
     */
    public function exportUsers($filters = [])
    {
        $users = $this->getUsers($filters);

        // Create Excel file
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.xlsx';
        $path = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Create Excel file using a library like PhpSpreadsheet
        // This is a simplified version - you'll need to implement actual Excel generation
        $data = [
            ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At'],
        ];

        foreach ($users['data'] as $user) {
            $data[] = [
                $user['id'],
                $user['name'],
                $user['email'],
                $user['role']['name'] ?? 'No Role',
                $user['status'],
                $user['created_at']->format('Y-m-d H:i:s'),
            ];
        }

        // Write to CSV for now (replace with Excel generation)
        $handle = fopen($path, 'w');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return $path;
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($currentValue, $type)
    {
        // Get previous month's count for comparison
        $previousMonth = now()->subMonth();
        
        $previousValue = match($type) {
            'total' => User::where('created_at', '<', $previousMonth)->count(),
            'active' => User::where('status', 'active')
                         ->where('created_at', '<', $previousMonth)
                         ->count(),
            'inactive' => User::where('status', 'inactive')
                           ->where('created_at', '<', $previousMonth)
                           ->count(),
            'new' => User::where('created_at', '>=', $previousMonth->copy()->subDays(30))
                      ->where('created_at', '<', $previousMonth)
                      ->count(),
            default => 0
        };

        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file, $directory = 'uploads')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/' . $directory, $filename);
        
        return str_replace('public/', 'storage/', $path);
    }

    /**
     * Delete file
     */
    private function deleteFile($filePath)
    {
        if ($filePath && file_exists(public_path($filePath))) {
            unlink(public_path($filePath));
        }
    }

    /**
     * Process professional arrays (handles both JSON strings and arrays)
     */
    private function decodeProfessionalArrays($data)
    {
        $professionalFields = [
            'education', 'work_experience', 'specialties', 'certificates',
            'languages', 'publications', 'awards', 'references',
            'insurances', 'documents'
        ];

        $decoded = [];
        foreach ($professionalFields as $field) {
            if (isset($data[$field])) {
                if (is_string($data[$field])) {
                    // If it's a JSON string, decode it
                    $decoded[$field] = json_decode($data[$field], true) ?: [];
                } elseif (is_array($data[$field])) {
                    // If it's already an array, use it directly
                    $decoded[$field] = $data[$field];
                } else {
                    // Default to empty array
                    $decoded[$field] = [];
                }
            }
        }

        return $decoded;
    }

    /**
     * Convert string boolean values to actual booleans
     */
    private function convertBooleanFields($data)
    {
        $booleanFields = [
            'send_welcome_email',
            'two_factor_enabled', 
            'profile_public',
            'kvkk_approved',
            'email_verified'
        ];

        foreach ($booleanFields as $field) {
            if (isset($data[$field])) {
                // Convert string "true"/"false", "1"/"0", 1/0, true/false to proper boolean
                $value = $data[$field];
                $data[$field] = $value === true || $value === 'true' || $value === '1' || $value === 1;
            }
        }

        return $data;
    }

    /**
     * Setup 2FA for user
     */
    private function setup2FA($user)
    {
        try {
            // Generate 2FA secret using Google Authenticator compatible format
            $secret = $this->generate2FASecret();
            
            $user->update([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => $this->generate2FARecoveryCodes(),
            ]);

            // TODO: Send 2FA setup instructions via email or notification
            // Mail::to($user->email)->send(new TwoFactorSetupEmail($user, $secret));
            
            return $secret;
        } catch (\Exception $e) {
            \Log::error('2FA setup failed for user ' . $user->id . ': ' . $e->getMessage());
            throw new \Exception('Failed to setup 2FA: ' . $e->getMessage());
        }
    }

    /**
     * Disable 2FA for user
     */
    private function disable2FA($user)
    {
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled' => false,
        ]);
    }

    /**
     * Generate 2FA secret
     */
    private function generate2FASecret()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        
        for ($i = 0; $i < 32; $i++) {
            $secret .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $secret;
    }

    /**
     * Generate 2FA recovery codes
     */
    private function generate2FARecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Log activity
     */
    private function logActivity($user, $event, $description)
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => $event,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'url' => request()->url(),
                'method' => request()->method(),
            ],
        ]);
    }

    /**
     * Send email to user
     */
    public function sendEmailToUser($userId, $emailData)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Create a simple email class for custom messages
            Mail::to($user->email)->send(new \App\Mail\CustomUserEmail(
                $user,
                $emailData['subject'],
                $emailData['body']
            ));
            
            // Log activity
            $this->logActivity($user, 'email_sent', "Email sent with subject: {$emailData['subject']}");
            
            return [
                'sent_to' => $user->email,
                'subject' => $emailData['subject'],
                'sent_at' => now()
            ];
            
        } catch (\Exception $e) {
            \Log::error('Failed to send email to user ' . $userId . ': ' . $e->getMessage());
            throw new \Exception('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetUserPassword($userId)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($userId);
            
            // Generate new temporary password
            $newPassword = $this->generateTemporaryPassword();
            
            // Update user password
            $user->update([
                'password' => Hash::make($newPassword),
                'password_changed_at' => null, // Force password change on next login
                'require_password_change' => true,
            ]);
            
            // Revoke all active sessions/tokens (if using Sanctum)
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }
            
            // Send temporary password email
            try {
                Mail::to($user->email)->send(new TempPasswordEmail($user, $newPassword));
            } catch (\Exception $emailException) {
                \Log::error('Failed to send temporary password email to user: ' . $user->email, [
                    'error' => $emailException->getMessage(),
                    'user_id' => $user->id
                ]);
                // Don't fail the password reset if email fails
            }
            
            // Log activity
            $this->logActivity($user, 'password_reset', 'Password reset by administrator');
            
            DB::commit();
            
            return [
                'user_id' => $user->id,
                'email' => $user->email,
                'reset_at' => now(),
                'temporary_password_sent' => true
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to reset password for user ' . $userId . ': ' . $e->getMessage());
            throw new \Exception('Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Get user login history
     */
    public function getUserLoginHistory($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Get login history from audit logs
            $loginHistory = \App\Models\AuditLog::where('subject_type', User::class)
                ->where('subject_id', $userId)
                ->whereIn('event', ['login', 'logout', 'failed_login'])
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'event' => $log->event,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'user_agent' => $log->user_agent,
                        'created_at' => $log->created_at,
                        'location' => $this->getLocationFromIP($log->ip_address),
                        'device' => $this->parseUserAgent($log->user_agent)
                    ];
                });
            
            // Get current active sessions if applicable
            $activeSessions = [];
            if (method_exists($user, 'tokens')) {
                $activeSessions = $user->tokens()
                    ->where('expires_at', '>', now())
                    ->orWhereNull('expires_at')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($token) {
                        return [
                            'id' => $token->id,
                            'name' => $token->name,
                            'abilities' => $token->abilities,
                            'last_used_at' => $token->last_used_at,
                            'created_at' => $token->created_at,
                            'expires_at' => $token->expires_at
                        ];
                    });
            }
            
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email
                ],
                'login_history' => $loginHistory,
                'active_sessions' => $activeSessions,
                'summary' => [
                    'total_logins' => $loginHistory->where('event', 'login')->count(),
                    'failed_logins' => $loginHistory->where('event', 'failed_login')->count(),
                    'last_login' => $loginHistory->where('event', 'login')->first()?->created_at,
                    'last_ip' => $loginHistory->first()?->ip_address
                ]
            ];
            
        } catch (\Exception $e) {
            \Log::error('Failed to get login history for user ' . $userId . ': ' . $e->getMessage());
            throw new \Exception('Failed to fetch login history: ' . $e->getMessage());
        }
    }

    /**
     * Generate temporary password
     */
    private function generateTemporaryPassword()
    {
        return 'Temp' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT) . '!';
    }

    /**
     * Get location from IP address (placeholder)
     */
    private function getLocationFromIP($ip)
    {
        // This is a placeholder - you would integrate with a real IP geolocation service
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Local Development';
        }
        
        return 'Unknown Location';
    }

    /**
     * Parse user agent to get device info
     */
    private function parseUserAgent($userAgent)
    {
        if (!$userAgent) return 'Unknown Device';
        
        // Basic user agent parsing - you might want to use a library like jenssegers/agent
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome Browser';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox Browser';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari Browser';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge Browser';
        } else {
            return 'Unknown Browser';
        }
    }
} 