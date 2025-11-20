<?php

namespace Workbench\App\Hooks;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Whilesmart\ModelConfiguration\Interfaces\ResultsFilterHookInterface;

class CustomFilterHook implements ResultsFilterHookInterface
{
    public function run(Relation $query, Request $request): mixed
    {
        $results = $query->get();

        return ['results' => $results];
    }
}
