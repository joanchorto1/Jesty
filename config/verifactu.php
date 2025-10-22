<?php

return [
    'connection_timeout' => env('VERIFACTU_CONNECTION_TIMEOUT', 30),
    'wsdl_cache' => env('VERIFACTU_WSDL_CACHE', defined('WSDL_CACHE_NONE') ? WSDL_CACHE_NONE : 0),
    'local_wsdl' => env('VERIFACTU_LOCAL_WSDL', storage_path('app/verifactu/SistemaFacturacion.wsdl')),
    'ssl' => [
        'verify_peer' => env('VERIFACTU_VERIFY_PEER', true),
        'verify_peer_name' => env('VERIFACTU_VERIFY_PEER_NAME', true),
        'allow_self_signed' => env('VERIFACTU_ALLOW_SELF_SIGNED', false),
        'cafile' => env('VERIFACTU_CAFILE'),
        'capath' => env('VERIFACTU_CAPATH'),
        'ciphers' => env('VERIFACTU_CIPHERS'),
        'crypto_method' => env('VERIFACTU_CRYPTO_METHOD'),
    ],
    'environments' => [
        'sandbox' => [
            'wsdl' => env('VERIFACTU_SANDBOX_WSDL', 'https://prewww1.aeat.es/wlpl/TIKE-CONT/ws/SistemaFacturacion.wsdl'),
            'endpoint' => env('VERIFACTU_SANDBOX_ENDPOINT'),
            'local_wsdl' => env('VERIFACTU_SANDBOX_LOCAL_WSDL'),
        ],
        'production' => [
            'wsdl' => env('VERIFACTU_PRODUCTION_WSDL', 'https://www1.aeat.es/wlpl/TIKE-CONT/ws/SistemaFacturacion.wsdl'),
            'endpoint' => env('VERIFACTU_PRODUCTION_ENDPOINT'),
            'local_wsdl' => env('VERIFACTU_PRODUCTION_LOCAL_WSDL'),
        ],
    ],
];
