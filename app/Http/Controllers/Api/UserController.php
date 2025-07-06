<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        // Middleware tanımlarını kaldırdım, çünkü Controller'da undefined method hatası veriyor.
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|exists:roles,name',
            'status' => 'sometimes|string|in:active,inactive,pending,suspended',
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
            $users = $this->userService->getUsers($request->all());
            
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Store user request received

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                new \App\Rules\PhoneNumberRule($request->input('phone_country', 'TR'))
            ],
            'phone_country' => 'sometimes|nullable|string|size:2',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string|in:active,inactive,pending,suspended',
            'password' => 'required|string|min:8|confirmed',
            'bio' => 'sometimes|nullable|string|max:1000',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
            'require_password_change' => 'sometimes|boolean',
            'send_welcome_email' => 'sometimes|nullable|boolean',
            'two_factor_enabled' => 'sometimes|nullable|boolean',
            // Location fields
            'address' => 'sometimes|nullable|string|max:500',
            'city' => 'sometimes|nullable|string|max:100',
            'country' => 'sometimes|nullable|string|max:100',
            'profile_public' => 'sometimes|nullable|boolean',
            'kvkk_approved' => 'sometimes|nullable|boolean',
            'admin_notes' => 'sometimes|nullable|string|max:2000',
            // Professional information arrays
            'education' => 'sometimes|nullable|array',
            'work_experience' => 'sometimes|nullable|array',
            'specialties' => 'sometimes|nullable|array',
            'certificates' => 'sometimes|nullable|array',
            'languages' => 'sometimes|nullable|array',
            'publications' => 'sometimes|nullable|array',
            'awards' => 'sometimes|nullable|array',
            'references' => 'sometimes|nullable|array',
            'insurances' => 'sometimes|nullable|array',
            'documents' => 'sometimes|nullable|array',
            // File upload
            'avatar_file' => 'sometimes|nullable|image|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            \Log::error('User creation validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

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
            
            $user = $this->userService->createUser($userData);
            
            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            \Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getUser($id);
            
            return response()->json([
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                new \App\Rules\PhoneNumberRule($request->input('phone_country', 'TR'))
            ],
            'phone_country' => 'sometimes|nullable|string|size:2',
            'role_id' => 'sometimes|required|exists:roles,id',
            'status' => 'sometimes|required|string|in:active,inactive,pending,suspended',
            'password' => 'sometimes|required|string|min:8|confirmed',
            'bio' => 'sometimes|nullable|string|max:1000',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
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
            
            $user = $this->userService->updateUser($id, $userData);
            
            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('User update failed', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update users
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|string|in:active,inactive,pending,suspended',
            'updates.role_id' => 'sometimes|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->userService->bulkUpdateUsers($request->all());
            
            return response()->json([
                'message' => 'Users updated successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        try {
            $file = $this->userService->exportUsers($request->all());
            
            return response()->download($file, 'users.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to export users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email to user
     */
    public function sendEmail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->userService->sendEmailToUser($id, $request->all());
            
            return response()->json([
                'message' => 'Email sent successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        try {
            $result = $this->userService->resetUserPassword($id);
            
            return response()->json([
                'message' => 'Password reset successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user login history
     */
    public function loginHistory($id)
    {
        try {
            $history = $this->userService->getUserLoginHistory($id);
            
            return response()->json([
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch login history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
