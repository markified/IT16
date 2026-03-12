<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class IpHelper
{
    /**
     * Get the real client IP address.
     * 
     * This method checks for proxy headers to get the actual device IP address
     * instead of the local/proxy IP address.
     *
     * @param Request|null $request
     * @return string
     */
    public static function getClientIp(?Request $request = null): string
    {
        $request = $request ?? request();
        
        // List of headers that may contain the real IP address
        // Ordered by priority (most reliable first)
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_X_FORWARDED_FOR',      // Standard proxy header
            'HTTP_X_FORWARDED',          // Alternative forward header
            'HTTP_X_CLUSTER_CLIENT_IP',  // Load balancer
            'HTTP_FORWARDED_FOR',        // RFC 7239
            'HTTP_FORWARDED',            // RFC 7239
            'HTTP_CLIENT_IP',            // Some proxies
        ];

        foreach ($headers as $header) {
            $ip = $request->server($header);
            
            if (!empty($ip)) {
                // X-Forwarded-For can contain multiple IPs (client, proxy1, proxy2, ...)
                // The first IP is typically the original client
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                // Validate the IP address
                if (self::isValidIp($ip)) {
                    return $ip;
                }
            }
        }

        // Fallback to Laravel's ip() method which handles trusted proxies
        $fallbackIp = $request->ip();
        
        // If still getting localhost, try to get the actual network IP
        if (self::isLocalIp($fallbackIp)) {
            // Try to get the machine's actual network IP
            $networkIp = self::getNetworkIp();
            if ($networkIp) {
                return $networkIp;
            }
        }

        return $fallbackIp ?? '0.0.0.0';
    }

    /**
     * Get the actual network IP of the machine.
     * This is useful when accessing from localhost to show the real device IP.
     *
     * @return string|null
     */
    public static function getNetworkIp(): ?string
    {
        // Try to get IP from hostname
        $hostname = gethostname();
        if ($hostname) {
            $ip = gethostbyname($hostname);
            if ($ip !== $hostname && self::isValidIp($ip) && !self::isLocalIp($ip)) {
                return $ip;
            }
        }

        // Try using socket connection to determine outbound IP
        // This connects to a public DNS and checks what IP is used
        try {
            $socket = @fsockopen('udp://8.8.8.8', 53, $errno, $errstr, 2);
            if ($socket) {
                $socketName = stream_socket_get_name($socket, false);
                fclose($socket);
                if ($socketName) {
                    $ip = substr($socketName, 0, strpos($socketName, ':'));
                    if (self::isValidIp($ip)) {
                        return $ip;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail and continue
        }

        // Windows-specific: try ipconfig
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig');
            if ($output) {
                // Match IPv4 addresses
                if (preg_match_all('/IPv4.*?:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/i', $output, $matches)) {
                    foreach ($matches[1] as $ip) {
                        if (self::isValidIp($ip) && !self::isLocalIp($ip)) {
                            return $ip;
                        }
                    }
                }
            }
        } else {
            // Linux/Mac: try hostname -I or ifconfig
            $output = shell_exec('hostname -I 2>/dev/null || ifconfig 2>/dev/null | grep "inet " | grep -v 127.0.0.1 | awk \'{print $2}\'');
            if ($output) {
                $ips = preg_split('/\s+/', trim($output));
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (self::isValidIp($ip) && !self::isLocalIp($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if the IP address is valid.
     *
     * @param string $ip
     * @return bool
     */
    public static function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Check if the IP is a local/private address.
     *
     * @param string $ip
     * @return bool
     */
    public static function isLocalIp(?string $ip): bool
    {
        if (empty($ip)) {
            return true;
        }

        $localPatterns = [
            '127.0.0.1',
            '::1',
            'localhost',
        ];

        if (in_array($ip, $localPatterns)) {
            return true;
        }

        // Check for private IP ranges
        $privateRanges = [
            '10.',           // 10.0.0.0/8
            '172.16.',       // 172.16.0.0/12 to 172.31.0.0/12
            '172.17.',
            '172.18.',
            '172.19.',
            '172.20.',
            '172.21.',
            '172.22.',
            '172.23.',
            '172.24.',
            '172.25.',
            '172.26.',
            '172.27.',
            '172.28.',
            '172.29.',
            '172.30.',
            '172.31.',
            '192.168.',      // 192.168.0.0/16
        ];

        foreach ($privateRanges as $range) {
            if (strpos($ip, $range) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get IP address with location info (if available).
     *
     * @param string $ip
     * @return array
     */
    public static function getIpInfo(string $ip): array
    {
        return [
            'ip' => $ip,
            'is_local' => self::isLocalIp($ip),
            'is_valid' => self::isValidIp($ip),
            'type' => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'IPv4' : 'IPv6',
        ];
    }
}
