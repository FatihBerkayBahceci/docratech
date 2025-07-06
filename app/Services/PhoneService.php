<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class PhoneService
{
    private array $allowedCountries;
    private array $restrictedTypes;
    private array $countryData;

    public function __construct()
    {
        $this->allowedCountries = config('phone.allowed_countries', ['TR', 'US', 'GB', 'DE', 'FR']);
        $this->restrictedTypes = config('phone.restricted_types', ['premium']);
        $this->countryData = $this->getCountryData();
    }

    /**
     * Enterprise-level phone validation
     */
    public function validatePhone(string $phone, string $country = 'TR', array $options = []): array
    {
        $validation = [
            'is_valid' => false,
            'errors' => [],
            'warnings' => [],
            'metadata' => [],
            'formatted' => null,
            'e164' => null,
            'type' => null,
            'country' => null,
            'confidence_score' => 0
        ];

        // Clean phone number
        $phone = $this->cleanPhoneNumber($phone);

        if (empty($phone)) {
            if ($options['required'] ?? false) {
                $validation['errors'][] = 'Phone number is required';
            }
            return $validation;
        }

        // Length validation first (to fix the original issue)
        if (strlen($phone) > 20) {
            $validation['errors'][] = 'Phone number must not exceed 20 characters';
            return $validation;
        }
        
        if (strlen($phone) < 7) {
            $validation['errors'][] = 'Phone number is too short';
            return $validation;
        }

        try {
            // Basic format validation
            if (!$this->isValidFormat($phone)) {
                $validation['errors'][] = 'Invalid phone number format';
                return $validation;
            }

            // Advanced validation
            $phoneData = $this->parsePhoneNumber($phone, $country);
            
            if (!$phoneData['is_valid']) {
                $validation['errors'][] = 'Invalid phone number';
                return $validation;
            }

            // Country validation
            if (!in_array($phoneData['country'], $this->allowedCountries)) {
                $validation['errors'][] = "Phone numbers from {$phoneData['country']} are not allowed";
                return $validation;
            }

            // Type validation
            if (in_array($phoneData['type'], $this->restrictedTypes)) {
                $validation['errors'][] = 'This type of phone number is not allowed';
                return $validation;
            }

            // Mobile-only validation
            if (($options['mobile_only'] ?? false) && !$this->isMobileType($phoneData['type'])) {
                $validation['warnings'][] = 'Mobile number preferred for SMS notifications';
            }

            // Success case
            $validation['is_valid'] = true;
            $validation['formatted'] = $phoneData['formatted'];
            $validation['e164'] = $phoneData['e164'];
            $validation['type'] = $phoneData['type'];
            $validation['country'] = $phoneData['country'];
            $validation['confidence_score'] = $this->calculateConfidenceScore($phoneData, $country);
            
            // Metadata
            $validation['metadata'] = [
                'is_mobile' => $this->isMobileType($phoneData['type']),
                'carrier' => $phoneData['carrier'] ?? null,
                'region' => $this->getRegionInfo($phoneData['country']),
                'timezone' => $this->getTimezoneInfo($phoneData['country'])
            ];

        } catch (\Exception $e) {
            Log::warning('Phone validation fallback', [
                'phone' => $phone,
                'country' => $country,
                'error' => $e->getMessage()
            ]);
            
            // Graceful fallback - don't block user registration
            if ($this->isValidBasicFormat($phone)) {
                $validation['is_valid'] = true;
                $validation['formatted'] = $this->basicFormat($phone, $country);
                $validation['e164'] = $this->basicE164($phone, $country);
                $validation['type'] = 'unknown';
                $validation['country'] = $country;
                $validation['confidence_score'] = 60; // Lower confidence for fallback
                $validation['warnings'][] = 'Basic validation only - advanced features unavailable';
            } else {
                $validation['errors'][] = 'Invalid phone number format';
            }
        }

        return $validation;
    }

    /**
     * Parse phone number with custom logic
     */
    private function parsePhoneNumber(string $phone, string $country): array
    {
        $result = [
            'is_valid' => false,
            'formatted' => null,
            'e164' => null,
            'type' => 'unknown',
            'country' => $country,
            'carrier' => null
        ];

        // Remove all non-digit characters except +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        
        // Handle different formats
        if (strpos($cleanPhone, '+') === 0) {
            // International format
            $result['e164'] = $cleanPhone;
            $result['country'] = $this->detectCountryFromE164($cleanPhone);
        } else {
            // National format - add country code
            $countryCode = $this->getCountryCode($country);
            $result['e164'] = $countryCode . ltrim($cleanPhone, '0');
            $result['country'] = $country;
        }

        // Validate length and format
        if (strlen($result['e164']) >= 10 && strlen($result['e164']) <= 15) {
            $result['is_valid'] = true;
            $result['formatted'] = $this->formatPhoneNumber($result['e164'], $result['country']);
            $result['type'] = $this->detectPhoneType($result['e164'], $result['country']);
        }

        return $result;
    }

    /**
     * Format phone number for display
     */
    private function formatPhoneNumber(string $e164, string $country): string
    {
        $formats = [
            'TR' => function($phone) {
                // +90 (5xx) xxx xx xx
                if (preg_match('/^\+90(\d{3})(\d{3})(\d{2})(\d{2})$/', $phone, $matches)) {
                    return "+90 ({$matches[1]}) {$matches[2]} {$matches[3]} {$matches[4]}";
                }
                return $phone;
            },
            'US' => function($phone) {
                // +1 (xxx) xxx-xxxx
                if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone, $matches)) {
                    return "+1 ({$matches[1]}) {$matches[2]}-{$matches[3]}";
                }
                return $phone;
            },
            'GB' => function($phone) {
                // +44 xxxx xxx xxx
                if (preg_match('/^\+44(\d{4})(\d{3})(\d{3})$/', $phone, $matches)) {
                    return "+44 {$matches[1]} {$matches[2]} {$matches[3]}";
                }
                return $phone;
            }
        ];

        return isset($formats[$country]) ? $formats[$country]($e164) : $e164;
    }

    /**
     * Detect phone type
     */
    private function detectPhoneType(string $e164, string $country): string
    {
        $patterns = [
            'TR' => [
                'mobile' => '/^\+905\d{8}$/',
                'landline' => '/^\+90[2-4]\d{8}$/'
            ],
            'US' => [
                'mobile' => '/^\+1[2-9]\d{9}$/',  // Simplified
                'landline' => '/^\+1[2-9]\d{9}$/' // Simplified
            ],
            'GB' => [
                'mobile' => '/^\+447\d{9}$/',
                'landline' => '/^\+44[1-2]\d{9}$/'
            ]
        ];

        if (isset($patterns[$country])) {
            foreach ($patterns[$country] as $type => $pattern) {
                if (preg_match($pattern, $e164)) {
                    return $type;
                }
            }
        }

        return 'unknown';
    }

    /**
     * Normalize phone for database storage
     */
    public function normalizeForStorage(string $phone, string $country = 'TR'): array
    {
        $validation = $this->validatePhone($phone, $country);
        
        if (!$validation['is_valid']) {
            throw new \InvalidArgumentException('Cannot normalize invalid phone number: ' . implode(', ', $validation['errors']));
        }

        return [
            'phone' => $validation['formatted'],
            'phone_e164' => $validation['e164'],
            'phone_country' => $validation['country'],
            'phone_type' => $validation['type'],
            'phone_metadata' => $validation['metadata'],
            'phone_verified' => false // Default to unverified
        ];
    }

    /**
     * Generate verification code
     */
    public function generateVerificationCode(): array
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(config('phone.verification_expiry', 10));

        return [
            'code' => $code,
            'expires_at' => $expiresAt,
            'hash' => hash('sha256', $code . config('app.key'))
        ];
    }

    /**
     * Send SMS verification (placeholder - integrate with your SMS provider)
     */
    public function sendVerificationSMS(string $phone, string $code): bool
    {
        // TODO: Integrate with SMS provider (Twilio, AWS SNS, etc.)
        Log::info('SMS verification sent (SIMULATED)', [
            'phone' => $phone,
            'code_length' => strlen($code),
            'timestamp' => now()
        ]);

        // Simulate success
        return true;
    }

    /**
     * Verify SMS code
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->phone_verification_code) {
            return false;
        }

        if (now()->isAfter($user->phone_verification_expires_at)) {
            return false;
        }

        $expectedHash = hash('sha256', $code . config('app.key'));
        
        if ($expectedHash === $user->phone_verification_code) {
            $user->update([
                'phone_verified' => true,
                'phone_verified_at' => now(),
                'phone_verification_code' => null,
                'phone_verification_expires_at' => null
            ]);
            
            return true;
        }

        return false;
    }

    /**
     * Get phone statistics
     */
    public function getPhoneStatistics(): array
    {
        return Cache::remember('phone_statistics', 3600, function () {
            return [
                'total_phones' => User::whereNotNull('phone')->count(),
                'verified_phones' => User::where('phone_verified', true)->count(),
                'by_country' => User::whereNotNull('phone_country')
                    ->groupBy('phone_country')
                    ->selectRaw('phone_country, count(*) as count')
                    ->get()
                    ->keyBy('phone_country'),
                'by_type' => User::whereNotNull('phone_type')
                    ->groupBy('phone_type')
                    ->selectRaw('phone_type, count(*) as count')
                    ->get()
                    ->keyBy('phone_type'),
                'verification_rate' => $this->calculateVerificationRate()
            ];
        });
    }

    // Helper methods
    private function cleanPhoneNumber(string $phone): string
    {
        return trim($phone);
    }

    private function isValidFormat(string $phone): bool
    {
        return preg_match('/^[\d\s\+\-\(\)\.]+$/', $phone) === 1;
    }

    private function isMobileType(string $type): bool
    {
        return in_array($type, ['mobile']);
    }

    private function calculateConfidenceScore(array $phoneData, string $expectedCountry): int
    {
        $score = 50;

        // Country match bonus
        if ($phoneData['country'] === $expectedCountry) {
            $score += 25;
        }

        // Type-based scoring
        $typeScores = [
            'mobile' => 25,
            'landline' => 20,
            'toll_free' => 10,
            'premium' => 5
        ];

        $score += $typeScores[$phoneData['type']] ?? 5;

        return min(100, $score);
    }

    private function calculateVerificationRate(): float
    {
        $totalPhones = User::whereNotNull('phone')->count();
        $verifiedPhones = User::where('phone_verified', true)->count();
        
        return $totalPhones > 0 ? round(($verifiedPhones / $totalPhones) * 100, 2) : 0;
    }

    private function getCountryData(): array
    {
        return [
            'TR' => ['name' => 'Turkey', 'code' => '+90', 'flag' => '🇹🇷'],
            'US' => ['name' => 'United States', 'code' => '+1', 'flag' => '🇺🇸'],
            'GB' => ['name' => 'United Kingdom', 'code' => '+44', 'flag' => '🇬🇧'],
            'DE' => ['name' => 'Germany', 'code' => '+49', 'flag' => '🇩🇪'],
            'FR' => ['name' => 'France', 'code' => '+33', 'flag' => '🇫🇷']
        ];
    }

    private function getCountryCode(string $country): string
    {
        return $this->countryData[$country]['code'] ?? '+90';
    }

    private function detectCountryFromE164(string $e164): string
    {
        foreach ($this->countryData as $code => $data) {
            if (strpos($e164, $data['code']) === 0) {
                return $code;
            }
        }
        return 'TR'; // Default
    }

    private function getRegionInfo(string $country): string
    {
        return $this->countryData[$country]['name'] ?? 'Unknown';
    }

    private function getTimezoneInfo(string $country): string
    {
        $timezones = [
            'TR' => 'Europe/Istanbul',
            'US' => 'America/New_York',
            'GB' => 'Europe/London',
            'DE' => 'Europe/Berlin',
            'FR' => 'Europe/Paris'
        ];

        return $timezones[$country] ?? 'UTC';
    }

    /**
     * Basic format validation for fallback
     */
    private function isValidBasicFormat(string $phone): bool
    {
        // Remove all non-digits
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        
        // Check basic length requirements
        return strlen($cleaned) >= 7 && strlen($cleaned) <= 15;
    }

    /**
     * Basic formatting for fallback
     */
    private function basicFormat(string $phone, string $country): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        
        // Apply basic country formatting
        switch ($country) {
            case 'TR':
                if (strlen($cleaned) >= 10) {
                    return preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '($1) $2 $3 $4', $cleaned);
                }
                break;
            case 'US':
            case 'CA':
                if (strlen($cleaned) >= 10) {
                    return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $cleaned);
                }
                break;
        }
        
        return $phone;
    }

    /**
     * Basic E164 format for fallback
     */
    private function basicE164(string $phone, string $country): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        $countryCode = $this->getCountryCode($country);
        
        // Remove leading zeros and add country code
        $nationalNumber = ltrim($cleaned, '0');
        return $countryCode . $nationalNumber;
    }
} 