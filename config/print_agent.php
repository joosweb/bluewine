<?php

return [
    'enabled' => env('PRINT_AGENT_ENABLED', false),
    'url' => env('PRINT_AGENT_URL', 'http://127.0.0.1:8765/print/pdf'),
    'raw_url' => env('PRINT_AGENT_RAW_URL', 'http://127.0.0.1:8765/print/raw'),
    'mode' => env('PRINT_AGENT_MODE', 'raw'),
    'token' => env('PRINT_AGENT_TOKEN', ''),
    'timeout' => env('PRINT_AGENT_TIMEOUT', 8),
];
