# API Endpoints

The package provides RESTful API endpoints for managing configurations.

## Base URL

Default: `/api/model-configurations`
Configurable via `config('model-configuration.route_prefix')`.

## Authentication

All endpoints require authentication. Configure in `config/model-configuration.php`:

```php
'auth_middleware' => ['auth:sanctum'],
```

## Endpoints

### List Configurations

```
GET /api/model-configurations
```

**Query Parameters:**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `configurable_type` | Yes | FQCN of the model (e.g., `App\Models\User`) |
| `configurable_id` | Yes | Model ID |

**Example:**
```
GET /api/model-configurations?configurable_type=App\Models\User&configurable_id=1
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "key": "timezone",
      "value": "America/New_York",
      "type": "string",
      "configurable_type": "App\\Models\\User",
      "configurable_id": 1,
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Get Single Configuration

```
GET /api/model-configurations/{key}
```

**Query Parameters:**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `configurable_type` | Yes | FQCN of the model |
| `configurable_id` | Yes | Model ID |

**Example:**
```
GET /api/model-configurations/timezone?configurable_type=App\Models\User&configurable_id=1
```

### Create Configuration

```
POST /api/model-configurations
```

**Body (JSON):**

| Field | Required | Description |
|-------|----------|-------------|
| `key` | Yes | Configuration key |
| `value` | Yes | Configuration value |
| `type` | Yes | One of: `string`, `int`, `float`, `bool`, `array`, `json`, `date` |
| `configurable_type` | Yes | FQCN of the model |
| `configurable_id` | Yes | Model ID |

**Example:**
```json
POST /api/model-configurations
{
    "key": "theme",
    "value": "dark",
    "type": "string",
    "configurable_type": "App\\Models\\User",
    "configurable_id": 1
}
```

### Update Configuration

```
PUT /api/model-configurations/{key}
```

**Body (JSON):** Same as create.

**Example:**
```json
PUT /api/model-configurations/theme
{
    "value": "light",
    "type": "string",
    "configurable_type": "App\\Models\\User",
    "configurable_id": 1
}
```

### Delete Configuration

```
DELETE /api/model-configurations/{key}
```

**Query Parameters:**

| Parameter | Required | Description |
|-----------|----------|-------------|
| `configurable_type` | Yes | FQCN of the model |
| `configurable_id` | Yes | Model ID |

**Example:**
```
DELETE /api/model-configurations/theme?configurable_type=App\Models\User&configurable_id=1
```

## OpenAPI Documentation

The package includes OpenAPI attributes on the controller. To expose:

1. Publish docs:
   ```bash
   php artisan vendor:publish --tag=model-configuration-docs
   ```

2. Install `zircote/swagger-php` (included in require-dev)

3. Generate OpenAPI spec:
   ```bash
   ./vendor/bin/openapi --output storage/api-docs
   ```

## Validation Rules

### Allowed Keys
If `allowed_keys` is configured, only those keys can be created/updated.

### Case Sensitivity
If `allow_case_insensitive_keys` is true, key lookups are case-insensitive.

### Type Validation
The `type` field must be one of the supported enum values.

## Error Responses

| Status | Scenario |
|--------|----------|
| 401 | Unauthenticated |
| 403 | Key not in `allowed_keys` |
| 404 | Configuration not found |
| 422 | Validation failed (missing fields, invalid type) |
| 500 | Server error |