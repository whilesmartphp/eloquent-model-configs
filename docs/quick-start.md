# Quick Start

## 1. Add Trait to Your Model

```php
use Whilesmart\ModelConfiguration\Traits\Configurable;

class User extends Model
{
    use Configurable;
}
```

## 2. Run Migrations

```bash
php artisan migrate
```

## 3. Set Configuration Values

```php
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;

$user = User::find(1);

// String value
$user->setConfigValue('timezone', 'America/New_York', ConfigValueType::String);

// Integer value
$user->setConfigValue('posts_per_page', 25, ConfigValueType::Integer);

// Boolean value
$user->setConfigValue('notifications_enabled', true, ConfigValueType::Boolean);

// Array value
$user->setConfigValue('preferences', ['theme' => 'dark', 'lang' => 'en'], ConfigValueType::Array);

// JSON value (complex objects)
$user->setConfigValue('dashboard_layout', ['widgets' => ['stats', 'charts']], ConfigValueType::Json);

// Date value
$user->setConfigValue('trial_ends_at', now()->addDays(14), ConfigValueType::Date);
```

## 4. Get Configuration Values

```php
// Get typed value (returns correct PHP type)
$timezone = $user->getConfigValue('timezone'); // string
$perPage = $user->getConfigValue('posts_per_page'); // int
$enabled = $user->getConfigValue('notifications_enabled'); // bool

// Get raw configuration model
$config = $user->getConfig('timezone');
$config->key;     // "timezone"
$config->value;   // "America/New_York"
$config->type;    // "string"

// Check if config exists
if ($user->getConfig('timezone')) {
    // ...
}
```

## 5. Access via API

### Get all configurations for a model
```
GET /api/model-configurations?configurable_type=App\Models\User&configurable_id=1
```

### Get single configuration
```
GET /api/model-configurations/{key}?configurable_type=App\Models\User&configurable_id=1
```

### Create/Update configuration
```
POST /api/model-configurations
{
    "key": "theme",
    "value": "dark",
    "type": "string",
    "configurable_type": "App\\Models\\User",
    "configurable_id": 1
}
```

### Delete configuration
```
DELETE /api/model-configurations/{key}?configurable_type=App\Models\User&configurable_id=1
```