<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cirugia_id' => ['required', 'exists:cirugias,id'],
            'caja_id' => ['required', 'exists:cajas,id'],
            'items' => ['required', 'array'],
            'items.*.nombre' => ['required', 'string'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}