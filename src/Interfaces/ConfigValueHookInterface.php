<?php

namespace Whilesmart\ModelConfiguration\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Models\Configuration;

interface ConfigValueHookInterface
{
    /**
     * Called after a configuration value is set or updated.
     *
     * @param  Model  $model  The model that owns the configuration
     * @param  string  $key  The configuration key
     * @param  mixed  $value  The new value
     * @param  ConfigValueType  $type  The value type
     * @param  Configuration  $configuration  The configuration model instance
     * @param  bool  $wasCreated  True if this was a new configuration, false if updated
     */
    public function onConfigValueSet(
        Model $model,
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void;
}
