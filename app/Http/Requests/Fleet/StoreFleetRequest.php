<?php

namespace App\Http\Requests\Fleet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFleetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_fuel_sensor' => $this->boolean('has_fuel_sensor'),
            'fuel_sensor_installed_at' => $this->boolean('has_fuel_sensor')
                ? $this->input('fuel_sensor_installed_at')
                : null,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'string', Rule::exists('customers', 'id')],
            'vehicle_name' => ['required', 'string', 'max:200'],
            'device_name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('fleets', 'device_name')
                    ->where(fn ($query) => $query->where('customer_id', $this->input('customer_id'))),
            ],
            'has_fuel_sensor' => ['boolean'],
            'fuel_sensor_installed_at' => ['nullable', Rule::requiredIf($this->boolean('has_fuel_sensor')), 'date', 'before_or_equal:today'],
            'is_active' => ['boolean'],
        ];
    }
}
