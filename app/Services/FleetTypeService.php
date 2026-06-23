<?php

namespace App\Services;

use App\Models\FleetType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FleetTypeService
{
    /**
     * Get the base query for DataTables server-side processing.
     */
    public function getDataTableQuery(): Builder
    {
        return FleetType::query()
            ->select([
                'id',
                'name',
                'created_at',
            ])
            ->withCount('fleets');
    }

    /**
     * Create a new fleet type.
     */
    public function create(array $data): FleetType
    {
        return DB::transaction(fn () => FleetType::create($data));
    }

    /**
     * Update an existing fleet type.
     */
    public function update(FleetType $fleetType, array $data): FleetType
    {
        return DB::transaction(function () use ($fleetType, $data) {
            $fleetType->update($data);

            return $fleetType->fresh();
        });
    }

    /**
     * Delete a fleet type.
     */
    public function delete(FleetType $fleetType): void
    {
        DB::transaction(function () use ($fleetType): void {
            $fleetType->delete();
        });
    }
}
