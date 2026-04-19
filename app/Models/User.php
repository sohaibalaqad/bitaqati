<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'phone', 'email', 'password', 'role', 'balance', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'balance'           => 'decimal:2',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isNetworkAdmin(): bool
    {
        return $this->role === 'network_admin';
    }

    /** Backward-compatible: returns true for both network_admin and super_admin */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'network_admin']);
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'sold_to');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function rechargeRequests()
    {
        return $this->hasMany(RechargeRequest::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
