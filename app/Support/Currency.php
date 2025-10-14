<?php

namespace App\Support;

class Currency
{
    /**
     * Convierte y formatea un monto.
     * @param float|int $amount Monto en moneda base (config('currency.base'))
     * @param string|null $toCurrency Moneda destino (COP/USD/EUR). Si es null, usa config('currency.default').
     * @return string Monto formateado con símbolo y separadores locales.
     */
    public static function format($amount, ?string $toCurrency = null): string
    {
        $toCurrency = $toCurrency ?: config('currency.default');
        $base = config('currency.base');
        $symbols = config('currency.symbols');
        $localeMap = config('currency.locale_map');

        // Convertir desde base a destino
        $converted = self::convertBaseTo($amount, $toCurrency);

        // Formatear con Intl si disponible
        $locale = $localeMap[$toCurrency] ?? 'es-CO';
        $formatted = false;
        if (class_exists('NumberFormatter')) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            // Mapear códigos ISO: COP, USD, EUR
            $currencyIso = $toCurrency;
            $formatted = $formatter->formatCurrency((float) $converted, $currencyIso);
        }

        if ($formatted === false || $formatted === null) {
            // Fallback simple
            $symbol = $symbols[$toCurrency] ?? '';
            $formatted = $symbol . number_format((float) $converted, 2, ',', '.');
        }

        return $formatted;
    }

    /**
     * Convierte monto desde la moneda base a la moneda destino.
     */
    public static function convertBaseTo($amount, string $toCurrency): float
    {
        $base = config('currency.base');
        if ($toCurrency === $base) {
            return (float) $amount;
        }

        // Tasas de conversión: cuantos COP por 1 unidad de moneda
        $toBase = config('currency.to_base');

        // Si base es COP, para convertir a USD: amount(COP) / COP_PER_USD
        if ($base === 'COP') {
            $rate = $toBase[$toCurrency] ?? null;
            if (!$rate || $rate <= 0) return (float) $amount;
            return (float) $amount / (float) $rate;
        }

        // General: convertir base -> COP -> destino
        // Primero base a COP
        $baseToCOP = $toBase[$base] ?? null; // COP por 1 base
        if (!$baseToCOP || $baseToCOP <= 0) return (float) $amount;
        $cop = (float) $amount * (float) $baseToCOP;

        // Luego COP a destino
        $destRate = $toBase[$toCurrency] ?? null;
        if (!$destRate || $destRate <= 0) return (float) $amount;
        return (float) $cop / (float) $destRate;
    }
}