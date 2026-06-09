<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = $this->customerService->getPaginated();

        return view('pages.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('pages.customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->create($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', "Customer \"{$customer->name}\" created successfully.");
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('pages.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->customerService->update($customer, $request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', "Customer \"{$customer->fresh()->name}\" updated successfully.");
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $this->customerService->delete($customer);

        return redirect()
            ->route('customers.index')
            ->with('info', "Customer \"{$name}\" deleted.");
    }
}
