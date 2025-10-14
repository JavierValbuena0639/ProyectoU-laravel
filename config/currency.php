<?php

return [
    // Moneda por defecto del sistema (Colombia)
    'default' => env('APP_CURRENCY', 'COP'),
    // Moneda base para conversiones (se asume que los montos almacenados están en esta moneda)
    'base' => 'COP',
    // Monedas soportadas
    'supported' => ['COP', 'USD', 'EUR'],
    // Tasas de conversión hacia la moneda base (COP por unidad de moneda)
    // Ejemplo: 1 USD ≈ 4000 COP, 1 EUR ≈ 4300 COP (puedes ajustar por ENV)
    'to_base' => [
        'COP' => 1,
        'USD' => (int) env('COP_PER_USD', 4000),
        'EUR' => (int) env('COP_PER_EUR', 4300),
    ],
    // Símbolos por moneda
    'symbols' => [
        'COP' => 'COP$',
        'USD' => '$',
        'EUR' => '€',
    ],
    // Locale sugerido por moneda para formateo JS (Intl.NumberFormat)
    'locale_map' => [
        'COP' => 'es-CO',
        'USD' => 'en-US',
        'EUR' => 'es-ES',
    ],
];