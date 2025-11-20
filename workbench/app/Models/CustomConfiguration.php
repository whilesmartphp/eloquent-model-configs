<?php

namespace Workbench\App\Models;

use Whilesmart\ModelConfiguration\Models\Configuration;

class CustomConfiguration extends Configuration
{
    protected $table = 'configurations';

    protected $appends = ['is_custom'];

    public function getIsCustomAttribute(): bool
    {
        return true;
    }
}
