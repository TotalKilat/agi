<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Fleet;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_crud_and_datatable_count(): void
    {
        $this->get(route('locations.index'))
            ->assertOk()
            ->assertSee('Locations')
            ->assertSee('New Location');

        $this->post(route('locations.store'), [
            'name' => 'Samarinda',
        ])->assertRedirect(route('locations.index'));

        $location = Location::query()->where('name', 'Samarinda')->firstOrFail();
        $customer = Customer::query()->create([
            'name' => 'AGI Customer',
            'username' => 'agi',
            'email' => 'agi@example.com',
            'password' => 'secret',
            'is_active' => true,
        ]);

        Fleet::query()->create([
            'customer_id' => $customer->id,
            'location_id' => $location->id,
            'vehicle_name' => 'B 2029 SJO',
            'device_name' => '60697058200041',
            'is_active' => true,
        ]);

        $this->getJson(route('locations.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]))
            ->assertOk()
            ->assertJsonPath('draw', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.fleets_count', '1');

        $this->put(route('locations.update', $location), [
            'name' => 'Balikpapan',
        ])->assertRedirect(route('locations.index'));

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Balikpapan',
        ]);

        $this->delete(route('locations.destroy', $location))
            ->assertRedirect(route('locations.index'));

        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
        $this->assertDatabaseHas('fleets', [
            'vehicle_name' => 'B 2029 SJO',
            'location_id' => null,
        ]);
    }

    public function test_fleet_form_can_assign_location(): void
    {
        $customer = Customer::query()->create([
            'name' => 'AGI Customer',
            'username' => 'agi',
            'email' => 'agi@example.com',
            'password' => 'secret',
            'is_active' => true,
        ]);
        $location = Location::query()->create(['name' => 'Sangatta']);

        $this->get(route('fleets.create'))
            ->assertOk()
            ->assertSee('Location')
            ->assertSee('Sangatta');

        $this->post(route('fleets.store'), [
            'customer_id' => $customer->id,
            'location_id' => $location->id,
            'vehicle_name' => 'B 3030 LOC',
            'device_name' => 'device-location',
            'has_fuel_sensor' => '0',
            'is_active' => '1',
        ])->assertRedirect(route('fleets.index'));

        $this->assertDatabaseHas('fleets', [
            'customer_id' => $customer->id,
            'location_id' => $location->id,
            'vehicle_name' => 'B 3030 LOC',
            'device_name' => 'device-location',
        ]);
    }
}
