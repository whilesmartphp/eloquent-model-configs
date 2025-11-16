<?php
return [
    // 'keys' => explode(',', env(MODELS_ALLOWED_CONFIGS, 'theme,color')),
    'keys' => array_filter(array_map('trim', explode(',', env('MODELS_ALLOWED_CONFIGS', 'theme,color')))),
];