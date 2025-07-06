<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Phone Validation Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for phone number validation,
    | verification, and processing throughout the application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Allowed Countries
    |--------------------------------------------------------------------------
    |
    | List of ISO 3166-1 alpha-2 country codes that are allowed for phone
    | number registration. Users can only register phone numbers from
    | these countries.
    |
    */
    'allowed_countries' => [
        'TR', // Turkey
        'US', // United States
        'GB', // United Kingdom
        'DE', // Germany
        'FR', // France
        'CA', // Canada
        'AU', // Australia
        'NL', // Netherlands
        'BE', // Belgium
        'SE', // Sweden
        'NO', // Norway
        'DK', // Denmark
        'FI', // Finland
        'CH', // Switzerland
        'AT', // Austria
        'IT', // Italy
        'ES', // Spain
        'PT', // Portugal
        'PL', // Poland
        'CZ', // Czech Republic
        'HU', // Hungary
        'RO', // Romania
        'BG', // Bulgaria
        'HR', // Croatia
        'SI', // Slovenia
        'SK', // Slovakia
        'LT', // Lithuania
        'LV', // Latvia
        'EE', // Estonia
        'IE', // Ireland
        'LU', // Luxembourg
        'MT', // Malta
        'CY', // Cyprus
        'GR', // Greece
        'JP', // Japan
        'KR', // South Korea
        'SG', // Singapore
        'HK', // Hong Kong
        'MY', // Malaysia
        'TH', // Thailand
        'ID', // Indonesia
        'PH', // Philippines
        'VN', // Vietnam
        'IN', // India
        'BD', // Bangladesh
        'PK', // Pakistan
        'LK', // Sri Lanka
        'AE', // United Arab Emirates
        'SA', // Saudi Arabia
        'QA', // Qatar
        'KW', // Kuwait
        'BH', // Bahrain
        'OM', // Oman
        'JO', // Jordan
        'LB', // Lebanon
        'IL', // Israel
        'EG', // Egypt
        'MA', // Morocco
        'TN', // Tunisia
        'DZ', // Algeria
        'ZA', // South Africa
        'KE', // Kenya
        'GH', // Ghana
        'NG', // Nigeria
        'BR', // Brazil
        'AR', // Argentina
        'CL', // Chile
        'CO', // Colombia
        'PE', // Peru
        'UY', // Uruguay
        'MX', // Mexico
        'CR', // Costa Rica
        'PA', // Panama
        'GT', // Guatemala
        'NI', // Nicaragua
        'HN', // Honduras
        'SV', // El Salvador
        'DO', // Dominican Republic
        'CU', // Cuba
        'JM', // Jamaica
        'TT', // Trinidad and Tobago
        'BB', // Barbados
        'BS', // Bahamas
        'BZ', // Belize
        'GY', // Guyana
        'SR', // Suriname
        'NZ', // New Zealand
        'FJ', // Fiji
        'PG', // Papua New Guinea
        'SB', // Solomon Islands
        'VU', // Vanuatu
        'NC', // New Caledonia
        'PF', // French Polynesia
        'RU', // Russia
        'UA', // Ukraine
        'BY', // Belarus
        'MD', // Moldova
        'AM', // Armenia
        'AZ', // Azerbaijan
        'GE', // Georgia
        'KZ', // Kazakhstan
        'KG', // Kyrgyzstan
        'TJ', // Tajikistan
        'TM', // Turkmenistan
        'UZ', // Uzbekistan
        'MN', // Mongolia
        'CN', // China
        'TW', // Taiwan
        'HK', // Hong Kong
        'MO', // Macau
        'KP', // North Korea
        'AF', // Afghanistan
        'IQ', // Iraq
        'IR', // Iran
        'SY', // Syria
        'YE', // Yemen
        'LY', // Libya
        'SD', // Sudan
        'SS', // South Sudan
        'ET', // Ethiopia
        'ER', // Eritrea
        'DJ', // Djibouti
        'SO', // Somalia
        'UG', // Uganda
        'TZ', // Tanzania
        'RW', // Rwanda
        'BI', // Burundi
        'KM', // Comoros
        'MG', // Madagascar
        'MU', // Mauritius
        'SC', // Seychelles
        'MV', // Maldives
        'CD', // Democratic Republic of the Congo
        'CG', // Republic of the Congo
        'CF', // Central African Republic
        'TD', // Chad
        'CM', // Cameroon
        'GQ', // Equatorial Guinea
        'GA', // Gabon
        'ST', // Sao Tome and Principe
        'BF', // Burkina Faso
        'CI', // Ivory Coast
        'GN', // Guinea
        'GW', // Guinea-Bissau
        'LR', // Liberia
        'SL', // Sierra Leone
        'ML', // Mali
        'MR', // Mauritania
        'NE', // Niger
        'SN', // Senegal
        'GM', // Gambia
        'CV', // Cape Verde
        'BJ', // Benin
        'TG', // Togo
        'ZM', // Zambia
        'ZW', // Zimbabwe
        'BW', // Botswana
        'SZ', // Eswatini
        'LS', // Lesotho
        'MW', // Malawi
        'MZ', // Mozambique
        'AO', // Angola
        'NA', // Namibia
    ],

    /*
    |--------------------------------------------------------------------------
    | Restricted Phone Types
    |--------------------------------------------------------------------------
    |
    | Phone number types that are not allowed for registration.
    | Available types: mobile, landline, toll_free, premium, unknown
    |
    */
    'restricted_types' => [
        'premium', // Premium rate numbers
        // 'toll_free', // Uncomment to restrict toll-free numbers
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS verification system.
    |
    */
    'verification_expiry' => 10, // Minutes
    'verification_attempts' => 3, // Max attempts per phone number
    'verification_cooldown' => 60, // Seconds between attempts
    'verification_required' => false, // Require verification for all phones

    /*
    |--------------------------------------------------------------------------
    | SMS Provider Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS service provider.
    | Currently supports: twilio, aws_sns, custom
    |
    */
    'sms_provider' => env('SMS_PROVIDER', 'custom'),
    'sms_from' => env('SMS_FROM_NUMBER', 'DocRaTech'),
    
    // Twilio settings
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
    ],

    // AWS SNS settings
    'aws_sns' => [
        'region' => env('AWS_SNS_REGION', 'us-east-1'),
        'access_key' => env('AWS_SNS_ACCESS_KEY'),
        'secret_key' => env('AWS_SNS_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Formatting Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for phone number formatting and display.
    |
    */
    'auto_format' => true, // Auto-format phone numbers
    'display_format' => 'international', // international, national, e164
    'input_format' => 'national', // Default input format

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for phone number validation.
    |
    */
    'strict_validation' => false, // Strict validation mode
    'allow_landline' => true, // Allow landline numbers
    'mobile_preferred' => true, // Prefer mobile numbers
    'max_length' => 20, // Maximum phone number length
    'min_confidence_score' => 70, // Minimum confidence score for validation

    /*
    |--------------------------------------------------------------------------
    | Default Country Settings
    |--------------------------------------------------------------------------
    |
    | Default country code and settings.
    |
    */
    'default_country' => env('PHONE_DEFAULT_COUNTRY', 'TR'),
    'detect_country' => true, // Auto-detect country from IP
    'fallback_country' => 'US', // Fallback if detection fails

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for phone data caching.
    |
    */
    'cache_validation' => true, // Cache validation results
    'cache_ttl' => 3600, // Cache time-to-live in seconds
    'cache_prefix' => 'phone_', // Cache key prefix

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for phone validation and verification logging.
    |
    */
    'log_validation' => true, // Log validation attempts
    'log_verification' => true, // Log verification attempts
    'log_channel' => env('LOG_CHANNEL', 'stack'), // Log channel
]; 