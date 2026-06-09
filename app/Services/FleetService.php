<?php

namespace App\Services;

use App\Models\Fleet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FleetService
{
    /**
     * Get the base query for DataTables server-side processing.
     */
    public function getDataTableQuery(): Builder
    {
        return Fleet::query()
            ->with('customer')
            ->select([
                'id',
                'customer_id',
                'vehicle_name',
                'device_name',
                'is_active',
                'created_at',
            ]);
    }

    /**
     * Create a new fleet.
     */
    public function create(array $data): Fleet
    {
        return DB::transaction(fn () => Fleet::create($data));
    }

    /**
     * Update an existing fleet.
     */
    public function update(Fleet $fleet, array $data): Fleet
    {
        return DB::transaction(function () use ($fleet, $data) {
            $fleet->update($data);
            return $fleet->fresh();
        });
    }

    /**
     * Delete a fleet.
     */
    public function delete(Fleet $fleet): void
    {
        DB::transaction(function () use ($fleet) {
            $fleet->delete();
        });
    }
}
