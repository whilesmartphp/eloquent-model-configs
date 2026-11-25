# Configurable Trait

The `Configurable` trait provides all methods for managing model configurations.

## Usage

```php
use Whilesmart\ModelConfiguration\Traits\Configurable;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;

class User extends Model
{
    use Configurable;
}
```

## Methods

### `configurations()`

Returns the MorphMany relationship to configurations.

```php
$user->configurations()->get();
$user->configurations()->where('key', 'theme')->first();
```

### `getConfig(string $key): ?Configuration`

Returns the full Configuration model for a key.

```php
$config = $user->getConfig('timezone');

if ($config) {
    $config->key;       // "timezone"
    $config->value;     // "America/New_York"
    $config->type;      // "string"
    $config->metadata;  // additional data (if any)
}
```

### `getConfigValue(string $key): mixed`

Returns the typed value of a configuration. Automatically casts based on stored type.

```php
$timezone = $user->getConfigValue('timezone');        // string
$perPage  = $user->getConfigValue('posts_per_page');  // int
$enabled  = $user->getConfigValue('notifications');   // bool
$prefs    = $user->getConfigValue('preferences');     // array
$layout   = $user->getConfigValue('dashboard_layout'); // object/array
$date     = $user->getConfigValue('trial_ends_at');   // Carbon|null
```

Returns `null` if key doesn't exist or type parsing fails.

### `getConfigType(string $key): ?ConfigValueType`

Returns the ConfigValueType enum for a key.

```php
$type = $user->getConfigType('timezone'); // ConfigValueType::String
$type = $user->getConfigType('per_page'); // ConfigValueType::Integer
```

### `setConfigValue(string $key, mixed $value, ConfigValueType $type): Configuration`

Creates or updates a configuration value. Returns the Configuration model.

```php
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;

$user->setConfigValue('theme', 'dark', ConfigValueType::String);
$user->setConfigValue('per_page', 50, ConfigValueType::Integer);
$user->setConfigValue('debug_mode', true, ConfigValueType::Boolean);
$user->setConfigValue('tags', ['laravel', 'php'], ConfigValueType::Array);
$user->setConfigValue('settings', ['foo' => 'bar'], ConfigValueType::Json);
$user->setConfigValue('expires_at', now()->addMonth(), ConfigValueType::Date);
```

### `getConfigurationsAttribute()`

Returns all configurations as a collection. Automatically available when `$appends` includes `configurations`.

```php
class User extends Model
{
    use Configurable;
    protected $appends = ['configurations'];
}

$user->configurations; // Collection of Configuration models
```

## Hook Integration

The trait automatically runs registered hooks when `setConfigValue()` is called. See [Hooks](hooks.md).

## Polymorphic Relationship

The trait uses a polymorphic relationship, so any model can use it:

```php
class Team extends Model { use Configurable; }
class Project extends Model { use Configurable; }
class Setting extends Model { use Configurable; }
```

Each model gets its own configurations table entries via `configurable_type` and `configurable_id`.