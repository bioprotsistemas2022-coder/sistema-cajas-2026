<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cirugia_id' => ['sometimes', 'exists:cirugias,id'],
            'caja_id' => ['sometimes', 'exists:cajas,id'],
            'items' => ['sometimes', 'array'],
            'items.*.nombre' => ['required', 'string'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}