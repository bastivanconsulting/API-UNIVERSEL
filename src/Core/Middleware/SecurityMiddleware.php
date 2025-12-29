<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core\Middleware;

use Bastivan\UniversalApi\Core\Config;
use Bastivan\UniversalApi\Controllers\ErrorController;

/**
 * Class SecurityMiddleware
 * Developed by Bastivan Consulting
 *
 * Handles Security headers, HTTPS enforcement, and CDN IP resolution.
 */
class SecurityMiddleware
{
    public static function handle(): void
    {
        // 1. Force HTTPS
        // In most production environments with load balancers (AWS, K8s), logic might differ (X-Forwarded-Proto).
        // But the requirement says "SSL Certificate Mandatory, No HTTP".

        $isHttps = false;
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
            $isHttps = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $isHttps = true;
        }

        // If strict SSL is enforced and we are not on HTTPS, we should block or redirect.
        // For an API, blocking with 403 Forbidden or 426 Upgrade Required is often better than redirecting POSTs.
        if (Config::get('APP_ENV') === 'production' && !$isHttps) {
            $controller = new ErrorController();
            $controller->forbidden(['message' => 'HTTPS is required.']);
        }

        // 2. Block Legacy SSL/TLS (Best effort application level check)
        // Usually handled by Nginx/Ingress, but we can check SSL_PROTOCOL if exposed by server.
        if (isset($_SERVER['SSL_PROTOCOL'])) {
            $protocol = $_SERVER['SSL_PROTOCOL'];
            if (preg_match('/(SSLv3|TLSv1\.0|TLSv1\.1)/', $protocol)) {
                $controller = new ErrorController();
                $controller->forbidden(['message' => 'TLS 1.2+ is required.']);
            }
        }

        // 3. Resolve Real IP from CDNs (Cloudflare, Gcore, Nginx Proxy)
        $trustedProxies = Config::get('TRUSTED_PROXIES', '*'); // Should be specific IPs in real prod

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            // Cloudflare
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_GCORE_REMOTE_IP'])) { // Hypothetical Gcore header
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_GCORE_REMOTE_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Standard Proxy - Take the first IP in the list
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $_SERVER['REMOTE_ADDR'] = trim($ips[0]);
        }

        // 4. IP Access Control (Deny All / Whitelist)
        $strategy = Config::get('SECURITY_ACCESS_STRATEGY', 'ALLOW_ALL');

        if ($strategy === 'WHITELIST') {
            $allowedIpsRaw = Config::get('SECURITY_ALLOWED_IPS', '');
            $allowedIps = array_filter(array_map('trim', explode(',', $allowedIpsRaw)));
            $clientIp = $_SERVER['REMOTE_ADDR'];

            $isAllowed = false;

            // Simple exact match check
            if (in_array($clientIp, $allowedIps, true)) {
                $isAllowed = true;
            }

            // If we want to support CIDR later, we would add that logic here.

            if (!$isAllowed) {
                // If the list is empty and strategy is WHITELIST, we effectively deny everyone.
                // But usually localhost might be implicitly allowed or explicitly required in the list.
                // We adhere to strict "Deny all except custom IPs".

                $controller = new ErrorController();
                $controller->forbidden(['message' => 'Access denied by security policy.']);
                // forbidden() method likely exits the script
                exit;
            }
        }
    }

    /**
     * Checks security options for a specific route.
     *
     * @param array $securityOptions
     */
    public static function checkRouteSecurity(array $securityOptions): void
    {
        // IP Access Control
        if (isset($securityOptions['allowed_ips']) && is_array($securityOptions['allowed_ips'])) {
            $allowedIps = $securityOptions['allowed_ips'];
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            if (!in_array($clientIp, $allowedIps, true)) {
                http_response_code(403);
                // Using standard error controller handling via redirect or direct output would be better,
                // but for now, we follow the established pattern in this file.
                // However, we should try to render a nice error if possible, but keep it simple here.
                echo "403 Forbidden - Access Denied by Route Policy - Bastivan Consulting";
                exit;
            }
        }
    }
}
