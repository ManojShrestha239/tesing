<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;

class CheckSubscriptionMiddleware
{
    /**
     * Configuration constants
     */
    private const CACHE_ACTIVE_TTL = 8; // hours
    private const CACHE_EXPIRED_TTL = 15; // minutes
    private const API_TIMEOUT = 3; // seconds
    private const CACHE_KEY_PREFIX = 'subscription_status_';

    /**
     * Default configuration
     */

    // for live
    private const DEFAULT_CONFIG = [
        'api_secret' => '5SFRSrbWB2VrUg81ES2R4IlYBVGu2TUo3RYjXLg7',
        'domain' => 'https://demo.fieldma.com/',
        'subscription_url' => 'https://tintsubs.bihanitech.com/',
        'redirect_url' => 'https://bihanitech.com/subscription-expired/',
    ];

    // //for local
    // private const DEFAULT_CONFIG = [
    //     'api_secret' => 'B6hLgZXTzX6V0ojoEDmMSEteVxkoRXvvz3eDWi2l',
    //     'domain' => 'http://eurofieldma.manojshrestha239.com.np',
    //     'subscription_url' => 'http://bihani-api/',
    //     'redirect_url' => 'https://bihanitech.com/subscription-expired/',
    // ];

    /**
     * Security headers
     */
    private const SECURITY_HEADERS = [
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Early return for development/local environments
        if ($this->shouldBypassSubscriptionCheck($request)) {
            return $this->allowRequest($request, $next);
        }

        try {
            // Validate HTTPS in production
            // $this->validateSecureConnection($request);

            // Get configuration
            $config = $this->getConfiguration();
            $this->validateConfiguration($config);

            // Validate domain
            $this->isValidDomain($config['domain']);

            // --- Read current version from pointer cache ---
            $pointerKey     = $this->generateVersionPointerKey($config['domain']);
            $currentVersion = Cache::get($pointerKey);

            // --- Versioned status lookup ---
            $cacheKey   = $this->generateCacheKey($config['domain'], $currentVersion);
            $cachedData = $currentVersion ? Cache::get($cacheKey) : null;
            if ($this->isCacheValid($cachedData)) {
                // dd($this->isCacheValid($cachedData), $cachedData, $cacheKey, $pointerKey, $currentVersion);
                return $this->handleCachedResponse($request, $next, $cachedData, $config);
            }

            // --- Cache miss → fetch fresh data ---
            $subscriptionData = $this->fetchSubscriptionStatus($config);

            return $this->processSubscriptionResponse(
                $request,
                $next,
                $subscriptionData,
                $cacheKey,
                $config,
                $cachedData,
                $pointerKey
            );
        } catch (\Exception $e) {

            return $this->handleFallback($request, $next, $cachedData ?? null, $config ?? []);
        }
    }

    /**
     * Determine if subscription check should be bypassed
     *
     * @param Request $request
     * @return bool
     */
    private function shouldBypassSubscriptionCheck(Request $request): bool
    {
        // Check for local development environment
        if ($this->isLocalEnvironment($request)) {
            return true;
        }

        // Check for specific bypass routes
        if ($this->isBypassRoute($request)) {
            return true;
        }

        // Add other bypass conditions as needed
        if (strtolower(config('app.env')) === 'local') {
            return true;
        }

        return false;
    }

    /**
     * Check if request is from local environment
     *
     * @param Request $request
     * @return bool
     */
    private function isLocalEnvironment(Request $request): bool
    {
        $localIps = ['127.0.0.1', '::1', 'localhost'];

        return in_array($request->ip(), $localIps, true);
    }

