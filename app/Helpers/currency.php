<?php

/**
 * Global Currency Helpers
 * All price formatting goes through here — never hardcode symbols in views.
 */

if (! function_exists('currency_code')) {
    /**
     * Get the active currency code (e.g. "ILS", "USD", "EUR").
     */
    function currency_code(): string
    {
        return \App\Models\Setting::get('currency', 'ILS');
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get the active currency symbol (e.g. "₪", "$", "€").
     */
    function currency_symbol(): string
    {
        return \App\Helpers\CurrencyHelper::symbolFor(currency_code());
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format a number as a localized currency string.
     *
     * Examples:
     *   format_currency(50)     → "50.00 ₪"  (ILS)
     *   format_currency(50)     → "$50.00"    (USD)
     *   format_currency(50)     → "€50.00"    (EUR)
     */
    function format_currency(float|int|string|null $amount): string
    {
        return \App\Helpers\CurrencyHelper::format($amount);
    }
}

if (! function_exists('feature')) {
    /**
     * Check if a platform feature flag is enabled.
     *
     * Usage: @if(feature('chat')) ... @endif
     */
    function feature(string $flag, bool $default = false): bool
    {
        return (bool) config("platform.features.{$flag}", $default);
    }
}
