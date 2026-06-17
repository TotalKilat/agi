<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FleetTransaction extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'fleet_id',
        'transaction_date',
        'vehicle_name_snapshot',
        'odometer_km',
        'initial_volume_l',
        'final_volume_l',
        'usage_l',
        'cost_rp',
        'idle_usage_l',
        'km_per_l',
        'l_per_km',
        'cost_per_km',
        'refuel_l',
        'refuel_times',
        'running_duration_seconds',
        'idle_duration_seconds',
        'stop_duration_seconds',
        'source_filename',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'odometer_km' => 'decimal:2',
            'initial_volume_l' => 'decimal:2',
            'final_volume_l' => 'decimal:2',
            'usage_l' => 'decimal:2',
            'cost_rp' => 'decimal:2',
            'idle_usage_l' => 'decimal:2',
            'km_per_l' => 'decimal:4',
            'l_per_km' => 'decimal:4',
            'cost_per_km' => 'decimal:4',
            'refuel_l' => 'decimal:3',
            'refuel_times' => 'integer',
            'running_duration_seconds' => 'integer',
            'idle_duration_seconds' => 'integer',
            'stop_duration_seconds' => 'integer',
            'imported_at' => 'datetime',
        ];
    }

    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }
}
