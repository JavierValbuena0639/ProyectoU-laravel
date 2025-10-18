<?php

return [
    // DIAN Electronic Invoicing software credentials
    'software_id' => env('FE_SOFTWARE_ID', ''),
    'software_pin' => env('FE_SOFTWARE_PIN', ''),

    // Certificate for XAdES signature (usually .p12/.pfx)
    'cert_path' => env('FE_CERT_PATH', storage_path('certs/dian.p12')),
    'cert_password' => env('FE_CERT_PASSWORD', ''),

    // Environment: 'test' (habilitación) or 'prod'
    'environment' => env('FE_ENVIRONMENT', 'test'),
];