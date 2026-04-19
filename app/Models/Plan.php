<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'max_cards', 'max_users', 'max_packages', 'price', 'features', 'is_active', 'is_popular'];

    protected function casts(): array
    {
        return [
            'features'   => 'array',
            'is_active'  => 'boolean',
            'is_popular' => 'boolean',
            'price'      => 'decimal:2',
        ];
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /** 0 means unlimited */
    public function isUnlimited(string $field): bool
    {
        return ($this->$field ?? 0) === 0;
    }
}
