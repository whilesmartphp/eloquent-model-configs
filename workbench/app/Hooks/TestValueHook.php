<?php

namespace Workbench\App\Hooks;

use Illuminate\Database\Eloquent\Model;
use Whilesmart\ModelConfiguration\Enums\ConfigValueType;
use Whilesmart\ModelConfiguration\Interfaces\ConfigValueHookInterface;
use Whilesmart\ModelConfiguration\Models\Configuration;

class TestValueHook implements ConfigValueHookInterface
{
    public static array $calls = [];

    public static function reset(): void
    {
        self::$calls = [];
    }

    public function onConfigValueSet(
        Model $model,
        string $key,
        mixed $value,
        ConfigValueType $type,
        Configuration $configuration,
        bool $wasCreated
    ): void {
        self::$calls[] = [
            'model_id' => $model->getKey(),
            'model_class' => get_class($model),
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'configuration_id' => $configuration->id,
            'was_created' => $wasCreated,
        ];
    }
}
