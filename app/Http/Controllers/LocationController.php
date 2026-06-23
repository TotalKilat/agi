<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
    ) {}

    /**
     * Display a listing of locations.
     */
    public function index(): View
    {
        return view('pages.locations.index');
    }

    /**
     * Return DataTables JSON for location listing.
     */
    public function data(): JsonResponse
    {
        return DataTables::eloquent($this->locationService->getDataTableQuery())
            ->addColumn(
                'name',
                fn (Location $location) => view('pages.locations.columns.name', compact('location'))->render(),
            )
            ->addColumn(
                'action',
                fn (Location $location) => view('pages.locations.columns.action', compact('location'))->render(),
            )
            ->addColumn('fleets_count', fn (Location $location) => number_format($location->fleets_count))
            ->only([
                'action',
                'name',
                'fleets_count',
            ])
            ->rawColumns(['action', 'name'])
            ->toJson();
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        return view('pages.locations.create');
    }

    /**
     * Store a newly created location.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $location = $this->locationService->create($request->validated());

        return redirect()
            ->route('locations.index')
            ->with('success', "Location \"{$location->name}\" created successfully.");
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        return view('pages.locations.edit', compact('location'));
    }

    /**
     * Update the specified location.
     */
    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $this->locationService->update($location, $request->validated());

        return redirect()
            ->route('locations.index')
            ->with('success', "Location \"{$location->fresh()->name}\" updated successfully.");
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Request $request, Location $location): RedirectResponse|JsonResponse
    {
        $name = $location->name;
        $this->locationService->delete($location);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Location \"{$name}\" deleted successfully.",
            ]);
        }

        return redirect()
            ->route('locations.index')
            ->with('info', "Location \"{$name}\" deleted.");
    }
}
