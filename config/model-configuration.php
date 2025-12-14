<?php

return [
    'register_routes' => true,
    'route_prefix' => env('MODEL_CONFIG_ROUTE_PREFIX', 'api'),
    'auth_middleware' => [],
    'allow_case_insensitive_keys' => false,
    'allowed_keys' => [],
    'model' => \Whilesmart\ModelConfiguration\Models\Configuration::class,

    /*
    |--------------------------------------------------------------------------
    | Hooks
    |--------------------------------------------------------------------------
    |
    | Register hooks to customize behavior. Each hook class can implement:
    | - ModelHookInterface: runs before/after HTTP requests to config endpoints
    | - ConfigValueHookInterface: runs when setConfigValue() is called
    |
    | A single hook class can implement both interfaces.
    |
    */
    'hooks' => [],
];
