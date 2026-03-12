<?php

namespace App\Helpers;

/**
 * Data Masking Helper
 * 
 * Provides functions to mask sensitive data displayed in the frontend.
 * This helps protect personally identifiable information (PII) while
 * still allowing users to identify records.
 */
class DataMaskingHelper
{
    /**
     * Mask an email address.
     * Example: john.doe@example.com → j****e@e****e.com
     *
     * @param string|null $email
     * @return string|null
     */
    public static function maskEmail(?string $email): ?string
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        // Mask name part
        $maskedName = self::maskString($name, 1, 1);

        // Mask domain part (before the last dot)
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2) {
            $domainName = implode('.', array_slice($domainParts, 0, -1));
            $tld = end($domainParts);
            $maskedDomain = self::maskString($domainName, 1, 1) . '.' . $tld;
        } else {
            $maskedDomain = $domain;
        }

        return $maskedName . '@' . $maskedDomain;
    }

    /**
     * Mask a phone/contact number.
     * Example: 09171234567 → 0917****567
     *
     * @param string|null $phone
     * @return string|null
     */
    public static function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return $phone;
        }

        // Remove non-numeric characters for processing
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($cleanPhone);

        if ($length < 4) {
            return str_repeat('*', $length);
        }

        // Show first 4 and last 3 digits
        $visibleStart = min(4, floor($length / 2));
        $visibleEnd = min(3, ceil($length / 3));
        $maskLength = $length - $visibleStart - $visibleEnd;

        if ($maskLength <= 0) {
            return $cleanPhone;
        }

        return substr($cleanPhone, 0, $visibleStart) 
            . str_repeat('*', $maskLength) 
            . substr($cleanPhone, -$visibleEnd);
    }

    /**
     * Mask an IP address.
     * Example: 192.168.1.100 → 192.168.***.***
     *
     * @param string|null $ip
     * @return string|null
     */
    public static function maskIpAddress(?string $ip): ?string
    {
        if (empty($ip)) {
            return $ip;
        }

        // Handle IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return $parts[0] . '.' . $parts[1] . '.***.***';
        }

        // Handle IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            $visible = array_slice($parts, 0, 2);
            $masked = array_fill(0, count($parts) - 2, '****');
            return implode(':', array_merge($visible, $masked));
        }

        // Unknown format - mask middle portion
        return self::maskString($ip, 4, 4);
    }

    /**
     * Mask a name (partial masking).
     * Example: John Doe → J*** D**
     *
     * @param string|null $name
     * @return string|null
     */
    public static function maskName(?string $name): ?string
    {
        if (empty($name)) {
            return $name;
        }

        $words = explode(' ', trim($name));
        $maskedWords = [];

        foreach ($words as $word) {
            if (strlen($word) <= 1) {
                $maskedWords[] = $word;
            } else {
                $maskedWords[] = substr($word, 0, 1) . str_repeat('*', strlen($word) - 1);
            }
        }

        return implode(' ', $maskedWords);
    }

    /**
     * Mask an address.
     * Example: 123 Main Street, City → 1** M*** S*****, C***
     *
     * @param string|null $address
     * @return string|null
     */
    public static function maskAddress(?string $address): ?string
    {
        if (empty($address)) {
            return $address;
        }

        // Mask most characters but preserve structure
        return preg_replace_callback('/\b(\w)(\w+)\b/', function ($matches) {
            return $matches[1] . str_repeat('*', strlen($matches[2]));
        }, $address);
    }

    /**
     * Mask a credit card number.
     * Example: 4111111111111111 → ****-****-****-1111
     *
     * @param string|null $cardNumber
     * @return string|null
     */
    public static function maskCreditCard(?string $cardNumber): ?string
    {
        if (empty($cardNumber)) {
            return $cardNumber;
        }

        $clean = preg_replace('/[^0-9]/', '', $cardNumber);
        $length = strlen($clean);

        if ($length < 4) {
            return str_repeat('*', $length);
        }

        $lastFour = substr($clean, -4);
        return '****-****-****-' . $lastFour;
    }

    /**
     * Mask account/ID numbers.
     * Example: ACC-123456789 → ACC-*****6789
     *
     * @param string|null $accountNumber
     * @return string|null
     */
    public static function maskAccountNumber(?string $accountNumber): ?string
    {
        if (empty($accountNumber)) {
            return $accountNumber;
        }

        // Keep prefix and last 4 characters
        if (preg_match('/^([A-Z]+-?)(.+)$/', $accountNumber, $matches)) {
            $prefix = $matches[1];
            $number = $matches[2];
            
            if (strlen($number) <= 4) {
                return $accountNumber;
            }
            
            return $prefix . str_repeat('*', strlen($number) - 4) . substr($number, -4);
        }

        // No prefix format
        if (strlen($accountNumber) <= 4) {
            return $accountNumber;
        }

        return str_repeat('*', strlen($accountNumber) - 4) . substr($accountNumber, -4);
    }

    /**
     * Generic string masking with configurable visible characters.
     *
     * @param string $string The string to mask
     * @param int $visibleStart Number of visible characters at start
     * @param int $visibleEnd Number of visible characters at end
     * @param string $maskChar Character to use for masking
     * @return string
     */
    public static function maskString(
        string $string, 
        int $visibleStart = 1, 
        int $visibleEnd = 1, 
        string $maskChar = '*'
    ): string {
        $length = strlen($string);
        
        if ($length <= ($visibleStart + $visibleEnd)) {
            return $string;
        }

        $maskLength = $length - $visibleStart - $visibleEnd;
        
        return substr($string, 0, $visibleStart) 
            . str_repeat($maskChar, $maskLength) 
            . substr($string, -$visibleEnd);
    }

    /**
     * Create a maskable wrapper for data that can be toggled.
     * Returns HTML with both masked and unmasked versions.
     *
     * @param string|null $data The data to wrap
     * @param string $type The type of data (email, phone, ip, name, etc.)
     * @param bool $initiallyMasked Whether to show masked by default
     * @return string HTML string with toggle capability
     */
    public static function maskableField(
        ?string $data, 
        string $type = 'string',
        bool $initiallyMasked = true
    ): string {
        if (empty($data)) {
            return '<span class="text-muted">-</span>';
        }

        // Get masked version based on type
        $maskedData = match ($type) {
            'email' => self::maskEmail($data),
            'phone' => self::maskPhone($data),
            'ip' => self::maskIpAddress($data),
            'name' => self::maskName($data),
            'address' => self::maskAddress($data),
            'card' => self::maskCreditCard($data),
            'account' => self::maskAccountNumber($data),
            default => self::maskString($data, 2, 2),
        };

        $displayClass = $initiallyMasked ? '' : 'd-none';
        $hiddenClass = $initiallyMasked ? 'd-none' : '';
        $escapedData = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        $escapedMasked = htmlspecialchars($maskedData, ENT_QUOTES, 'UTF-8');
        $eyeIcon = $initiallyMasked ? 'fa-eye' : 'fa-eye-slash';
        $maskedAttr = $initiallyMasked ? 'true' : 'false';

        return <<<HTML
<span class="maskable-field" data-masked="$maskedAttr">
    <span class="masked-value $displayClass">$escapedMasked</span>
    <span class="unmasked-value $hiddenClass">$escapedData</span>
    <button type="button" class="btn btn-link btn-sm p-0 ms-1 toggle-mask" onclick="toggleMask(this)" title="Toggle visibility">
        <i class="fas $eyeIcon text-secondary"></i>
    </button>
</span>
HTML;
    }
}
