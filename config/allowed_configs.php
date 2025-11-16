<?php
return [
    'keys' => explode(',', env(MODELS_ALLOWED_CONFIGS, 'theme,color')),
];