<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PhoneService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PhoneController extends Controller
{
    private PhoneService $phoneService;

    public function __construct(PhoneService $phoneService)
    {
        $this->phoneService = $phoneService;
    }

    /**
     * Validate phone number
     */
    public function validatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'country' => 'sometimes|string|size:2',
            'options' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phone = $request->input('phone');
            $country = $request->input('country', 'TR');
            $options = $request->input('options', []);

            $validation = $this->phoneService->validatePhone($phone, $country, $options);

            return response()->json($validation);
        } catch (\Exception $e) {
            Log::error('Phone validation API error', [
                'error' => $e->getMessage(),
                'phone' => $request->input('phone'),
                'country' => $request->input('country')
            ]);

            return response()->json([
                'is_valid' => false,
                'errors' => ['Validation service temporarily unavailable']
            ], 500);
        }
    }

    /**
     * Send verification SMS
     */
    public function sendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'country' => 'sometimes|string|size:2',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phone = $request->input('phone');
            $country = $request->input('country', 'TR');
            $userId = $request->input('user_id');

            // First validate the phone number
            $validation = $this->phoneService->validatePhone($phone, $country);
            
            if (!$validation['is_valid']) {
                return response()->json([
                    'message' => 'Invalid phone number',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Generate verification code
            $verification = $this->phoneService->generateVerificationCode();

            // If user_id provided, update user record
            if ($userId) {
                $user = User::findOrFail($userId);
                $user->update([
                    'phone_verification_code' => $verification['hash'],
                    'phone_verification_expires_at' => $verification['expires_at']
                ]);
            }

            // Send SMS
            $sent = $this->phoneService->sendVerificationSMS($validation['e164'], $verification['code']);

            if ($sent) {
                return response()->json([
                    'message' => 'Verification code sent successfully',
                    'expires_at' => $verification['expires_at']
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to send verification code'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Phone verification send error', [
                'error' => $e->getMessage(),
                'phone' => $request->input('phone'),
                'user_id' => $request->input('user_id')
            ]);

            return response()->json([
                'message' => 'Verification service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Confirm verification code
     */
    public function confirmVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->input('user_id'));
            $code = $request->input('code');

            $verified = $this->phoneService->verifyCode($user, $code);

            if ($verified) {
                return response()->json([
                    'message' => 'Phone number verified successfully',
                    'verified' => true
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid or expired verification code',
                    'verified' => false
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Phone verification confirm error', [
                'error' => $e->getMessage(),
                'user_id' => $request->input('user_id')
            ]);

            return response()->json([
                'message' => 'Verification service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Get phone statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $statistics = $this->phoneService->getPhoneStatistics();

            return response()->json([
                'message' => 'Phone statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Phone statistics error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Statistics service temporarily unavailable'
            ], 500);
        }
    }
} 