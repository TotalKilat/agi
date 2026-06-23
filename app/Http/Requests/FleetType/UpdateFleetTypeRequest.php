<?php

namespace App\Http\Requests\FleetType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFleetTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name', '')),
        ]);
    }

    public function rules(): array
    {
        $fleetType = $this->route('fleet_type');

        return [
            'name' => ['required', 'string', 'max:200', Rule::unique('fleet_type', 'name')->ignore($fleetType?->id)],
        ];
    }
}
