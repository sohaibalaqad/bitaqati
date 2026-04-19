<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SubdomainNotReserved implements ValidationRule
{
    /**
     * Subdomains that are reserved for platform use and cannot be registered.
     */
    private const RESERVED = [
        'admin', 'superadmin', 'super', 'api', 'app', 'mail', 'smtp', 'pop', 'imap',
        'ftp', 'sftp', 'ssh', 'www', 'web', 'portal', 'dashboard', 'panel',
        'login', 'register', 'auth', 'oauth', 'billing', 'pay', 'payment',
        'support', 'help', 'docs', 'status', 'static', 'assets', 'cdn',
        'blog', 'shop', 'store', 'dev', 'test', 'staging', 'demo', 'sandbox',
        'root', 'system', 'platform', 'manage', 'management', 'cpanel',
        'bitaqati', 'default', 'main', 'master', 'primary',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), self::RESERVED, true)) {
            $fail('هذا الرابط محجوز ولا يمكن استخدامه، اختر رابطاً آخر');
        }
    }
}
