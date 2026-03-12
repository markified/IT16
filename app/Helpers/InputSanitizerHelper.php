<?php

namespace App\Helpers;

class InputSanitizerHelper
{
    /**
     * Sanitize a string input - removes HTML tags and trims whitespace.
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizeString(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Trim whitespace
        $input = trim($input);

        // Remove HTML tags
        $input = strip_tags($input);

        // Convert special characters to HTML entities to prevent XSS
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    /**
     * Sanitize email input.
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizeEmail(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        $input = strtolower($input);
        
        // Use filter_var to sanitize email
        $sanitized = filter_var($input, FILTER_SANITIZE_EMAIL);
        
        return $sanitized ?: null;
    }

    /**
     * Sanitize integer input.
     *
     * @param mixed $input
     * @return int|null
     */
    public static function sanitizeInteger($input): ?int
    {
        if ($input === null || $input === '') {
            return null;
        }

        return filter_var($input, FILTER_SANITIZE_NUMBER_INT) !== false 
            ? (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT) 
            : null;
    }

    /**
     * Sanitize numeric/decimal input.
     *
     * @param mixed $input
     * @return float|null
     */
    public static function sanitizeNumeric($input): ?float
    {
        if ($input === null || $input === '') {
            return null;
        }

        // Remove everything except digits, dots, and minus sign
        $sanitized = preg_replace('/[^0-9.\-]/', '', (string) $input);
        
        return is_numeric($sanitized) ? (float) $sanitized : null;
    }

    /**
     * Sanitize phone number input.
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizePhone(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        
        // Remove everything except digits, plus, hyphens, parentheses, and spaces
        $sanitized = preg_replace('/[^0-9+\-() ]/', '', $input);
        
        return $sanitized ?: null;
    }

    /**
     * Sanitize text area input (allows newlines but removes HTML).
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizeTextArea(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Trim whitespace
        $input = trim($input);

        // Remove HTML tags but preserve newlines
        $input = strip_tags($input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    /**
     * Sanitize date input.
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizeDate(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        
        // Validate date format (basic check)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
            return $input;
        }

        // Try to parse and format the date
        $timestamp = strtotime($input);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Sanitize reference number input (numbers only).
     *
     * @param string|null $input
     * @return string|null
     */
    public static function sanitizeReferenceNumber(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        
        // Remove everything except digits
        $sanitized = preg_replace('/[^0-9]/', '', $input);
        
        return $sanitized ?: null;
    }

    /**
     * Sanitize an array of inputs.
     *
     * @param array $inputs
     * @param array $rules Array with field names as keys and sanitization type as value
     *                     Types: 'string', 'email', 'integer', 'numeric', 'phone', 'textarea', 'date'
     * @return array
     */
    public static function sanitizeArray(array $inputs, array $rules): array
    {
        $sanitized = [];

        foreach ($rules as $field => $type) {
            if (!array_key_exists($field, $inputs)) {
                continue;
            }

            $value = $inputs[$field];

            switch ($type) {
                case 'string':
                    $sanitized[$field] = self::sanitizeString($value);
                    break;
                case 'email':
                    $sanitized[$field] = self::sanitizeEmail($value);
                    break;
                case 'integer':
                    $sanitized[$field] = self::sanitizeInteger($value);
                    break;
                case 'numeric':
                    $sanitized[$field] = self::sanitizeNumeric($value);
                    break;
                case 'phone':
                    $sanitized[$field] = self::sanitizePhone($value);
                    break;
                case 'textarea':
                    $sanitized[$field] = self::sanitizeTextArea($value);
                    break;
                case 'date':
                    $sanitized[$field] = self::sanitizeDate($value);
                    break;
                case 'reference_number':
                    $sanitized[$field] = self::sanitizeReferenceNumber($value);
                    break;
                default:
                    $sanitized[$field] = self::sanitizeString($value);
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize all request inputs with specified rules.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @return array
     */
    public static function sanitizeRequest(\Illuminate\Http\Request $request, array $rules): array
    {
        return self::sanitizeArray($request->all(), $rules);
    }
}
