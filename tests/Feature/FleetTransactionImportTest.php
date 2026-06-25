<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Fleet;
use App\Models\FleetType;
use App\Models\FleetTransaction;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FleetTransactionImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_index_and_create_pages_are_available(): void
    {
        $this->createFleet('B 9882 SDD');

        $this->get(route('fleet-transactions.index'))
            ->assertOk()
            ->assertSee('Fleet Transactions')
            ->assertSee('Upload File')
            ->assertSee(route('fleet-transactions.import'));

        $this->get(route('fleet-transactions.create'))
            ->assertOk()
            ->assertSee('Create Fleet Transaction')
            ->assertSee('B 9882 SDD');
    }

    public function test_daily_performance_html_xls_import_creates_transactions_linked_to_fleets(): void
    {
        $fleetA = $this->createFleet('B 9882 SDD');
        $fleetB = $this->createFleet('B 9037 SDE');

        $this->post(route('fleet-transactions.import'), [
            'file' => $this->sampleUpload(),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.created', 2)
            ->assertJsonPath('data.updated', 0)
            ->assertJsonPath('data.unchanged', 0)
            ->assertJsonPath('data.processed', 2)
            ->assertJsonPath('data.skipped', 0);

        $this->assertDatabaseHas('fleet_transactions', [
            'fleet_id' => $fleetA->id,
            'transaction_date' => '2026-06-10 00:00:00',
            'vehicle_name_snapshot' => 'B 9882 SDD',
            'odometer_km' => 0,
            'usage_l' => 0,
            'cost_rp' => 0,
            'stop_duration_seconds' => 86400,
        ]);
        $this->assertDatabaseHas('fleet_transactions', [
            'fleet_id' => $fleetB->id,
            'transaction_date' => '2026-06-10 00:00:00',
            'vehicle_name_snapshot' => 'B 9037 SDE',
            'odometer_km' => 16,
            'usage_l' => 12.79,
            'cost_rp' => 39649,
            'running_duration_seconds' => 5299,
            'stop_duration_seconds' => 81101,
        ]);
        $this->assertDatabaseCount('fleet_transactions', 2);

        $this->post(route('fleet-transactions.import'), [
            'file' => $this->sampleUpload(),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.created', 0)
            ->assertJsonPath('data.updated', 0)
            ->assertJsonPath('data.unchanged', 2)
            ->assertJsonPath('data.processed', 2)
            ->assertJsonPath('data.skipped', 0);
    }

    public function test_transaction_data_can_be_filtered_by_multiple_fleet_types_and_locations(): void
    {
        $truck = FleetType::query()->create(['name' => 'Truck']);
        $pickup = FleetType::query()->create(['name' => 'Pickup']);
        $poolA = Location::query()->create(['name' => 'Pool A']);
        $poolB = Location::query()->create(['name' => 'Pool B']);

        $this->createFleet('B 9882 SDD', null, [
            'fleet_type_id' => $truck->id,
            'location_id' => $poolA->id,
        ]);
        $this->createFleet('B 9037 SDE', null, [
            'fleet_type_id' => $pickup->id,
            'location_id' => $poolB->id,
        ]);

        $this->post(route('fleet-transactions.import'), [
            'file' => $this->sampleUpload(),
        ], ['Accept' => 'application/json'])->assertOk();

        $this->getJson(route('fleet-transactions.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'fleet_type_ids' => [$truck->id],
            'location_ids' => [$poolA->id],
            'columns' => $this->dataTableColumns(),
        ]))
            ->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertSee('Truck')
            ->assertSee('Pool A');

        $this->getJson(route('fleet-transactions.data', [
            'draw' => 2,
            'start' => 0,
            'length' => 10,
            'fleet_type_ids' => [$truck->id, $pickup->id],
            'location_ids' => [$poolA->id, $poolB->id],
            'columns' => $this->dataTableColumns(),
        ]))
            ->assertOk()
            ->assertJsonPath('recordsFiltered', 2)
            ->assertSee('Truck')
            ->assertSee('Pickup')
            ->assertSee('Pool A')
            ->assertSee('Pool B');
    }

    public function test_import_skips_unknown_vehicle_name_and_processes_valid_rows(): void
    {
        $fleet = $this->createFleet('B 9882 SDD');

        $this
            ->post(route('fleet-transactions.import'), [
                'file' => $this->sampleUpload(),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.processed', 1)
            ->assertJsonPath('data.skipped', 1);

        $this->assertDatabaseHas('fleet_transactions', [
            'fleet_id' => $fleet->id,
            'transaction_date' => '2026-06-10 00:00:00',
            'vehicle_name_snapshot' => 'B 9882 SDD',
        ]);
        $this->assertDatabaseCount('fleet_transactions', 1);
    }

    public function test_import_skips_ambiguous_master_names_and_duplicate_vehicle_date_rows(): void
    {
        $this->createFleet('B 9882 SDD', 'B9882A');
        $this->createFleet('B 9882 SDD', 'B9882B');
        $fleet = $this->createFleet('B 9037 SDE');

        $this
            ->post(route('fleet-transactions.import'), [
                'file' => $this->sampleUpload($this->duplicateRowsSampleHtml()),
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.created', 2)
            ->assertJsonPath('data.processed', 2)
            ->assertJsonPath('data.skipped', 2);

        $this->assertDatabaseHas('fleet_transactions', [
            'fleet_id' => $fleet->id,
            'transaction_date' => '2026-06-10 00:00:00',
            'vehicle_name_snapshot' => 'B 9037 SDE',
        ]);
        $this->assertDatabaseHas('fleet_transactions', [
            'fleet_id' => $fleet->id,
            'transaction_date' => '2026-06-11 00:00:00',
            'vehicle_name_snapshot' => 'B 9037 SDE',
        ]);
        $this->assertDatabaseCount('fleet_transactions', 2);
    }

    public function test_transaction_can_be_created_updated_deleted_and_listed(): void
    {
        $fleet = $this->createFleet('B 9882 SDD');

        $this->post(route('fleet-transactions.store'), [
            'fleet_id' => $fleet->id,
            'transaction_date' => '2026-06-10',
            'odometer_km' => 12.5,
            'initial_volume_l' => 70.37,
            'final_volume_l' => 68.12,
            'usage_l' => 2.25,
            'cost_rp' => 6975,
            'idle_usage_l' => 0,
            'km_per_l' => 5.5555,
            'l_per_km' => 0.18,
            'cost_per_km' => 558,
            'refuel_l' => '',
            'refuel_times' => '',
            'running_duration_seconds' => 1200,
            'idle_duration_seconds' => '',
            'stop_duration_seconds' => 85200,
        ])->assertRedirect(route('fleet-transactions.index'));

        $transaction = FleetTransaction::query()->firstOrFail();

        $this->getJson(route('fleet-transactions.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'B 9882 SDD', 'regex' => 'false'],
            'columns' => [
                [
                    'data' => 'fleet_name',
                    'name' => 'vehicle_name_snapshot',
                    'searchable' => 'true',
                    'orderable' => 'true',
                    'search' => ['value' => '', 'regex' => 'false'],
                ],
            ],
        ]))
            ->assertOk()
            ->assertJsonPath('recordsFiltered', 1)
            ->assertSee('B 9882 SDD')
            ->assertJsonMissingPath('data.0.id');

        $this->put(route('fleet-transactions.update', $transaction), [
            'fleet_id' => $fleet->id,
            'transaction_date' => '2026-06-11',
            'odometer_km' => 13,
            'usage_l' => 2.5,
            'cost_rp' => 7750,
        ])->assertRedirect(route('fleet-transactions.index'));

        $this->assertDatabaseHas('fleet_transactions', [
            'id' => $transaction->id,
            'transaction_date' => '2026-06-11 00:00:00',
            'usage_l' => 2.5,
        ]);

        $this->deleteJson(route('fleet-transactions.destroy', $transaction))
            ->assertOk()
            ->assertJsonPath('message', 'Transaction for "B 9882 SDD" on 2026-06-11 deleted successfully.');

        $this->assertSoftDeleted('fleet_transactions', ['id' => $transaction->id]);
    }

    private function createFleet(string $vehicleName, ?string $deviceName = null, array $attributes = []): Fleet
    {
        $customer = Customer::query()->first() ?: Customer::query()->create([
            'name' => 'AGI Customer',
            'username' => 'agi',
            'email' => 'agi@example.com',
            'password' => 'secret',
            'is_active' => true,
        ]);

        return Fleet::query()->create([
            'customer_id' => $customer->id,
            'vehicle_name' => $vehicleName,
            'device_name' => $deviceName ?? str_replace(' ', '', $vehicleName),
            'is_active' => true,
        ] + $attributes);
    }

    private function dataTableColumns(): array
    {
        return collect([
            'fleet_name',
            'transaction_date',
            'customer_name',
            'fleet_type_name',
            'location_name',
            'odometer_km',
            'usage_l',
            'cost_rp',
        ])->map(fn(string $column): array => [
            'data' => $column,
            'name' => $column,
            'searchable' => 'true',
            'orderable' => 'false',
            'search' => ['value' => '', 'regex' => 'false'],
        ])->all();
    }

    private function sampleUpload(?string $html = null): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'fleet-transactions-');
        file_put_contents($path, $html ?? $this->sampleHtml());

        return new UploadedFile($path, 'Daily Performance Analysis Report PER GRUP.xls', 'application/vnd.ms-excel', null, true);
    }

    private function sampleHtml(): string
    {
        return <<<'HTML'
<html><body>
<table><tr><td>[2026-06-10 00:00:00-2026-06-11 23:59:59]</td></tr></table>
<table>
<tr>
<th>Device Name</th><th>Date & Time</th><th>Odometer(Km)</th><th>Initial Volume(L)</th><th>Final Volume(L)</th><th>Usage (L)</th><th>Cost (Rp)</th><th>Idle Usage (L)</th><th>1 Km /L</th><th>1 L /Km</th><th>1 Km /Cost</th><th>Refuel (L)</th><th>Refuel (Times)</th><th>Running (HH:mm:ss)</th><th>Idle (HH:mm:ss)</th><th>Stop (HH:mm:ss)</th>
</tr>
<tr><td>B 9882 SDD</td><td>2026-06-10 00:00:00</td><td>0.00</td><td>70.37</td><td>70.58</td><td>0.00</td><td>0.00</td><td>0.0</td><td>0.0</td><td>0.0</td><td>0.00</td><td></td><td></td><td></td><td></td><td>24:00:00</td></tr>
<tr><td>B 9037 SDE</td><td>2026-06-10 00:00:00</td><td>16.00</td><td>95.34</td><td>94.83</td><td>12.79</td><td>39649.00</td><td>0.0</td><td>0.8</td><td>1.2</td><td>2485.34</td><td></td><td></td><td>01:28:19</td><td></td><td>22:31:41</td></tr>
<tr><td></td><td></td><td>16.00</td><td></td><td></td><td>12.79</td><td>39649.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td>01:28:19</td><td></td><td>46:31:41</td></tr>
</table>
</body></html>
HTML;
    }

    private function duplicateRowsSampleHtml(): string
    {
        return <<<'HTML'
<html><body>
<table><tr><td>[2026-06-10 00:00:00-2026-06-11 23:59:59]</td></tr></table>
<table>
<tr>
<th>Device Name</th><th>Date & Time</th><th>Odometer(Km)</th><th>Initial Volume(L)</th><th>Final Volume(L)</th><th>Usage (L)</th><th>Cost (Rp)</th><th>Idle Usage (L)</th><th>1 Km /L</th><th>1 L /Km</th><th>1 Km /Cost</th><th>Refuel (L)</th><th>Refuel (Times)</th><th>Running (HH:mm:ss)</th><th>Idle (HH:mm:ss)</th><th>Stop (HH:mm:ss)</th>
</tr>
<tr><td>B 9882 SDD</td><td>2026-06-10 00:00:00</td><td>10.00</td><td></td><td></td><td>5.00</td><td>10000.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td>01:00:00</td><td></td><td>23:00:00</td></tr>
<tr><td>B 9037 SDE</td><td>2026-06-10 00:00:00</td><td>16.00</td><td></td><td></td><td>12.79</td><td>39649.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td>01:28:19</td><td></td><td>22:31:41</td></tr>
<tr><td>B 9037 SDE</td><td>2026-06-10 12:00:00</td><td>20.00</td><td></td><td></td><td>14.00</td><td>42000.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td>02:00:00</td><td></td><td>22:00:00</td></tr>
<tr><td>B 9037 SDE</td><td>2026-06-11 00:00:00</td><td>18.00</td><td></td><td></td><td>13.00</td><td>41000.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td>01:40:00</td><td></td><td>22:20:00</td></tr>
</table>
</body></html>
HTML;
    }
}
