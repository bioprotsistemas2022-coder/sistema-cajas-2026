<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsignacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caja_id' => ['required_without:codigo_interno', 'integer', 'exists:cajas,id'],
            'codigo_interno' => ['required_without:caja_id', 'string', 'exists:cajas,codigo_interno'],
            'consignatario_nombre' => ['nullable', 'string', 'max:255'],
            'fecha_consignacion' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'caja_id.required_without' => 'Debe enviar caja_id o codigo_interno.',
            'codigo_interno.required_without' => 'Debe enviar caja_id o codigo_interno.',
            'codigo_interno.exists' => 'No existe una caja con ese código interno.',
        ];
    }
}