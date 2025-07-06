<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\SecurityEvent;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class AuthService
{
    /**
     * User login
     */
    public function login($email, $password)
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        
        // Check if user is suspended
        if ($user->status === 'suspended') {
            Auth::logout();
            return [
                'suspended' => true,
                'message' => 'Your account has been suspended. Please contact support for assistance.'
            ];
        }
        // Check if user is active
        if ($user->status !== 'active') {
            Auth::logout();
            $message = match($user->status) {
                'inactive' => 'Your account is currently inactive. Please contact support for assistance.',
                'pending' => 'Your account is pending approval. Please wait for administrator approval.',
                default => 'Your account is not active. Please contact support for assistance.',
            };
            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }

        // Create tokens
        $token = $user->createToken('auth-token')->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'])->plainTextToken;

        // Update last login
        $user->update([
            'last_login_at' => now(),
        ]);

        // Log activity
        $this->logActivity($user, 'login', 'User logged in successfully');

        return [
            'user' => $user->load(['roles', 'permissions']),
            'token' => $token,
            'refresh_token' => $refreshToken,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->getRoleNames()->toArray(),
        ];
    }

    /**
     * User registration
     */
    public function register($data)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);

            // Assign default role if specified
            if (isset($data['role_id'])) {
                $user->role_id = $data['role_id'];
                $user->save();
            }

            // Create tokens
            $token = $user->createToken('auth-token')->plainTextToken;
            $refreshToken = $user->createToken('refresh-token', ['refresh'])->plainTextToken;

            // Log activity
            $this->logActivity($user, 'register', 'User registered successfully');

            DB::commit();

            return [
                'user' => $user->load(['roles', 'permissions']),
                'token' => $token,
                'refresh_token' => $refreshToken,
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'roles' => $user->getRoleNames()->toArray(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * User logout
     */
    public function logout($user)
    {
        // Revoke all tokens
        $user->tokens()->delete();

        // Log activity
        $this->logActivity($user, 'logout', 'User logged out successfully');

        return true;
    }

    /**
     * Refresh token
     */
    public function refreshToken($refreshToken)
    {
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($refreshToken);

        if (!$token || !$token->can('refresh')) {
            throw new \Exception('Invalid refresh token');
        }

        $user = $token->tokenable;

        // Revoke old tokens
        $user->tokens()->where('name', 'auth-token')->delete();

        // Create new token
        $newToken = $user->createToken('auth-token')->plainTextToken;

        return [
            'token' => $newToken,
            'refresh_token' => $refreshToken,
            'user' => $user->load(['roles', 'permissions']),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->getRoleNames()->toArray(),
        ];
    }

    /**
     * Update user profile
     */
    public function updateProfile($user, $data)
    {
        $user->update($data);

        // Log activity
        $this->logActivity($user, 'update', 'Profile updated successfully');

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Log activity
     */
    private function logActivity($user, $event, $description)
    {
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
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
     * Attempt to authenticate a user.
     */
    public function attemptLogin(string $email, string $password, Request $request): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->logSecurityEvent('failed_login', 'high', 'Invalid email address', [
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Check if user is active
        if (!$user->is_active) {
            $this->logSecurityEvent('failed_login', 'medium', 'Inactive user login attempt', [
                'user_id' => $user->id,
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);
            return ['success' => false, 'message' => 'Account is deactivated'];
        }

        // Check if user is locked
        if ($user->isLocked()) {
            $this->logSecurityEvent('failed_login', 'high', 'Locked user login attempt', [
                'user_id' => $user->id,
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);
            return ['success' => false, 'message' => 'Account is temporarily locked'];
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            $this->handleFailedLogin($user, $request);
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Successful login
        $this->handleSuccessfulLogin($user, $request);
        
        return [
            'success' => true,
            'user' => $user,
            'token' => $this->createToken($user, $request),
        ];
    }

    /**
     * Handle failed login attempt.
     */
    private function handleFailedLogin(User $user, Request $request): void
    {
        $user->increment('failed_login_attempts');
        
        $maxAttempts = config('auth.max_attempts', 5);
        $lockoutMinutes = config('auth.lockout_minutes', 15);

        if ($user->failed_login_attempts >= $maxAttempts) {
            $user->update([
                'locked_until' => now()->addMinutes($lockoutMinutes),
            ]);

            $this->logSecurityEvent('account_locked', 'high', 'Account locked due to multiple failed login attempts', [
                'user_id' => $user->id,
                'failed_attempts' => $user->failed_login_attempts,
                'ip_address' => $request->ip(),
            ]);
        }

        $this->logSecurityEvent('failed_login', 'medium', 'Invalid password', [
            'user_id' => $user->id,
            'failed_attempts' => $user->failed_login_attempts,
            'ip_address' => $request->ip(),
        ]);
    }

    /**
     * Handle successful login.
     */
    private function handleSuccessfulLogin(User $user, Request $request): void
    {
        // Reset failed login attempts
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $this->logSecurityEvent('successful_login', 'low', 'User logged in successfully', [
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        $this->logAuditEvent('auth', 'login', $user, 'User logged in successfully');
    }

    /**
     * Create authentication token.
     */
    private function createToken(User $user, Request $request): string
    {
        $token = $user->createToken('auth-token')->plainTextToken;
        
        // Create session record
        $this->createUserSession($user, $token, $request);
        
        return $token;
    }

    /**
     * Create user session record.
     */
    private function createUserSession(User $user, string $token, Request $request): void
    {
        $userAgent = $request->userAgent();
        $deviceInfo = $this->parseUserAgent($userAgent);

        UserSession::create([
            'user_id' => $user->id,
            'session_id' => Str::uuid(),
            'token_hash' => hash('sha256', $token),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],
            'location' => $this->getLocationFromIp($request->ip()),
            'last_activity' => now(),
            'expires_at' => now()->addHours(config('sanctum.expiration', 24)),
            'is_active' => true,
            'is_remembered' => false,
            'metadata' => [
                'created_via' => 'login',
                'device_info' => $deviceInfo,
            ],
        ]);
    }

    /**
     * Parse user agent string.
     */
    private function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'platform' => 'unknown',
            ];
        }

        // Simple parsing - in production, use a proper library like jenssegers/agent
        $deviceType = 'desktop';
        $browser = 'unknown';
        $platform = 'unknown';

        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'tablet';
        }

        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }

        if (preg_match('/Windows/', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    /**
     * Get location from IP address.
     */
    private function getLocationFromIp(string $ip): ?string
    {
        // In production, use a proper IP geolocation service
        // For now, return null
        return null;
    }

    /**
     * Log security event.
     */
    private function logSecurityEvent(string $eventType, string $severity, string $description, array $details = []): void
    {
        SecurityEvent::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log audit event.
     */
    private function logAuditEvent(string $eventType, string $action, User $user, string $description): void
    {
        AuditLog::create([
            'user_id' => $user->id,
            'event_type' => $eventType,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Get user sessions.
     */
    public function getUserSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->sessions()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Revoke user session.
     */
    public function revokeSession(User $user, int $sessionId): bool
    {
        $session = UserSession::where('user_id', $user->id)
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return false;
        }

        $session->update(['is_active' => false]);

        $this->logAuditEvent('session', 'revoke', $user, 'Session revoked by user');

        return true;
    }

    /**
     * Revoke all user sessions except current.
     */
    public function revokeAllSessions(User $user, Request $request): void
    {
        $currentTokenHash = hash('sha256', $request->bearerToken());

        UserSession::where('user_id', $user->id)
            ->where('token_hash', '!=', $currentTokenHash)
            ->update(['is_active' => false]);

        $this->logAuditEvent('session', 'revoke_all', $user, 'All sessions revoked except current');
    }

    /**
     * Enable two-factor authentication.
     */
    public function enableTwoFactor(User $user): array
    {
        $secret = $this->generateTwoFactorSecret();
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        $this->logAuditEvent('security', 'enable_2fa', $user, 'Two-factor authentication enabled');

        return [
            'secret' => $secret,
            'recovery_codes' => $recoveryCodes,
        ];
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        $this->logAuditEvent('security', 'disable_2fa', $user, 'Two-factor authentication disabled');
    }

    /**
     * Generate two-factor secret.
     */
    private function generateTwoFactorSecret(): string
    {
        return Str::random(32);
    }

    /**
     * Generate recovery codes.
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Verify two-factor code.
     */
    public function verifyTwoFactorCode(User $user, string $code): bool
    {
        // In production, use a proper 2FA library like google2fa
        // For now, simple verification
        return $code === $user->two_factor_secret;
    }

    /**
     * Verify recovery code.
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        
        if (in_array($code, $recoveryCodes)) {
            // Remove used code
            $recoveryCodes = array_diff($recoveryCodes, [$code]);
            $user->update(['two_factor_recovery_codes' => array_values($recoveryCodes)]);
            
            return true;
        }

        return false;
    }
} 