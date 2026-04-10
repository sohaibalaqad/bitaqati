<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    /**
     * Map of currency code → symbol.
     */
    public const CURRENCIES = [
        'ILS' => ['symbol' => '₪',    'name' => 'شيكل إسرائيلي',  'position' => 'after'],
        'USD' => ['symbol' => '$',    'name' => 'دولار أمريكي',    'position' => 'before'],
        'EUR' => ['symbol' => '€',    'name' => 'يورو',             'position' => 'before'],
        'GBP' => ['symbol' => '£',    'name' => 'جنيه إسترليني',   'position' => 'before'],
        'SAR' => ['symbol' => '﷼',   'name' => 'ريال سعودي',      'position' => 'after'],
        'JOD' => ['symbol' => 'د.أ', 'name' => 'دينار أردني',     'position' => 'after'],
        'AED' => ['symbol' => 'د.إ', 'name' => 'درهم إماراتي',    'position' => 'after'],
        'EGP' => ['symbol' => 'ج.م', 'name' => 'جنيه مصري',      'position' => 'after'],
        'KWD' => ['symbol' => 'د.ك', 'name' => 'دينار كويتي',     'position' => 'after'],
        'QAR' => ['symbol' => 'ر.ق', 'name' => 'ريال قطري',       'position' => 'after'],
        'BHD' => ['symbol' => 'د.ب', 'name' => 'دينار بحريني',    'position' => 'after'],
        'OMR' => ['symbol' => 'ر.ع', 'name' => 'ريال عُماني',     'position' => 'after'],
    ];

    /**
     * Get the symbol for a given currency code.
     */
    public static function symbolFor(string $code): string
    {
        return static::CURRENCIES[$code]['symbol'] ?? $code;
    }

    /**
     * Format a monetary amount using the active currency setting.
     */
    public static function format(float|int|string|null $amount): string
    {
        $amount  = number_format((float) ($amount ?? 0), 2);
        $code    = Setting::get('currency', 'ILS');
        $meta    = static::CURRENCIES[$code] ?? ['symbol' => $code, 'position' => 'after'];
        $symbol  = $meta['symbol'];

        return $meta['position'] === 'before'
            ? $symbol . $amount
            : $amount . ' ' . $symbol;
    }

    /**
     * Return a JS-safe config object for use in Blade layouts.
     * Outputs: { code, symbol, position }
     */
    public static function jsConfig(): array
    {
        $code = Setting::get('currency', 'ILS');
        $meta = static::CURRENCIES[$code] ?? ['symbol' => $code, 'position' => 'after'];

        return [
            'code'     => $code,
            'symbol'   => $meta['symbol'],
            'position' => $meta['position'],
        ];
    }
}
