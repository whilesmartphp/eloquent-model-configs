# Custom Configuration Models

Extend the base Configuration model to add features like soft deletes, auditing, or custom traits.

## Step 1: Create Custom Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\ModelConfiguration\Models\Configuration as BaseConfiguration;

class Configuration extends BaseConfiguration
{
    use SoftDeletes;

    protected $fillable = [
        'configurable_id',
        'configurable_type',
        'key',
        'type',
        'value',
        'metadata',
    ];

    // Add custom relationships, accessors, scopes, etc.
}
```

## Step 2: Configure Package

In `config/model-configuration.php`:

```php
return [
    // ...
    'model' => \App\Models\Configuration::class,
];
```

## Step 3: Publish and Modify Migrations

```bash
php artisan vendor:publish --tag=model-configuration-migrations
```

Edit the migration to add `deleted_at` column:

```php
Schema::create('configurations', function (Blueprint $table) {
    $table->id();
    $table->string('key');
    $table->json('value');
    $table->string('configurable_type');
    $table->unsignedBigInteger('configurable_id');
    $table->string('type')->default('string');
    $table->softDeletes(); // Add this line
    $table->timestamps();

    $table->unique(['configurable_id', 'configurable_type', 'key']);
});
```

```bash
php artisan migrate
```

## Features Enabled

### Soft Deletes
```php
$user->setConfigValue('theme', 'dark', ConfigValueType::String);

// Soft delete
$user->configurations()->where('key', 'theme')->delete();

// Restore
$user->configurations()->withTrashed()->where('key', 'theme')->restore();

// Query with trashed
$user->configurations()->withTrashed()->get();
```

### Auditing (spatie/laravel-activitylog)
```php
use Spatie\Activitylog\Traits\LogsActivity;

class Configuration extends BaseConfiguration
{
    use LogsActivity;

    protected static $logAttributes = ['key', 'value', 'type'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
```

### Custom Traits
```php
class Configuration extends BaseConfiguration
{
    use HasUuid, HasTeam, HasFactory;

    // Custom methods
    public function scopeForUser($query, User $user)
    {
        return $query->where('configurable_type', User::class)
            ->where('configurable_id', $user->id);
    }
}
```

### Additional Columns
Add columns to the migration and model:

```php
// Migration
$table->string('description')->nullable();
$table->boolean('is_system')->default(false);

// Model
protected $fillable = [
    'configurable_id',
    'configurable_type',
    'key',
    'type',
    'value',
    'metadata',
    'description',
    'is_system',
];
```

## Benefits

- No need to override controllers or routes
- Package automatically uses your custom model
- Receive package updates without maintaining custom controllers
- Full Laravel model capabilities (observers, events, scopes, etc.)

## Testing

```php
// Verify custom model is used
$config = $user->setConfigValue('test', 'value', ConfigValueType::String);
assert($config instanceof \App\Models\Configuration);
assert($config->trashed() === false);
```