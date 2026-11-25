# Value Types

The `ConfigValueType` enum defines supported configuration value types with automatic casting.

## Supported Types

| Enum Case | Database Type | PHP Return Type | Description |
|-----------|---------------|-----------------|-------------|
| `String` | `'string'` | `string` | Plain text |
| `Integer` | `'int'` | `int` | Whole numbers |
| `Float` | `'float'` | `float` | Decimal numbers |
| `Boolean` | `'bool'` | `bool` | True/false |
| `Array` | `'array'` | `array` | Indexed/associative arrays |
| `Json` | `'json'` | `mixed` | Complex objects (stored as JSON) |
| `Date` | `'date'` | `Carbon|null` | DateTime objects |

## Usage Examples

```php
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;

$user->setConfigValue('name', 'John', ConfigValueType::String);
$user->setConfigValue('age', 30, ConfigValueType::Integer);
$user->setConfigValue('rating', 4.5, ConfigValueType::Float);
$user->setConfigValue('active', true, ConfigValueType::Boolean);
$user->setConfigValue('tags', ['php', 'laravel'], ConfigValueType::Array);
$user->setConfigValue('profile', ['bio' => 'Dev', 'avatar' => 'img.png'], ConfigValueType::Json);
$user->setConfigValue('birthday', '1990-01-15', ConfigValueType::Date);
```

## Type Casting Behavior

### String
```php
$user->setConfigValue('name', 123, ConfigValueType::String);
$user->getConfigValue('name'); // "123" (string)
```

### Integer
```php
$user->setConfigValue('count', '42', ConfigValueType::Integer);
$user->getConfigValue('count'); // 42 (int)

$user->setConfigValue('count', 3.14, ConfigValueType::Integer);
$user->getConfigValue('count'); // 3 (int)
```

### Float
```php
$user->setConfigValue('price', '19.99', ConfigValueType::Float);
$user->getConfigValue('price'); // 19.99 (float)
```

### Boolean
```php
$user->setConfigValue('enabled', 'true', ConfigValueType::Boolean);
$user->getConfigValue('enabled'); // true (bool)

$user->setConfigValue('enabled', 1, ConfigValueType::Boolean);
$user->getConfigValue('enabled'); // true (bool)

$user->setConfigValue('enabled', 'false', ConfigValueType::Boolean);
$user->getConfigValue('enabled'); // false (bool)
```

### Array
```php
$user->setConfigValue('colors', ['red', 'blue'], ConfigValueType::Array);
$user->getConfigValue('colors'); // ['red', 'blue'] (array)

$user->setConfigValue('settings', new Collection(['a' => 1]), ConfigValueType::Array);
$user->getConfigValue('settings'); // ['a' => 1] (array)
```

### Json
```php
$user->setConfigValue('data', ['nested' => ['deep' => 'value']], ConfigValueType::Json);
$user->getConfigValue('data'); // ['nested' => ['deep' => 'value']] (preserved structure)
```

### Date
```php
$user->setConfigValue('expires', '2025-12-31', ConfigValueType::Date);
$user->getConfigValue('expires'); // Carbon instance

$user->setConfigValue('expires', now()->addDays(30), ConfigValueType::Date);
$user->getConfigValue('expires'); // Carbon instance

// Invalid date returns null
$user->setConfigValue('expires', 'not-a-date', ConfigValueType::Date);
$user->getConfigValue('expires'); // null (with warning logged)
```

## Checking Types Programmatically

```php
$type = $user->getConfigType('age'); // ConfigValueType::Integer

if ($type === ConfigValueType::Integer) {
    // ...
}

// Get all type cases
ConfigValueType::cases(); // [String, Integer, Float, Boolean, Array, Json, Date]
```

## Database Storage

All values are stored as JSON in the `value` column. The `type` column stores the enum string (e.g., `'string'`, `'int'`).

```sql
-- configurations table
id | key         | value              | type   | configurable_type      | configurable_id
---|-------------|--------------------|--------|------------------------|----------------
1  | timezone    | "America/NY"| "string"| App\Models\User      | 1
2  | per_page    | 25                 | "int"  | App\Models\User      | 1
3  | settings    | {"theme":"dark"}   | "json" | App\Models\User      | 1
```