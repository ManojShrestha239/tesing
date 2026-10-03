# Dynamic Features — Feature Guide

**Feature name:** Dynamic Features  
**Status:** In Review  
**Author:** Manoj Shrestha  
**Commits covered:**

- `70c78dc` — feat: add package permissions retrieval and integrate into user authentication and home responses (Sep 30, 2026)
- `e71e3b8` — feat: update package permissions handling and enhance Docker networking configuration (Oct 1, 2026)

---

## 1. What kind of feature is it?

**Dynamic Features** is a subscription-driven, remote-controlled permission system.

The app no longer ships with a fixed list of features. Instead, a **central subscription API** (Bihani) returns the list of features the current domain/package is allowed to use. The Laravel app then:

- Stores those features in the Spatie permissions table at runtime.
- Shares them with Blade views (for menus and GTM).
- Returns them to mobile apps (`v3` API) so the app can show/hide screens dynamically.
- Caches them so the app keeps working if the subscription API goes down.

It includes these types of features:

- **Remote feature (permission) control**
    - Features come from the subscription API as `web_features` and `mobile_features`.
    - `web_features` are synced into Spatie `permissions` (guard `web`) automatically.
    - Permissions not in the payload are removed (with role detach) so the app always matches the paid package.
    - All permission names are normalized to **lowercase + trimmed** so `@can('notice')` matches reliably.

- **Web (Blade) integration**
    - Views get shared data: `GTMID`, `enableGTM`, `expireDate`, `web_features`, `mobile_features`, `max_mobile_users`, `url`.
    - Sidebar navigation picks the first allowed route using `$authUser->can($permission)`.
    - `AuthServiceProvider` overrides `Gate::before` so a Super-Admin only passes `can()` checks for permissions that **actually exist** in the DB.

- **Mobile API integration**
    - `POST` auth endpoint on `v3` returns a new `package_permissions` key.
    - `home` endpoint on `v1`/`v2` returns `package_permissions` too.
    - Payload contains `mobile_features` (feature list) and `max_mobile_users`.

- **Versioned subscription caching**
    - Cache key = `subscription_status_<md5(domain)>_v_<md5(version)>`.
    - A separate **version pointer** cache key tracks the latest version.
    - When the API returns a new version, the pointer updates and a new payload key is written.
    - Active cache TTL: **8 hours**. Expired cache TTL: **15 minutes**.

- **Docker networking (dev environment)**
    - App and web containers join an external shared network so this project can reach the subscription API container by alias `fieldma-api`.
    - Ports remapped to avoid clashes: MySQL `3307:3306`, phpMyAdmin `8082:80`.

---

## 2. Out of Scope

These are **not** part of this feature:

- No UI for managing features/permissions in this project. The subscription API is the source of truth.
- No multi-guard support. Only the `web` guard is synced.
- No per-user feature overrides. Features are per-domain/package.
- No history or audit log of permission changes.
- No offline edit queue. If the API fails, the last cached payload is used.
- No changes to mobile app code in this repo — only the API responses.
- No production Docker changes. `docker-compose.yml` changes are for local/shared dev networking only.

---

## 3. Full Guide to Port This Feature to Another Project

This guide assumes you are copying **Dynamic Features** from the original project into a **different Laravel project**.

You **cannot** use `git cherry-pick` because the codebases are separate. You will manually copy files, merge changes, and adapt as needed.

---

### Step 1: Gather the source files

**Created files:**

None. This feature only modifies existing files.

**Updated files:**

```
app/Http/Controllers/API/MobileuserController.php
app/Http/Controllers/API/v3/MobileuserController.php
app/Http/Middleware/CheckSubscriptionMiddleware.php
app/Providers/AuthServiceProvider.php
database/seeders/PermissionSeeder.php
resources/views/dashboards/partials/nav.blade.php
routes/v3/api.php
docker-compose.yml
```

**Deleted files:**

None.

**Migrations:**

None. This feature reuses the existing Spatie permission tables.

---

### Step 2: Check prerequisites

Before copying anything, confirm the target project has:

