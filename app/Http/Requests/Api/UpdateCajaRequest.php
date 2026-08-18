<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'codigo_interno' => [
                'sometimes', 'string', 'max:255',
                Rule::unique('cajas', 'codigo_interno')->ignore($this->route('caja')),
            ],
            'estado' => ['sometimes', Rule::in([
                'DISPONIBLE', 'EN ESTERILIZADORA', 'EN CX', 'EN TRANSITO',
                'PENDIENTE', 'ACONDICIONAMIENTO', 'EN REPARACION', 'BAJA', 'CONSIGNADA',
            ])],
            'pdf_path' => ['nullable', 'string'],
            'imagen_salida_path' => ['nullable', 'string'],
        ];
    }
}