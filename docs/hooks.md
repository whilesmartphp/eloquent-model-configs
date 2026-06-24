# Hooks

Hooks allow customizing package behavior at two extension points.

## Hook Types

### 1. ModelHookInterface (HTTP Request Hooks)

Runs before/after HTTP requests to configuration endpoints.

```php
use Whilesmart\ModelConfiguration\Interfaces\ModelHookInterface;
use Whilesmart\ModelConfiguration\Enums\ConfigAction;
use Illuminate\Http\Request;

class PaginateResultsHook implements ModelHookInterface
{
    public function beforeQuery(mixed $data, ConfigAction $action, Request $request): mixed
    {
        if ($action === ConfigAction::INDEX) {
            return $data->paginate(20);
        }
        return $data;
    }

    public function afterQuery(mixed $results, ConfigAction $action, Request $request): mixed
    {
        return $results;
    }
}
```

**Actions:**
- `ConfigAction::INDEX` — List configurations
- `ConfigAction::STORE` — Create configuration
- `ConfigAction::SHOW` — Get single configuration
- `ConfigAction::UPDATE` — Update configuration
- `ConfigAction::DESTROY` — Delete configuration

### 2. ConfigValueHookInterface (Value Change Hooks)

Runs when `setConfigValue()` is called (both via trait and API).

```php
use Illuminate\Database\Eloquent\Model;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Interfaces\ConfigValueHookInterface;
use Whilesmart\ModelConfiguration\Models\Configuration;

class OnboardingCompletedHook implements ConfigValueHookInterface
{
    public function onConfigValueSet(
        Model $model,
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void {
        if ($key === 'onboarding_completed' && $value === 'true') {
            app(OnboardingService::class)->complete($model);
        }
    }
}
```

**Parameters:**
- `$model` — The configurable model (e.g., User)
- `$key` — Configuration key that was set
- `$value` — New value
- `$type` — ConfigValueType enum
- `$configuration` — Configuration model instance
- `$wasCreated` — True if new, false if updated

### 3. Combined Hook

A single class can implement both interfaces:

```php
use Whilesmart\ModelConfiguration\Interfaces\ModelHookInterface;
use Whilesmart\ModelConfiguration\Interfaces\ConfigValueHookInterface;
use Whilesmart\ModelConfiguration\Enums\ConfigAction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Models\Configuration;

class MyHook implements ModelHookInterface, ConfigValueHookInterface
{
    // ModelHookInterface
    public function beforeQuery(mixed $data, ConfigAction $action, Request $request): mixed
    {
        return $data;
    }

    public function afterQuery(mixed $results, ConfigAction $action, Request $request): mixed
    {
        return $results;
    }

    // ConfigValueHookInterface
    public function onConfigValueSet(
        Model $model,
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void {
        // Handle value changes
    }
}
```

## Registering Hooks

Add to `config/model-configuration.php`:

```php
return [
    // ...
    'hooks' => [
        \App\Hooks\PaginateResultsHook::class,
        \App\Hooks\OnboardingCompletedHook::class,
        \App\Hooks\MyCombinedHook::class,
    ],
];
```

Hooks are resolved via Laravel's container (supports constructor injection).

## Use Cases

| Hook Type | Use Cases |
|-----------|-----------|
| ModelHookInterface | Pagination, filtering, response transformation, logging, rate limiting |
| ConfigValueHookInterface | Trigger side effects, sync to external services, audit logging, notifications, cache invalidation |

## Example: Audit Log Hook

```php
class AuditConfigHook implements ConfigValueHookInterface
{
    public function onConfigValueSet(
        Model $model,
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'config_key' => $key,
                'old_value' => $wasCreated ? null : $configuration->getOriginal('value'),
                'new_value' => $value,
                'type' => $type->value,
            ])
            ->log($wasCreated ? 'config_created' : 'config_updated');
    }
}
```