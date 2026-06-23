<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleetType\StoreFleetTypeRequest;
use App\Http\Requests\FleetType\UpdateFleetTypeRequest;
use App\Models\FleetType;
use App\Services\FleetTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FleetTypeController extends Controller
{
    public function __construct(
        private readonly FleetTypeService $fleetTypeService,
    ) {}

    /**
     * Display a listing of fleet types.
     */
    public function index(): View
    {
        return view('pages.fleet-types.index');
    }

    /**
     * Return DataTables JSON for fleet type listing.
     */
    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->fleetTypeService->getDataTableQuery())
            ->addColumn(
                'name',
                fn (FleetType $fleetType) => view('pages.fleet-types.columns.name', compact('fleetType'))->render(),
            )
            ->addColumn(
                'action',
                fn (FleetType $fleetType) => view('pages.fleet-types.columns.action', compact('fleetType'))->render(),
            )
            ->addColumn('fleets_count', fn (FleetType $fleetType) => number_format($fleetType->fleets_count))
            ->only([
                'action',
                'name',
                'fleets_count',
            ])
            ->rawColumns(['action', 'name'])
            ->toJson();
    }

    /**
     * Show the form for creating a new fleet type.
     */
    public function create(): View
    {
        return view('pages.fleet-types.create');
    }

    /**
     * Store a newly created fleet type.
     */
    public function store(StoreFleetTypeRequest $request): RedirectResponse
    {
        $fleetType = $this->fleetTypeService->create($request->validated());

        return redirect()
            ->route('fleet-types.index')
            ->with('success', "Fleet Type \"{$fleetType->name}\" created successfully.");
    }

    /**
     * Show the form for editing the specified fleet type.
     */
    public function edit(FleetType $fleetType): View
    {
        return view('pages.fleet-types.edit', compact('fleetType'));
    }

    /**
     * Update the specified fleet type.
     */
    public function update(UpdateFleetTypeRequest $request, FleetType $fleetType): RedirectResponse
    {
        $this->fleetTypeService->update($fleetType, $request->validated());

        return redirect()
            ->route('fleet-types.index')
            ->with('success', "Fleet Type \"{$fleetType->fresh()->name}\" updated successfully.");
    }

    /**
     * Remove the specified fleet type.
     */
    public function destroy(Request $request, FleetType $fleetType): RedirectResponse|JsonResponse
    {
        $name = $fleetType->name;
        $this->fleetTypeService->delete($fleetType);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Fleet Type \"{$name}\" deleted successfully.",
            ]);
        }

        return redirect()
            ->route('fleet-types.index')
            ->with('info', "Fleet Type \"{$name}\" deleted.");
    }
}
