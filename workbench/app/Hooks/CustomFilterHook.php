<?php

namespace Workbench\App\Hooks;

use Illuminate\Http\Request;
use Whilesmart\ModelConfiguration\Enums\ConfigAction;
use Whilesmart\ModelConfiguration\Interfaces\ModelHookInterface;

class CustomFilterHook implements ModelHookInterface
{
    public function beforeQuery(mixed $data, ConfigAction $action, Request $request): mixed
    {
        if ($action == ConfigAction::INDEX) {
            return $data->paginate();
        }

        return $data;
    }

    public function afterQuery(mixed $results, ConfigAction $action, Request $request): mixed
    {
        return $results;
    }
}
