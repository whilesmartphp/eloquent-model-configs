<?php

namespace Whilesmart\ModelConfiguration\Interfaces;

use Illuminate\Http\Request;
use Whilesmart\ModelConfiguration\Enums\ConfigAction;

interface ModelHookInterface
{
    public function beforeQuery(mixed $data, ConfigAction $action, Request $request): mixed;

    public function afterQuery(mixed $results, ConfigAction $action, Request $request): mixed;
}
