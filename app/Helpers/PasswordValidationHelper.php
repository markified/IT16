<?php

namespace App\Helpers;

use App\Models\SecuritySetting;
use Illuminate\Validation\Rules\Password;

class PasswordValidationHelper
{
    /**
     * Build password validation rules based on security settings.
     *
     * @return \Illuminate\Validation\Rules\Password
     */
    public static function getPasswordRules(): Password
    {
        $minLength = (int) SecuritySetting::get('password_min_length', 8);
        $requireUppercase = (bool) SecuritySetting::get('password_require_uppercase', true);
        $requireLowercase = (bool) SecuritySetting::get('password_require_lowercase', true);
        $requireNumbers = (bool) SecuritySetting::get('password_require_numbers', true);
        $requireSymbols = (bool) SecuritySetting::get('password_require_symbols', false);

        $password = Password::min($minLength);

        if ($requireUppercase || $requireLowercase) {
            if ($requireUppercase && $requireLowercase) {
                $password = $password->mixedCase();
            } elseif ($requireUppercase) {
                $password = $password->rules(['regex:/[A-Z]/']);
            } elseif ($requireLowercase) {
                $password = $password->letters();
            }
        }

        if ($requireNumbers) {
            $password = $password->numbers();
        }

        if ($requireSymbols) {
            $password = $password->symbols();
        }

        return $password;
    }

    /**
     * Get password requirement messages for display.
     *
     * @return array
     */
    public static function getPasswordRequirements(): array
    {
        $requirements = [];

        $minLength = (int) SecuritySetting::get('password_min_length', 8);
        $requirements[] = "At least {$minLength} characters";

        if (SecuritySetting::get('password_require_uppercase', true)) {
            $requirements[] = 'At least one uppercase letter (A-Z)';
        }

        if (SecuritySetting::get('password_require_lowercase', true)) {
            $requirements[] = 'At least one lowercase letter (a-z)';
        }

        if (SecuritySetting::get('password_require_numbers', true)) {
            $requirements[] = 'At least one number (0-9)';
        }

        if (SecuritySetting::get('password_require_symbols', false)) {
            $requirements[] = 'At least one special character (!@#$%^&*)';
        }

        return $requirements;
    }
}
