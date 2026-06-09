<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fleet\StoreFleetRequest;
use App\Http\Requests\Fleet\UpdateFleetRequest;
use App\Models\Customer;
use App\Models\Fleet;
use App\Services\FleetService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FleetController extends Controller
{
    public function __construct(
        private readonly FleetService $fleetService,
    ) {}

    /**
     * Display a listing of fleets.
     */
    public function index(): View
    {
        return view('pages.fleets.index');
    }

    /**
     * Return DataTables JSON for fleet listing.
     */
    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->fleetService->getDataTableQuery())
            ->addColumn(
                'vehicle_name',
                fn (Fleet $fleet) => view('pages.fleets.columns.vehicle_name', compact('fleet'))->render(),
            )
            ->addColumn(
                'customer_name',
                fn (Fleet $fleet) => $fleet->customer?->name ?? '—',
            )
            ->addColumn(
                'status',
                fn (Fleet $fleet) => view('pages.fleets.columns.status', compact('fleet'))->render(),
            )
            ->addColumn(
                'action',
                fn (Fleet $fleet) => view('pages.fleets.columns.action', compact('fleet'))->render(),
            )
            ->filterColumn('customer_name', function (Builder $query, string $keyword): void {
                $query->whereHas('customer', function (Builder $q) use ($keyword): void {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->only([
                'action',
                'vehicle_name',
                'device_name',
                'customer_name',
                'status',
            ])
            ->rawColumns(['action', 'vehicle_name', 'status'])
            ->toJson();
    }

    /**
     * Show the form for creating a new fleet.
     */
    public function create(): View
    {
        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.fleets.create', compact('customers'));
    }

    /**
     * Store a newly created fleet.
     */
    public function store(StoreFleetRequest $request): RedirectResponse
    {
        $fleet = $this->fleetService->create($request->validated());

        return redirect()
            ->route('fleets.index')
            ->with('success', "Fleet \"{$fleet->vehicle_name}\" created successfully.");
    }

    /**
     * Show the form for editing the specified fleet.
     */
    public function edit(Fleet $fleet): View
    {
        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.fleets.edit', compact('fleet', 'customers'));
    }

    /**
     * Update the specified fleet.
     */
    public function update(UpdateFleetRequest $request, Fleet $fleet): RedirectResponse
    {
        $this->fleetService->update($fleet, $request->validated());

        return redirect()
            ->route('fleets.index')
            ->with('success', "Fleet \"{$fleet->fresh()->vehicle_name}\" updated successfully.");
    }

    /**
     * Remove the specified fleet.
     */
    public function destroy(Request $request, Fleet $fleet): RedirectResponse|JsonResponse
    {
        $vehicleName = $fleet->vehicle_name;
        $this->fleetService->delete($fleet);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Fleet \"{$vehicleName}\" deleted successfully.",
            ]);
        }

        return redirect()
            ->route('fleets.index')
            ->with('info', "Fleet \"{$vehicleName}\" deleted.");
    }
}
