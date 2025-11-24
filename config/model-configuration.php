<?php

return [
    'register_routes' => true,
    'route_prefix' => env('MODEL_CONFIG_ROUTE_PREFIX', 'api'),
    'auth_middleware' => [],
    'allow_case_insensitive_keys' => false,
    'allowed_keys' => [],
    'model' => \Whilesmart\ModelConfiguration\Models\Configuration::class,
    'hooks' => [],
];
