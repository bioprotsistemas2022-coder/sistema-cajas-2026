<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Services\BoxStateService;
use Illuminate\Http\Request;

class AcondicionadorController extends Controller
{
    public function index()
    {
        $cajas = Caja::where('estado', 'ACONDICIONAMIENTO')->get();
        return view('acondicionador.dashboard', compact('cajas'));
    }

    public function disponibilizar(Request $request, Caja $caja)
    {
        BoxStateService::transition($caja, 'DISPONIBLE', auth()->id(), [
            'observaciones' => "Lavado finalizado. Caja disponible para nuevo uso."
        ]);

        return back()->with('success', 'Caja disponibilizada correctamente.');
    }
}
