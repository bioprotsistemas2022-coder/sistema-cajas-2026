<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCirugiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plc_cod' => ['nullable', 'string', 'max:255'],
            'bioimplant_id' => ['nullable', 'string', 'max:255'],
            'paciente' => ['required', 'string', 'max:255'],
            'medico' => ['required', 'string', 'max:255'],
            'fecha_cx' => ['required', 'date'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'tecnico_id' => ['nullable', 'exists:users,id'],
            'tecnico_nombre' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['PENDIENTE', 'EN_CURSO', 'COMPLETADA', 'CANCELADA', 'POSTPUESTA'])],
            'cajas' => ['nullable', 'array'],
            'cajas.*' => ['integer', 'exists:cajas,id'],
        ];
    }
}