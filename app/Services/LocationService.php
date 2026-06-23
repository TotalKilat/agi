<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LocationService
{
    /**
     * Get the base query for DataTables server-side processing.
     */
    public function getDataTableQuery(): Builder
    {
        return Location::query()
            ->select([
                'id',
                'name',
                'created_at',
            ])
            ->withCount('fleets');
    }

    /**
     * Create a new location.
     */
    public function create(array $data): Location
    {
        return DB::transaction(fn () => Location::create($data));
    }

    /**
     * Update an existing location.
     */
    public function update(Location $location, array $data): Location
    {
        return DB::transaction(function () use ($location, $data) {
            $location->update($data);

            return $location->fresh();
        });
    }

    /**
     * Delete a location.
     */
    public function delete(Location $location): void
    {
        DB::transaction(function () use ($location): void {
            $location->delete();
        });
    }
}
