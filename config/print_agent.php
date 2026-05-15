<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Print agent (USB local printer)
    |--------------------------------------------------------------------------
    |
    | El "agente local" es el binario en tools/usb-print-agent/ que corre en
    | la PC del cajero y expone HTTP en 127.0.0.1:8765. Bluewine puede llegar
    | a el de dos formas:
    |
    |  - 'server'  -> PHP llama al agente con curl. Solo funciona si Laravel
    |                 vive en la misma red local que el agente. NO sirve cuando
    |                 Laravel esta en hosting.
    |  - 'client'  -> Laravel devuelve el payload en base64 al navegador y el
    |                 navegador hace fetch directamente a 127.0.0.1:8765. Es la
    |                 unica via cuando Bluewine vive en hosting.
    |
    | Por defecto usamos 'client' porque es el caso real de produccion.
    |
    */

    'enabled' => env('PRINT_AGENT_ENABLED', false),

    // Modo de despacho: 'client' (navegador) o 'server' (curl desde PHP).
    'dispatch' => env('PRINT_AGENT_DISPATCH', 'client'),

    // URLs usadas cuando el dispatch es 'server' (PHP -> agente con curl).
    'url' => env('PRINT_AGENT_URL', 'http://127.0.0.1:8765/print/pdf'),
    'raw_url' => env('PRINT_AGENT_RAW_URL', 'http://127.0.0.1:8765/print/raw'),

    // URLs que se entregan al navegador cuando dispatch = 'client'.
    // Deben apuntar a 127.0.0.1 para evitar mixed-content si Bluewine corre en HTTPS.
    'client_url' => env('PRINT_AGENT_CLIENT_URL', 'http://127.0.0.1:8765/print/pdf'),
    'client_raw_url' => env('PRINT_AGENT_CLIENT_RAW_URL', 'http://127.0.0.1:8765/print/raw'),

    // 'raw' (ESC/POS) o 'pdf'.
    'mode' => env('PRINT_AGENT_MODE', 'raw'),

    'token' => env('PRINT_AGENT_TOKEN', ''),
    'timeout' => env('PRINT_AGENT_TIMEOUT', 8),
];
