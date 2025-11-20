<?php

namespace Whilesmart\ModelConfiguration\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

interface ResultsFilterHookInterface
{
    /**
     * Process the data.
     */
    public function run(Relation $query, Request $request): mixed;
}
