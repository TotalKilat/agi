<?php

namespace App\Http\Requests\FleetTransaction;

use Illuminate\Foundation\Http\FormRequest;

class ImportFleetTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:102400', 'extensions:xls,html,htm'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.extensions' => 'The transaction file must use an xls, html, or htm extension.',
            'file.max' => 'The transaction file must not be larger than 100 MB.',
        ];
    }
}
