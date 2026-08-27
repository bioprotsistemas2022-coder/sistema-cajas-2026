<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VincularCxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plc_cod' => ['required', 'string', 'max:255'],
            'paciente' => ['required', 'string', 'max:255'],
            'medico' => ['required', 'string', 'max:255'],
            'fecha_cx' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'hospital' => ['nullable', 'string', 'max:500'],
            'cajas' => ['required', 'array', 'min:1'],
            'cajas.*' => ['required', 'string', 'max:255'],
            'external_nco_cod' => ['nullable', 'integer'],
            'external_nco_cods' => ['nullable', 'array'],
            'external_nco_cods.*' => ['integer'],
            'implantes' => ['nullable', 'array'],
            'implantes.*.ArtCod' => ['nullable', 'string'],
            'implantes.*.ArtDes' => ['nullable', 'string'],
            'implantes.*.ImplDsc' => ['nullable', 'string'],
            'implantes.*.ImplCan' => ['nullable'],
            'implantes.*.ImplLot' => ['nullable', 'string'],
            'implantes.*.ImplVen' => ['nullable', 'string'],
            'implantes.*.ImplSer' => ['nullable', 'string'],
            'detalles' => ['nullable', 'array'],
        ];
    }
}
