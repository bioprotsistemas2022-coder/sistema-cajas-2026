<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'codigo_interno' => ['required', 'string', 'max:255', 'unique:cajas,codigo_interno'],
            'estado' => ['sometimes', Rule::in([
                'DISPONIBLE', 'CONSIGNADA', 'PENDIENTE_DESPACHO', 'EN ESTERILIZADORA', 'EN CX', 'CX FINALIZADA',
                'EN TRANSITO VUELTA', 'PENDIENTE', 'ACONDICIONAMIENTO', 'EN REPARACION', 'BAJA',
            ])],
            'pdf_path' => ['nullable', 'string'],
            'imagen_salida_path' => ['nullable', 'string'],
        ];
    }
}