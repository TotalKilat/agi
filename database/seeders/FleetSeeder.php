<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Fleet;
use Illuminate\Database\Seeder;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all()->keyBy('username');

        $fleets = [
            [
                'customer_id'  => $customers['alexkim']->id,
                'vehicle_name' => 'Toyota Hiace 2024',
                'device_name'   => 'GT06N-001',
            ],
            [
                'customer_id'  => $customers['alexkim']->id,
                'vehicle_name' => 'Isuzu Elf 2023',
                'device_name'   => 'GT06N-002',
            ],
            [
                'customer_id'  => $customers['sarahchen']->id,
                'vehicle_name' => 'Mitsubishi Fuso 2025',
                'device_name'   => 'COBAN-101',
            ],
            [
                'customer_id'  => $customers['marcorossi']->id,
                'vehicle_name' => 'Iveco Daily 2024',
                'device_name'   => 'TK303G-010',
            ],
            [
                'customer_id'  => $customers['marcorossi']->id,
                'vehicle_name' => 'Fiat Ducato 2023',
                'device_name'   => 'TK303G-011',
            ],
        ];

        foreach ($fleets as $data) {
            Fleet::query()->updateOrCreate(
                [
                    'customer_id'  => $data['customer_id'],
                    'device_name'  => $data['device_name'],
                ],
                $data,
            );
        }
    }
}