1. **Laravel** (same major version as the source project).
2. **Spatie Laravel Permission** installed:
    ```bash
    composer require spatie/laravel-permission
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    php artisan migrate
    ```
3. A `config/permission_redirects.php` file that maps permission names to routes (used by `nav.blade.php`). If it does not exist, create it:
    ```php
    <?php
    return [
        'dashboard' => '/dashboard',
        'notice'    => '/notices',
        // ... one entry per feature -> route path
    ];
    ```
4. The existing `CheckSubscriptionMiddleware` (this feature builds on top of it). If the target project does not have it, port that middleware first.

---

### Step 3: Update `CheckSubscriptionMiddleware.php`

This is the biggest change. Copy the final version of the file from the source project, then verify the following pieces exist.

1. **Imports** — make sure this is present at the top:

    ```php
    use Spatie\Permission\Models\Permission;
    ```

2. **Constants** — confirm these exist:

    ```php
    private const CACHE_ACTIVE_TTL = 8;      // hours
    private const CACHE_EXPIRED_TTL = 15;    // minutes
    private const API_TIMEOUT = 3;           // seconds
    private const CACHE_KEY_PREFIX = 'subscription_status_';
    ```

    > Note: `CACHE_VERSION_TTL` was added and then removed across the two commits. It is **not** in the final version — do not add it.

3. **`handle()` method** — must use the version pointer:

    ```php
    $pointerKey     = $this->generateVersionPointerKey($config['domain']);
    $currentVersion = Cache::get($pointerKey);

    $cacheKey   = $this->generateCacheKey($config['domain'], $currentVersion);
    $cachedData = $currentVersion ? Cache::get($cacheKey) : null;

    if ($this->isCacheValid($cachedData)) {
        return $this->handleCachedResponse($request, $next, $cachedData, $config);
    }

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
    ```

4. **`generateCacheKey()`** — two-argument version:

    ```php
    private function generateCacheKey(string $domain, ?string $version = null): string
    {
        $base = self::CACHE_KEY_PREFIX . md5($domain);
        return ($version !== null && $version !== '')
            ? $base . '_v_' . md5($version)
            : $base;
    }
    ```

5. **New method — `generateVersionPointerKey()`**:

    ```php
    private function generateVersionPointerKey(string $domain): string
    {
        return self::CACHE_KEY_PREFIX . 'version_' . md5($domain);
    }
    ```

6. **`processSubscriptionResponse()`** — new signature and version handling:

    ```php
    private function processSubscriptionResponse(
        Request $request,
        Closure $next,
        array $apiResponse,
        string $cacheKey,
        array $config,
        ?array $cachedData,
        ?string $pointerKey = null
    ) {
        // ... error/decode handling ...

        $this->shareViewData($responseData);

        $subscriptionStatus = $responseData['status'] ?? 'unknown';
        $newVersion         = $responseData['version'] ?? null;

        if ($newVersion && $pointerKey) {
            Cache::put(
                $pointerKey,
                $newVersion,
                Carbon::now()->addHours(self::CACHE_ACTIVE_TTL)
            );
            $cacheKey = $this->generateCacheKey($config['domain'], $newVersion);
        }
        // ... switch on status ...
    }
    ```

7. **`cacheActiveSubscription()`** — store the new keys and use **hours**:

    ```php
    $expiresAt = Carbon::now()->addHours(self::CACHE_ACTIVE_TTL);

    $cacheData = [
        'version'          => $responseData['version'] ?? null,
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
    ```

8. **`shareViewData()`** — sync web features and expose request attribute:

    ```php
    private function shareViewData(array $data): void
    {
        $webFeatures    = $data['web_features'] ?? [];
        $mobileFeatures = $data['mobile_features'] ?? [];

        $this->syncPermissions($webFeatures);

        $maxMobileUsers = $data['max_mobile_users'] ?? 0;

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

        if (function_exists('view')) {
            view()->share($sharedData);
        }

        // Makes the data available via $request->attributes->get('appData')
        request()->attributes->set('appData', $sharedData);
    }
    ```

