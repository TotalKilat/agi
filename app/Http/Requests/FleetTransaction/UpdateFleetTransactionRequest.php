<?php

namespace App\Http\Requests\FleetTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFleetTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->nullableNumericFields());
    }

    public function rules(): array
    {
        $transaction = $this->route('fleet_transaction');

        return [
            'fleet_id' => ['required', 'string', Rule::exists('fleets', 'id')],
            'transaction_date' => [
                'required',
                'date',
                Rule::unique('fleet_transactions', 'transaction_date')
                    ->where(fn ($query) => $query->where('fleet_id', $this->input('fleet_id')))
                    ->ignore($transaction?->id),
            ],
            'odometer_km' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'initial_volume_l' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'final_volume_l' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'usage_l' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'cost_rp' => ['required', 'numeric', 'min:0', 'max:99999999999999.99'],
            'idle_usage_l' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'km_per_l' => ['nullable', 'numeric', 'min:0', 'max:99999999.9999'],
            'l_per_km' => ['nullable', 'numeric', 'min:0', 'max:99999999.9999'],
            'cost_per_km' => ['nullable', 'numeric', 'min:0', 'max:999999999999.9999'],
            'refuel_l' => ['nullable', 'numeric', 'min:0', 'max:999999999.999'],
            'refuel_times' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'running_duration_seconds' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'idle_duration_seconds' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'stop_duration_seconds' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nullableNumericFields(): array
    {
        return collect([
            'initial_volume_l',
            'final_volume_l',
            'idle_usage_l',
            'km_per_l',
            'l_per_km',
            'cost_per_km',
            'refuel_l',
            'refuel_times',
            'running_duration_seconds',
            'idle_duration_seconds',
            'stop_duration_seconds',
        ])
            ->mapWithKeys(fn (string $field): array => [
                $field => $this->input($field) === '' ? null : $this->input($field),
            ])
            ->all();
    }
}
