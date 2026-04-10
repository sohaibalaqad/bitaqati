<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'speed', 'duration', 'price', 'cost'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
        ];
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function availableCards()
    {
        return $this->hasMany(Card::class)->where('status', 'available');
    }

    public function profit(): float
    {
        return $this->price - $this->cost;
    }
}