9. **New method — `syncPermissions()`**:

    ```php
    private function syncPermissions(array $permissionNames): void
    {
        $guard = 'web';

        $permissionNames = array_values(array_unique(array_filter(array_map(
            fn($name) => is_string($name) ? strtolower(trim($name)) : null,
            $permissionNames
        ))));

        // Safety: never wipe all permissions on an empty payload
        if (empty($permissionNames)) {
            return;
        }

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => $guard,
            ]);
        }

        $toDelete = Permission::query()
            ->where('guard_name', $guard)
            ->whereNotIn('name', $permissionNames)
            ->get();

        foreach ($toDelete as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    ```

10. **Expired subscription response** — HTTP code changed to `401`:
    ```php
    return $this->createJsonErrorResponse(
        'Your subscription has expired. Please renew to continue accessing this feature.',
        'SUBSCRIPTION_EXPIRED',
        401
    );
    ```

---

### Step 4: Update `AuthServiceProvider.php`

Replace the `Gate::before` block with this version. It prevents Super-Admins from bypassing `can()` checks for permissions that do not exist:

```php
use Spatie\Permission\Models\Permission;

// ...

Gate::before(function ($user, $ability) {
    if (! $user->hasRole('Super-Admin')) {
        return null;
    }

    static $permissions = null;

    if ($permissions === null) {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->map(fn($n) => strtolower(trim($n)))
            ->flip()
            ->all();
    }

    return array_key_exists(strtolower($ability), $permissions)
        ? true
        : null;
});
```

---

### Step 5: Update `PermissionSeeder.php`

Force lowercase names so seeded permissions match runtime-synced names:

```php
Permission::firstOrCreate(
    ['name' => strtolower($permission)],
    ['guard_name' => 'web']
);
```

If the target project already has uppercase permissions in the DB, run the seeder again after deploying, or write a one-off migration to lowercase existing rows.

---

### Step 6: Update `nav.blade.php`

