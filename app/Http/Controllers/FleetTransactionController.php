<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleetTransaction\ImportFleetTransactionRequest;
use App\Http\Requests\FleetTransaction\StoreFleetTransactionRequest;
use App\Http\Requests\FleetTransaction\UpdateFleetTransactionRequest;
use App\Models\Fleet;
use App\Models\FleetTransaction;
use App\Services\FleetTransactionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FleetTransactionController extends Controller
{
    public function __construct(
        private readonly FleetTransactionService $fleetTransactionService,
    ) {}

    public function index(): View
    {
        return view('pages.fleet-transactions.index');
    }

    public function data(): JsonResponse
    {
        $query = $this->fleetTransactionService->getDataTableQuery();

        $fleetName = trim((string) request()->input('fleet_name'));
        if ($fleetName !== '') {
            $query->where('fleet_transactions.vehicle_name_snapshot', 'like', "%{$fleetName}%");
        }

        $customerName = trim((string) request()->input('customer_name'));
        if ($customerName !== '') {
            $query->whereHas('fleet.customer', function (Builder $query) use ($customerName): void {
                $query->where('name', 'like', "%{$customerName}%");
            });
        }

        $transactionDateStart = trim((string) request()->input('transaction_date_start'));
        if ($transactionDateStart !== '') {
            $query->whereDate('fleet_transactions.transaction_date', '>=', $transactionDateStart);
        }

        $transactionDateEnd = trim((string) request()->input('transaction_date_end'));
        if ($transactionDateEnd !== '') {
            $query->whereDate('fleet_transactions.transaction_date', '<=', $transactionDateEnd);
        }

        $this->applyNumericRangeFilter($query, 'fleet_transactions.odometer_km', 'odometer_min', 'odometer_max');
        $this->applyNumericRangeFilter($query, 'fleet_transactions.usage_l', 'usage_min', 'usage_max');
        $this->applyNumericRangeFilter($query, 'fleet_transactions.cost_rp', 'cost_min', 'cost_max');
        $this->applyNumericRangeFilter($query, 'fleet_transactions.refuel_l', 'refuel_min', 'refuel_max');
        $this->applyNumericRangeFilter($query, 'fleet_transactions.km_per_l', 'km_per_l_min', 'km_per_l_max');
        $this->applyNumericRangeFilter($query, 'fleet_transactions.l_per_km', 'l_per_km_min', 'l_per_km_max');

        $efficiencyStatus = trim((string) request()->input('efficiency_status'));
        if ($efficiencyStatus === 'normal') {
            $query
                ->whereHas('fleet', fn (Builder $query) => $query->where('has_fuel_sensor', true))
                ->whereBetween('fleet_transactions.km_per_l', [2.5, 4.5]);
        }

        if ($efficiencyStatus === 'abnormal') {
            $query
                ->whereHas('fleet', fn (Builder $query) => $query->where('has_fuel_sensor', true))
                ->whereNotNull('fleet_transactions.km_per_l')
                ->where(function (Builder $query): void {
                    $query
                        ->where('fleet_transactions.km_per_l', '<', 2.5)
                        ->orWhere('fleet_transactions.km_per_l', '>', 4.5);
                });
        }

        if ($efficiencyStatus === 'no_sensor') {
            $query->whereHas('fleet', function (Builder $query): void {
                $query->where('has_fuel_sensor', false)->orWhereNull('has_fuel_sensor');
            });
        }

        if ($efficiencyStatus === 'no_data') {
            $query
                ->whereHas('fleet', fn (Builder $query) => $query->where('has_fuel_sensor', true))
                ->whereNull('fleet_transactions.km_per_l');
        }

        return DataTables::eloquent($query)
            ->filter(function (Builder $query): void {
                $keyword = trim((string) request()->input('search.value'));

                if ($keyword === '') {
                    return;
                }

                $query->where(function (Builder $query) use ($keyword): void {
                    $query
                        ->where('fleet_transactions.vehicle_name_snapshot', 'like', "%{$keyword}%")
                        ->orWhere('fleet_transactions.transaction_date', 'like', "%{$keyword}%")
                        ->orWhere('fleet_transactions.odometer_km', 'like', "%{$keyword}%")
                        ->orWhere('fleet_transactions.usage_l', 'like', "%{$keyword}%")
                        ->orWhere('fleet_transactions.cost_rp', 'like', "%{$keyword}%")
                        ->orWhere('fleet_transactions.source_filename', 'like', "%{$keyword}%")
                        ->orWhereHas('fleet', function (Builder $query) use ($keyword): void {
                            $query
                                ->where('vehicle_name', 'like', "%{$keyword}%")
                                ->orWhere('device_name', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('fleet.customer', function (Builder $query) use ($keyword): void {
                            $query->where('name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->addColumn(
                'fleet_name',
                fn(FleetTransaction $transaction) => view('pages.fleet-transactions.columns.fleet', compact('transaction'))->render(),
            )
            ->addColumn('customer_name', fn(FleetTransaction $transaction) => $transaction->fleet?->customer?->name ?? '—')
            ->addColumn('transaction_date', fn(FleetTransaction $transaction) => $transaction->transaction_date?->locale('id')->translatedFormat('d F Y') ?? '—')
            ->addColumn('odometer_km', fn(FleetTransaction $transaction) => $this->formatNumber($transaction->odometer_km, 2) . ' km')
            ->addColumn('usage_l', fn(FleetTransaction $transaction) => $this->formatNumber($transaction->usage_l, 2) . ' L')
            ->addColumn('cost_rp', fn(FleetTransaction $transaction) => 'Rp ' . $this->formatNumber($transaction->cost_rp, 2))
            ->addColumn('refuel_l', fn(FleetTransaction $transaction) => $this->formatNullableNumber($transaction->refuel_l, 2, ' L'))
            ->addColumn('km_per_l', fn(FleetTransaction $transaction) => $this->formatNullableNumber($transaction->km_per_l, 2, ' km/L'))
            ->addColumn('l_per_km', fn(FleetTransaction $transaction) => $this->formatNullableNumber($transaction->l_per_km, 2, ' L/km'))
            ->addColumn('status', fn(FleetTransaction $transaction) => $this->formatEfficiencyStatus($transaction))
            ->addColumn('running_duration', fn(FleetTransaction $transaction) => $this->formatDuration($transaction->running_duration_seconds))
            ->addColumn('idle_duration', fn(FleetTransaction $transaction) => $this->formatDuration($transaction->idle_duration_seconds))
            ->addColumn('stop_duration', fn(FleetTransaction $transaction) => $this->formatDuration($transaction->stop_duration_seconds))
            ->addColumn(
                'action',
                fn(FleetTransaction $transaction) => view('pages.fleet-transactions.columns.action', compact('transaction'))->render(),
            )
            ->only([
                'action',
                'fleet_name',
                'customer_name',
                'transaction_date',
                'odometer_km',
                'usage_l',
                'cost_rp',
                'refuel_l',
                'km_per_l',
                'l_per_km',
                'status',
                'running_duration',
                'idle_duration',
                'stop_duration',
            ])
            ->rawColumns(['action', 'fleet_name', 'status'])
            ->toJson();
    }

    public function create(): View
    {
        return view('pages.fleet-transactions.create', [
            'fleets' => $this->selectableFleets(),
        ]);
    }

    public function store(StoreFleetTransactionRequest $request): RedirectResponse
    {
        $transaction = $this->fleetTransactionService->create($request->validated());

        return redirect()
            ->route('fleet-transactions.index')
            ->with('success', "Transaction for \"{$transaction->vehicle_name_snapshot}\" created successfully.");
    }

    public function import(ImportFleetTransactionRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $summary = $this->fleetTransactionService->import($request->file('file'));
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => collect($exception->errors())->flatten()->first() ?: 'The transaction file could not be imported.',
                'errors' => $exception->errors(),
            ], 422);
        }

        $message = sprintf(
            'Transaction import completed: %d processed (%d created, %d updated, and %d unchanged), %d skipped.',
            $summary['processed'],
            $summary['created'],
            $summary['updated'],
            $summary['unchanged'],
            $summary['skipped'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $summary,
            ]);
        }

        return redirect()
            ->route('fleet-transactions.index')
            ->with('success', $message);
    }

    public function recalculateEfficiency(Request $request): JsonResponse|RedirectResponse
    {
        $summary = $this->fleetTransactionService->recalculateEfficiencyMetrics();

        $message = sprintf(
            'Perhitungan ulang selesai: %d diperbarui, %d tidak berubah.',
            $summary['updated'],
            $summary['unchanged'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $summary,
            ]);
        }

        return redirect()
            ->route('fleet-transactions.index')
            ->with('success', $message);
    }

    public function edit(FleetTransaction $fleetTransaction): View
    {
        return view('pages.fleet-transactions.edit', [
            'transaction' => $fleetTransaction,
            'fleets' => $this->selectableFleets(),
        ]);
    }

    public function update(UpdateFleetTransactionRequest $request, FleetTransaction $fleetTransaction): RedirectResponse
    {
        $transaction = $this->fleetTransactionService->update($fleetTransaction, $request->validated());

        return redirect()
            ->route('fleet-transactions.index')
            ->with('success', "Transaction for \"{$transaction->vehicle_name_snapshot}\" updated successfully.");
    }

    public function destroy(Request $request, FleetTransaction $fleetTransaction): RedirectResponse|JsonResponse
    {
        $vehicleName = $fleetTransaction->vehicle_name_snapshot;
        $date = $fleetTransaction->transaction_date?->toDateString();

        $this->fleetTransactionService->delete($fleetTransaction);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Transaction for \"{$vehicleName}\" on {$date} deleted successfully.",
            ]);
        }

        return redirect()
            ->route('fleet-transactions.index')
            ->with('info', "Transaction for \"{$vehicleName}\" on {$date} deleted.");
    }

    private function selectableFleets()
    {
        return Fleet::query()
            ->with('customer')
            ->orderBy('vehicle_name')
            ->get(['id', 'customer_id', 'vehicle_name', 'device_name']);
    }

    private function applyNumericRangeFilter(Builder $query, string $column, string $minInput, string $maxInput): void
    {
        $minValue = $this->parseNullableDecimal(request()->input($minInput));
        if ($minValue !== null) {
            $query->where($column, '>=', $minValue);
        }

        $maxValue = $this->parseNullableDecimal(request()->input($maxInput));
        if ($maxValue !== null) {
            $query->where($column, '<=', $maxValue);
        }
    }

    private function parseNullableDecimal(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace(' ', '', $normalized);

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    private function formatNumber(mixed $value, int $precision): string
    {
        return number_format((float) $value, $precision, ',', '.');
    }

    private function formatNullableNumber(mixed $value, int $precision, string $suffix = ''): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return $this->formatNumber($value, $precision) . $suffix;
    }

    private function formatDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }

    private function formatEfficiencyStatus(FleetTransaction $transaction): string
    {
        if ($transaction->fleet?->has_fuel_sensor !== true) {
            return '<span class="badge badge-neutral"><span class="badge-dot"></span>Fuel Sensor belum terpasang</span>';
        }

        $kmPerLiter = $transaction->km_per_l;

        if ($kmPerLiter === null || $kmPerLiter === '') {
            return '—';
        }

        $val = (float) $kmPerLiter;

        if ($val >= 2.5 && $val <= 4.5) {
            return '<span class="badge badge-success"><span class="badge-dot"></span>Wajar</span>';
        }

        return '<span class="badge badge-danger"><span class="badge-dot"></span>Tidak Wajar</span>';
    }
}