    /**
     * Check if current route should bypass subscription check
     *
     * @param Request $request
     * @return bool
     */
    private function isBypassRoute(Request $request): bool
    {
        $bypassRoutes = [
            '/privacy_policy',
            '/terms-of-service',
        ];

        $currentPath = parse_url($request->url(), PHP_URL_PATH);

        foreach ($bypassRoutes as $route) {
            if (!empty($currentPath) && str_ends_with($currentPath, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate secure connection in production
     */
    private function validateSecureConnection(Request $request): void
    {
        if (!$request->secure() && Config::get('app.env') === 'production') {
            abort(403, 'Secure connection required');
        }
    }

    /**
     * Validate domain
     */
    private function isValidDomain($domain)
    {
        $currentDomain = parse_url(request()->getSchemeAndHttpHost(), PHP_URL_HOST);
        $domain = parse_url($domain, PHP_URL_HOST);

        if ($domain !== $currentDomain) {
            return abort(403, 'Access denied: Invalid domain');
        }
    }

    /**
     * Get middleware configuration
     */
    private function getConfiguration(): array
    {
        return [
            'api_secret' => Config::get('subscription.api_secret', self::DEFAULT_CONFIG['api_secret']),
            'domain' => Config::get('subscription.domain', self::DEFAULT_CONFIG['domain']),
            'subscription_url' => Config::get('subscription.url', self::DEFAULT_CONFIG['subscription_url']),
            'redirect_url' => Config::get('subscription.redirect_url', self::DEFAULT_CONFIG['redirect_url']),
        ];
    }

    /**
     * Validate configuration parameters
     */
    private function validateConfiguration(array $config): void
    {
        if (empty($config['api_secret']) || empty($config['domain'])) {
            throw new \InvalidArgumentException('Missing required configuration parameters');
        }
    }

    /**
     * Generate cache key for domain
     */
    // private function generateCacheKey(string $domain): string
    // {
    //     return self::CACHE_KEY_PREFIX . md5($domain);
    // }

    /**
     * Cache key for the full subscription payload, keyed by version.
     */
    private function generateCacheKey(string $domain, ?string $version = null): string
    {
        $base = self::CACHE_KEY_PREFIX . md5($domain);
        return ($version !== null && $version !== '')
            ? $base . '_v_' . md5($version)
            : $base;
    }

    /**
     * Check if cached data is valid
     */
    private function isCacheValid(?array $cachedData): bool
    {
        return $cachedData !== null
            && isset($cachedData['status'], $cachedData['expires_at'])
            && $this->createCarbonInstance($cachedData['expires_at'])->isFuture();
    }

    /**
     * Handle cached subscription response
     */
    private function handleCachedResponse(Request $request, Closure $next, array $cachedData, array $config)
    {
        $this->shareViewData($cachedData);

        if ($cachedData['status'] === 'active') {
            return $this->allowRequest($request, $next);
        }

        if ($cachedData['status'] === 'expired') {
            return $this->handleExpiredSubscription($request, $config);
        }

        return $this->createErrorResponse('Invalid subscription status', 500);
    }

    /**
     * Fetch subscription status from API
     */

    private function fetchSubscriptionStatus(array $config): array
    {
        $timestamp = Carbon::now()->timestamp;
        $nonce = $this->generateUuid();
        $signature = $this->generateSignature($timestamp, $config['domain'], $nonce, $config['api_secret']);

        $endpoint = rtrim($config['subscription_url'], '/') . '/api/check-subscription';

        $headers = [
            'X-API-Signature: ' . $signature,
            'X-API-Timestamp: ' . $timestamp,
            'X-API-Nonce: ' . $nonce,
            'X-API-Domain: ' . $config['domain'],
        ];

        $postData = http_build_query([
            'domain' => $config['domain'],
            'timestamp' => $timestamp,
        ]);

        return $this->executeApiRequest($endpoint, $headers, $postData);
    }

    /**
     * Generate HMAC signature
     */
    private function generateSignature(int $timestamp, string $domain, string $nonce, string $apiSecret): string
    {
        return hash_hmac('sha256', $timestamp . $domain . $nonce, $apiSecret);
    }

    /**
     * Execute API request using cURL
     */
    private function executeApiRequest(string $endpoint, array $headers, string $postData): array
    {
        $ch = curl_init($endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => self::API_TIMEOUT,
            CURLOPT_FAILONERROR => false,
            CURLOPT_SSL_VERIFYPEER => false, // Only for development
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        return [
            'body' => $response,
            'status' => $httpCode,
            'error' => $curlError,
        ];
    }

    /**
     * Process subscription API response
     */
    private function processSubscriptionResponse(
        Request $request,
        Closure $next,
        array $apiResponse,
        string $cacheKey,          // kept for fallback but recomputed below
        array $config,
        ?array $cachedData,
        ?string $pointerKey = null
    ) {
        if (!empty($apiResponse['error']) || $apiResponse['status'] !== 200) {
            return $this->handleFallback($request, $next, $cachedData, $config);
        }

        $responseData = json_decode($apiResponse['body'] ?? '{}', true);

        if (!is_array($responseData)) {
            return $this->handleFallback($request, $next, $cachedData, $config);
        }

        $this->shareViewData($responseData);

        $subscriptionStatus = $responseData['status'] ?? 'unknown';
        $newVersion         = $responseData['version'] ?? null;

        // ---- Version changed? Store under a fresh key + refresh pointer ----
        if ($newVersion && $pointerKey) {
            Cache::put(
                $pointerKey,
                $newVersion,
                Carbon::now()->addHours(self::CACHE_ACTIVE_TTL)
            );
            $cacheKey = $this->generateCacheKey($config['domain'], $newVersion);
        }

        switch ($subscriptionStatus) {
            case 'active':
                $this->cacheActiveSubscription($cacheKey, $responseData);
                return $this->allowRequest($request, $next);

            case 'expired':
                $this->cacheExpiredSubscription($cacheKey);
                return $this->handleExpiredSubscription($request, $config);

            default:
                return $this->createErrorResponse('Invalid subscription response', 503);
        }
    }

    /**
     * Handle expired subscription
     */
    private function handleExpiredSubscription(Request $request, array $config)
    {
        if ($this->isApiRequest($request, $config)) {
            return $this->createJsonErrorResponse(
                'Your subscription has expired. Please renew to continue accessing this feature.',
                'SUBSCRIPTION_EXPIRED',
                401
            );
        }

        return redirect()->to($config['redirect_url']);
    }

    /**
     * Check if request is an API request
     */
    private function isApiRequest(Request $request, array $config): bool
    {
        $apiPath = rtrim($config['domain'], '/') . '/api';
        return strpos($request->url(), $apiPath) !== false;
    }

    /**
     * Allow request to proceed
     */
    private function allowRequest(Request $request, Closure $next)
    {
        $response = $next($request);

        foreach (self::SECURITY_HEADERS as $key => $value) {
            if (method_exists($response, 'header')) {
                $response->header($key, $value);
            } elseif (isset($response->headers)) {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }

    /**
     * Handle fallback verification
     */
    private function handleFallback(Request $request, Closure $next, ?array $cachedData, array $config)
    {
        if ($this->isCacheValid($cachedData)) {
            return $this->handleCachedResponse($request, $next, $cachedData, $config);
        }

        return $this->createErrorResponse('Service temporarily unavailable', 503);
    }

    /**
     * Cache active subscription
     */
    /**
     * Cache active subscription
     */
    private function cacheActiveSubscription(string $cacheKey, array $responseData): void
    {
        $expiresAt = Carbon::now()->addHours(self::CACHE_ACTIVE_TTL);

        $cacheData = [
            'version'          => $responseData['version'] ?? null,   // ← new
            'GTMID'            => $responseData['gtmid'] ?? null,
            'enableGTM'        => $responseData['enableGTM'] ?? false,
            'expireDate'       => $responseData['expiry_date'] ?? null,
            'url'              => $responseData['redirect_url'] ?? null,
            'web_features'     => $responseData['web_features'] ?? [],
            'mobile_features'  => $responseData['mobile_features'] ?? [],
            'max_mobile_users' => $responseData['max_mobile_users'] ?? 0,
            'status'           => 'active',
            'expires_at'       => $expiresAt->toDateTimeString(),
        ];

        Cache::put($cacheKey, $cacheData, $expiresAt);
    }

    /**
     * Cache expired subscription
     */
    private function cacheExpiredSubscription(string $cacheKey): void
    {
        $expiresAt = Carbon::now()->addMinutes(self::CACHE_EXPIRED_TTL);

        $cacheData = [
            'status' => 'expired',
            'expires_at' => $expiresAt->toDateTimeString(),
        ];

        Cache::put($cacheKey, $cacheData, $expiresAt);
    }

    /**
     * Share data with views
     */
    /**
     * Share data with views and request attributes
     */
    private function shareViewData(array $data): void
    {
        // Get permissions (handles both API 'permissions' key and Cache 'package' key)
        $webFeatures = $data['web_features'] ?? $data['web_features'] ?? [];
        $mobileFeatures = $data['mobile_features'] ?? [];

        // ---- Sync to DB (idempotent) ----
        $this->syncPermissions($webFeatures);

        // Get max mobile users count
        $maxMobileUsers = $data['max_mobile_users'] ?? 0;

        // Extract common fields with fallbacks for API vs Cache key names
        $gtmId      = $data['GTMID'] ?? $data['gtmid'] ?? null;
        $enableGTM  = $data['enableGTM'] ?? false;
        $expireDate = $data['expireDate'] ?? $data['expiry_date'] ?? null;
        $url        = $data['url'] ?? $data['redirect_url'] ?? null;

        $sharedData = [
            'GTMID'            => $gtmId,
            'enableGTM'        => $enableGTM,
            'expireDate'       => $expireDate,
            'web_features'     => $webFeatures,
            'mobile_features'  => $mobileFeatures,
            'max_mobile_users' => $maxMobileUsers,
            'url'              => $url,
        ];

        // 1. Share with Blade views (.blade.php)
        if (function_exists('view')) {
            view()->share($sharedData);
        }

        // 2. Attach to Request object for api.php & controllers
        request()->attributes->set('appData', $sharedData);
    }

    /**
     * Generate UUID v4
     */
    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Create Carbon instance (Laravel version compatible)
     */
    private function createCarbonInstance(string $dateTime): Carbon
    {
        return class_exists('\Carbon\Carbon') ? Carbon::parse($dateTime) : new Carbon($dateTime);
    }

    /**
     * Create error response
     */
    private function createErrorResponse(string $message, int $statusCode)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $statusCode)->withHeaders(self::SECURITY_HEADERS);
    }

    /**
     * Create JSON error response for API requests
     */
    private function createJsonErrorResponse(string $message, string $errorCode, int $statusCode)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => $errorCode
        ], $statusCode)->withHeaders(self::SECURITY_HEADERS);
    }

    /**
     * Full sync of subscription permissions.
     *
     * - Adds any permission in the payload that doesn't exist yet.
     * - Removes any 'web'-guard permission NOT in the payload.
     * - All names normalized to lowercase so @can('notice') matches DB.
     * - Detaches removed permissions from all roles before deletion.
     * - Clears Spatie's permission cache so changes take effect immediately.
     */
    private function syncPermissions(array $permissionNames): void
    {
        $guard = 'web';

        // ---- 1. Normalize ONCE — lowercase, trim, drop empties, dedupe ----
        $permissionNames = array_values(array_unique(array_filter(array_map(
            fn($name) => is_string($name) ? strtolower(trim($name)) : null,
            $permissionNames
        ))));

        // Safety: if the payload was empty or malformed, do nothing.
        // Prevents wiping the entire permissions table on a bad API response.
        if (empty($permissionNames)) {
            return;
        }

        // ---- 2. Insert any missing permissions ----
        // Note: names are already lowercase here, so no strtolower needed.
        foreach ($permissionNames as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => $guard,
            ]);
        }

        // ---- 3. Delete permissions not in the payload (same normalized list) ----
        $toDelete = Permission::query()
            ->where('guard_name', $guard)
            ->whereNotIn('name', $permissionNames)   // ← both sides lowercase now
            ->get();

        // ---- 4. Detach from roles, then delete ----
        foreach ($toDelete as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }

        // ---- 5. Clear Spatie cache so @can() / hasPermissionTo() see the change ----
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Cache key for the "current version" pointer.
     */
    private function generateVersionPointerKey(string $domain): string
    {
        return self::CACHE_KEY_PREFIX . 'version_' . md5($domain);
    }
}