Change `hasPermissionTo()` to `can()` (the former throws if the permission doesn't exist):

```php
@php
    $authUser = auth()->user();
    $permissionRoutes = config('permission_redirects');
    $routePath = '/dashboard';

    foreach ($permissionRoutes as $permission => $route) {
        if ($authUser->can($permission)) {
            $routePath = $route;
            break;
        }
    }
@endphp
```

---

### Step 7: Update the v3 `MobileuserController.php`

1. Add a private method:

    ```php
    private function packagePermissions(Request $request)
    {
        $appData = $request->attributes->get('appData');

        return $mobileFeatures = $appData['mobile_features'] ?? null;
    }
    ```

2. Call it in `authenticate()`:

    ```php
    $packagePermissions = $this->packagePermissions($request);
    ```

3. Pass it into the `$makeResponse` closure, and add it to the JSON payload:

    ```php
    'package_permissions' => $packagePermissions,
    ```

4. In the failure branches that call `$makeResponse(...)` with `null`s, add the extra `null` argument so the argument count matches.

---

### Step 8: Update the v1/v2 `MobileuserController.php`

1. In `home()`, add:

    ```php
    $data['package_permissions'] = $this->packagePermissions(request());
    ```

2. Add the same private method as in Step 7.

---

### Step 9: Clean up `routes/v3/api.php`

Remove the unused import:

```php
use Illuminate\Http\Request;   // <-- delete this line
```

---

### Step 10: Update `docker-compose.yml` (dev only)

Do not copy this verbatim into production. Only apply if you also run the subscription API in a shared Docker network.

1. Add the `shared` network to the `app` service:

    ```yaml
    networks:
        - fieldma_2_net
        - shared
    ```

2. Update the web service:

    ```yaml
    networks:
        fieldma_2_net: {}
        shared:
            aliases:
                - fieldma-api
    ```

3. Remap MySQL and phpMyAdmin ports to avoid collisions:

    ```yaml
    mysql:
        ports:
            - "3307:3306"

    phpmyadmin:
        ports:
            - "8082:80"
    ```

4. Declare the external network at the bottom:

    ```yaml
    networks:
        fieldma_2_net:
            driver: bridge
        shared:
            external: true
            name: ms_connect_to_bihani_subscription_system
    ```

5. Create the network on the host once:
    ```bash
    docker network create ms_connect_to_bihani_subscription_system
    ```

---

### Step 11: Adapter checklist after copying

Change these items in the target project:

1. **`DEFAULT_CONFIG`** in `CheckSubscriptionMiddleware` — set `api_secret`, `domain`, `subscription_url`, and `redirect_url` for the target environment. The source file has both a **live** and a **commented-out local** block — pick one and delete the other before committing.
2. **`config('permission_redirects')`** — confirm it maps every permission the subscription API returns to a real route.
3. **Spatie guard name** — the feature hardcodes `'web'`. If the target project uses a different default guard, update both `syncPermissions()` and the `Gate::before` closure.
4. **API field names** — the middleware expects `web_features`, `mobile_features`, `max_mobile_users`, and `version` from the subscription API. If the target API uses different keys, adapt `cacheActiveSubscription()` and `shareViewData()`.

---

### Step 12: Verify it works

Run these manual tests:

1. **Clear caches:**
    ```bash
    php artisan cache:clear
    php artisan permission:cache-reset
    ```
2. **Hit the v3 auth endpoint.** The JSON response should include:
    ```json
    "package_permissions": { "mobile_features": [...], "max_mobile_users": 10 }
    ```
3. **Hit the v1/v2 `home` endpoint.** The JSON should include `package_permissions` with the same shape.
4. **Check the DB.** Only the permissions returned by the API should exist for guard `web`:
    ```bash
    php artisan tinker
    >>> \Spatie\Permission\Models\Permission::where('guard_name','web')->pluck('name');
    ```
5. **Check the cache keys.** With Redis:
    ```bash
    redis-cli --scan --pattern 'subscription_status_*'
    ```
    You should see a `...version_<hash>` pointer key plus `..._v_<hash>` payload keys.
6. **Open the dashboard.** The sidebar logo link should point to the first permitted route.
7. **Test expiry.** Force the API to return `status: expired`; the JSON error should use HTTP `401` with code `SUBSCRIPTION_EXPIRED`.
8. **Test API outage.** Stop the subscription API. The cached payload should still be served for up to 8 hours.

---

### Step 13: Known issues to fix while porting

These are real problems in the current diff. Fix them in the target project (and consider fixing them in the source too):

1. **Dead code in `packagePermissions()`** — `return $mobileFeatures = $appData['mobile_features'] ?? null;` assigns and returns in one line. Change to:
    ```php
    return $appData['mobile_features'] ?? null;
    ```
2. **Duplicated key in `shareViewData()`** — `$data['web_features'] ?? $data['web_features']` is a copy-paste mistake. Replace the second one with `$data['permissions']` if you need API backward-compatibility, or delete it.
3. **`max_mobile_users` lost in mobile payload** — commit 2 dropped the `max_mobile_users` return from `packagePermissions()`. If the mobile app still needs it, return an array:
    ```php
    return [
        'mobile_features'  => $appData['mobile_features'] ?? [],
        'max_mobile_users' => $appData['max_mobile_users'] ?? 0,
    ];
    ```
4. **TTL units were changed twice** — commit 1 changed `addHours` to `addMinutes` (a bug: 8 minutes instead of 8 hours), commit 2 changed it back. The final version uses `addHours`. Do not copy the intermediate state.
5. **Commented-out code left in** — `packagePermissions()` and `DEFAULT_CONFIG` both have commented blocks. Clean these up before merging.
6. **Docker `depends_on` missing `shared`** — the `web` service depends on `app` but the shared network must exist on the host before `docker compose up`. Document this in your README.

---

### Step 14: Rollback plan

If the feature breaks production:

1. Revert the middleware to the pre-feature version — it reads from the old non-versioned cache key.
2. Clear the version pointer keys:
    ```bash
    php artisan cache:clear
    ```
3. Restore the previous `Gate::before` in `AuthServiceProvider`.
4. Restore the previous `PermissionSeeder` (or leave it — lowercase names do not break anything).
5. No DB migration is needed. Permissions synced at runtime are safe to leave; the next sync will overwrite them.
