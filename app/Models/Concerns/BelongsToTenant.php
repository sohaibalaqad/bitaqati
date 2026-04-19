<?php

namespace App\Models\Concerns;

use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // ── Global Scope: auto-filter all queries by current tenant ─────
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = app(TenantContext::class)->id();
            if ($tenantId) {
                $builder->where((new static)->getTable() . '.tenant_id', $tenantId);
            }
        });

        // ── Auto-assign tenant_id on create ─────────────────────────────
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $tenantId = app(TenantContext::class)->id();

                // Hard-fail if a tenant-scoped model is being created with no tenant context.
                // This catches bugs where TenantContext is unset and would silently produce
                // orphan rows (tenant_id = NULL) that leak across tenants.
                if (! $tenantId) {
                    throw new \RuntimeException(
                        'Cannot create ' . static::class . ' without a tenant context. ' .
                        'Use withoutGlobalScopes() and set tenant_id explicitly for platform-level operations.'
                    );
                }

                $model->tenant_id = $tenantId;
            }
        });
    }

    /** Remove tenant scope for a single query (e.g. cross-tenant super admin reads) */
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope('tenant');
    }
}
