<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->authService->login($request->email, $request->password);
            
            // Suspended user special response
            if (isset($result['suspended']) && $result['suspended'] === true) {
                return response()->json([
                    'message' => $result['message'],
                    'status' => 'suspended'
                ], 200);
            }
            
            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'access_token' => $result['token'],
                'refresh_token' => $result['refresh_token'],
                'permissions' => $result['permissions'],
                'roles' => $result['roles']
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed',
                'error' => 'An error occurred during login'
            ], 500);
        }
    }

    /**
     * User registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->authService->register($request->all());
            
            return response()->json([
                'message' => 'Registration successful',
                'user' => $result['user'],
                'access_token' => $result['token'],
                'refresh_token' => $result['refresh_token'],
                'permissions' => $result['permissions'],
                'roles' => $result['roles']
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            
            return response()->json([
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->authService->refreshToken($request->refresh_token);
            
            return response()->json([
                'message' => 'Token refreshed successfully',
                'access_token' => $result['token'],
                'refresh_token' => $result['refresh_token'],
                'user' => $result['user'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load(['roles', 'permissions']);
        
        return response()->json([
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->getRoleNames()->toArray()
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                new \App\Rules\PhoneNumberRule($request->input('phone_country', 'TR'))
            ],
            'phone_country' => 'sometimes|nullable|string|size:2',
            'bio' => 'sometimes|nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = $request->all();
            
            // Enhanced phone processing
            if ($request->filled('phone')) {
                $phoneService = app(\App\Services\PhoneService::class);
                $phoneData = $phoneService->normalizeForStorage(
                    $request->phone,
                    $request->phone_country ?? 'TR'
                );
                $userData = array_merge($userData, $phoneData);
            }
            
            $updatedUser = $this->authService->updateProfile($user, $userData);
            
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $updatedUser
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            
            return response()->json([
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
            
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Password reset link sent successfully'
                ]);
            } else {
                return response()->json([
                    'message' => 'Unable to send reset link',
                    'error' => $status
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password reset failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::reset($request->all(), function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            });
            
            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => 'Password reset successfully'
                ]);
            } else {
                return response()->json([
                    'message' => 'Password reset failed',
                    'error' => $status
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password reset failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
