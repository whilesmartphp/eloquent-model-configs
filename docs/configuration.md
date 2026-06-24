# Configuration

## Publish Config

```bash
php artisan vendor:publish --tag=model-configuration
```

Creates `config/model-configuration.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Register Routes
    |--------------------------------------------------------------------------
    |
    | Whether to automatically register the package routes.
    | Set to false if you want to manually define routes.
    |
    */
    'register_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for all package routes.
    | Default: 'api' (routes at /api/model-configurations)
    |
    */
    'route_prefix' => env('MODEL_CONFIG_ROUTE_PREFIX', 'api'),

    /*
    |--------------------------------------------------------------------------
    | Auth Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to protect the configuration endpoints.
    | Must be an array of middleware names.
    |
    */
    'auth_middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Allow Case-Insensitive Keys
    |--------------------------------------------------------------------------
    |
    | If true, config keys are case-insensitive.
    | 'ConfigName' and 'configname' are treated as the same key.
    |
    */
    'allow_case_insensitive_keys' => false,

    /*
    |--------------------------------------------------------------------------
    | Allowed Keys
    |--------------------------------------------------------------------------
    |
    | Restrict which config keys can be used.
    | Empty array = all keys allowed.
    |
    | Each entry can be:
    | - A string key name (no validation)
    | - Key => Laravel validation rules
    |
    */
    'allowed_keys' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Configuration Model
    |--------------------------------------------------------------------------
    |
    | Use a custom model extending the base Configuration model.
    | Enables adding traits like SoftDeletes, Auditable, etc.
    |
    */
    'model' => \Whilesmart\ModelConfiguration\Models\Configuration::class,

    /*
    |--------------------------------------------------------------------------
    | Hooks
    |--------------------------------------------------------------------------
    |
    | Register hooks to customize behavior. Each hook class can implement:
    | - ModelHookInterface: runs before/after HTTP requests
    | - ConfigValueHookInterface: runs when setConfigValue() is called
    |
    | A single hook class can implement both interfaces.
    |
    */
    'hooks' => [],
];
```

## Environment Variables

```env
# Optional: Change route prefix
MODEL_CONFIG_ROUTE_PREFIX=api
```

## Allowed Keys Examples

```php
// Allow specific keys only (no validation)
'allowed_keys' => ['timezone', 'theme', 'posts_per_page'],

// Allow with validation rules
'allowed_keys' => [
    'timezone' => 'string|in:UTC,America/New_York,Europe/London',
    'posts_per_page' => 'integer|min:1|max:100',
    'theme' => 'string|in:light,dark,auto',
    'simple_key', // No validation
],

// Allow all (default)
'allowed_keys' => [],
```

## Case-Insensitive Keys

When `'allow_case_insensitive_keys' => true`:
- Keys are stored as-is (case preserved)
- Lookups are case-insensitive
- `getConfig('TimeZone')` finds key `'timezone'`
- `getConfig('TIMEZONE')` finds key `'timezone'`