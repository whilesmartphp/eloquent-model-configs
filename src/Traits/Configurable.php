<?php

namespace Whilesmart\ModelConfiguration\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Interfaces\ConfigValueHookInterface;
use Whilesmart\ModelConfiguration\Models\Configuration;

trait Configurable
{
    public function getConfig(string $key): ?Configuration
    {
        return $this->configurations()->where('key', $key)
            ->first();
    }

    public function configurations(): MorphMany
    {
        $model = config('model-configuration.model', Configuration::class);

        return $this->morphMany($model, 'configurable');
    }

    public function getConfigValue(string $key)
    {
        $config = $this->configurations()->where('key', $key)
            ->first();
        if (! is_null($config)) {
            $type = ConfigValueType::tryFrom($config->type);
            if (! is_null($type)) {
                return $type->getValue($config->value);
            }
        }

        return null;
    }

    public function getConfigType(string $key): ?ConfigValueType
    {
        $config = $this->configurations()->where('key', $key)
            ->first();
        if (! is_null($config)) {
            $type = ConfigValueType::tryFrom($config->type);
            if (! is_null($type)) {
                return $type;
            }
        }

        return null;
    }

    public function setConfigValue($key, $value, ConfigValueType $type): Configuration
    {
        if ($type === ConfigValueType::Date && $value instanceof Carbon) {
            $value = $value->toDateTimeString();
        }

        $configuration = $this->configurations()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type->value]
        );

        $this->runConfigValueHooks($key, $value, $type, $configuration, $configuration->wasRecentlyCreated);

        return $configuration;
    }

    protected function runConfigValueHooks(
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void {
        $hooks = config('model-configuration.hooks', []);

        foreach ($hooks as $hookClass) {
            if ($hookClass && class_exists($hookClass)) {
                $hook = app($hookClass);
                if ($hook instanceof ConfigValueHookInterface) {
                    $hook->onConfigValueSet($this, $key, $value, $type, $configuration, $wasCreated);
                }
            }
        }
    }

    public function getConfigurationsAttribute()
    {
        return $this->configurations()->get();
    }
}
