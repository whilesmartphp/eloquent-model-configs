# Installation

## Requirements

- PHP 8.1+
- Laravel 10.x or 11.x
- Laravel Sanctum (for API authentication)

## Install via Composer

```bash
composer require whilesmart/eloquent-model-configuration
```

The service provider is auto-discovered.

## Publish and Migrate

### Publish Everything (Recommended)

```bash
php artisan vendor:publish --tag=model-configuration
php artisan migrate
```

This publishes:
- Migrations → `database/migrations/`
- Routes → `routes/model-configuration.php`
- Controllers → `app/Http/Controllers/`
- Config → `config/model-configuration.php`

### Publish Selectively

**Routes only:**
```bash
php artisan vendor:publish --tag=model-configuration-routes
```

**Migrations only:**
```bash
php artisan vendor:publish --tag=model-configuration-migrations
```

**Controllers only:**
```bash
php artisan vendor:publish --tag=model-configuration-controllers
```

**Documentation (OpenAPI):**
```bash
php artisan vendor:publish --tag=model-configuration-docs
```

## Register Routes

After publishing routes, add to your `routes/api.php`:

```php
require base_path('routes/model-configuration.php');
```

## Configure Auth Middleware

Edit `config/model-configuration.php`:

```php
return [
    // ...
    'auth_middleware' => ['auth:sanctum'],
    // ...
];
```

The default routes require authentication.