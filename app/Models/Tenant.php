<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'subdomain', 'owner_id', 'plan_id', 'status'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    /** Convenience: get a setting value for this tenant */
    public function setting(string $key, $default = null): ?string
    {
        return $this->settings()->where('key', $key)->value('value') ?? $default;
    }
}
