<?php

namespace App\Rules;

use App\Services\PhoneService;
use Illuminate\Contracts\Validation\Rule;

class PhoneNumberRule implements Rule
{
    private string $country;
    private array $options;
    private array $errors = [];

    public function __construct(string $country = 'TR', array $options = [])
    {
        $this->country = $country;
        $this->options = $options;
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true; // Let required rule handle empty values
        }

        $phoneService = app(PhoneService::class);
        $validation = $phoneService->validatePhone($value, $this->country, $this->options);

        if (!$validation['is_valid']) {
            $this->errors = $validation['errors'];
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return !empty($this->errors) 
            ? implode(', ', $this->errors)
            : 'The :attribute must be a valid phone number.';
    }
} 