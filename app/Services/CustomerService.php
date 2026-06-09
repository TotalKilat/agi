<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Get paginated list of active customers.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all active customers for dropdowns.
     */
    public function getAll(): Collection
    {
        return Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new customer.
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = bcrypt($data['password']);
            return Customer::create($data);
        });
    }

    /**
     * Update an existing customer.
     */
    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }
            $customer->update($data);
            return $customer->fresh();
        });
    }

    /**
     * Delete a customer.
     */
    public function delete(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $customer->delete();
        });
    }
}
