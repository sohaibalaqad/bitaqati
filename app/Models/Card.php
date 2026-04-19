<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'username', 'password', 'package_id', 'status',
        'sold_to', 'sold_at', 'is_used', 'first_used_at',
    ];

    protected function casts(): array
    {
        return [
            'sold_at'       => 'datetime',
            'first_used_at' => 'datetime',
            'is_used'       => 'boolean',
        ];
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'sold_to');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }
}
