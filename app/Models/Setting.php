<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'key', 'value', 'group', 'label'];

    /**
     * Get a setting value — scoped to the current tenant.
     */
    public static function get(string $key, $default = null): mixed
    {
        $tenantId = app(TenantContext::class)->id() ?? 0;
        $cacheKey = "setting_{$tenantId}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $tenantId) {
            $query = static::withoutGlobalScopes()->where('key', $key);
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            } else {
                // Super-admin context: only return platform-level settings (tenant_id IS NULL),
                // never bleed a random tenant's setting into the super-admin panel.
                $query->whereNull('tenant_id');
            }
            return $query->value('value') ?? $default;
        });
    }

    /**
     * Set a setting value — scoped to the current tenant.
     */
    public static function set(string $key, $value): void
    {
        $tenantId = app(TenantContext::class)->id();

        static::withoutGlobalScopes()->updateOrCreate(
            ['key' => $key, 'tenant_id' => $tenantId],
            ['value' => $value, 'tenant_id' => $tenantId]
        );

        $tenantId = $tenantId ?? 0;
        Cache::forget("setting_{$tenantId}_{$key}");
    }
}
