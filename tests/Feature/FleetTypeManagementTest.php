<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Fleet;
use App\Models\FleetType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleetTypeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_type_crud_and_datatable_count(): void
    {
        $this->get(route('fleet-types.index'))
            ->assertOk()
            ->assertSee('Fleet Types')
            ->assertSee('New Fleet Type');

        $this->post(route('fleet-types.store'), [
            'name' => 'Dump Truck',
        ])->assertRedirect(route('fleet-types.index'));

        $fleetType = FleetType::query()->where('name', 'Dump Truck')->firstOrFail();
        $customer = Customer::query()->create([
            'name' => 'AGI Customer',
            'username' => 'agi',
            'email' => 'agi@example.com',
            'password' => 'secret',
            'is_active' => true,
        ]);

        Fleet::query()->create([
            'customer_id' => $customer->id,
            'fleet_type_id' => $fleetType->id,
            'vehicle_name' => 'B 2029 SJO',
            'device_name' => '60697058200041',
            'is_active' => true,
        ]);

        $this->getJson(route('fleet-types.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]))
            ->assertOk()
            ->assertJsonPath('draw', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.fleets_count', '1');

        $this->put(route('fleet-types.update', $fleetType), [
            'name' => 'Light Truck',
        ])->assertRedirect(route('fleet-types.index'));

        $this->assertDatabaseHas('fleet_type', [
            'id' => $fleetType->id,
            'name' => 'Light Truck',
        ]);

        $this->delete(route('fleet-types.destroy', $fleetType))
            ->assertRedirect(route('fleet-types.index'));

        $this->assertDatabaseMissing('fleet_type', ['id' => $fleetType->id]);
        $this->assertDatabaseHas('fleets', [
            'vehicle_name' => 'B 2029 SJO',
            'fleet_type_id' => null,
        ]);
    }

    public function test_fleet_form_can_assign_fleet_type(): void
    {
        $customer = Customer::query()->create([
            'name' => 'AGI Customer',
            'username' => 'agi',
            'email' => 'agi@example.com',
            'password' => 'secret',
            'is_active' => true,
        ]);
        $fleetType = FleetType::query()->create(['name' => 'Trailer']);

        $this->get(route('fleets.create'))
            ->assertOk()
            ->assertSee('Fleet Type')
            ->assertSee('Trailer');

        $this->post(route('fleets.store'), [
            'customer_id' => $customer->id,
            'fleet_type_id' => $fleetType->id,
            'vehicle_name' => 'B 3030 TRL',
            'device_name' => 'device-trailer',
            'has_fuel_sensor' => '0',
            'is_active' => '1',
        ])->assertRedirect(route('fleets.index'));

        $this->assertDatabaseHas('fleets', [
            'customer_id' => $customer->id,
            'fleet_type_id' => $fleetType->id,
            'vehicle_name' => 'B 3030 TRL',
            'device_name' => 'device-trailer',
        ]);
    }
}
