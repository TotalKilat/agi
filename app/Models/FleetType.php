<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetType extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'fleet_type';

    protected $fillable = [
        'name',
    ];

    public function fleets(): HasMany
    {
        return $this->hasMany(Fleet::class);
    }
}
