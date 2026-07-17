<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Services\BoxStateService;
use Illuminate\Http\Request;

class LogisticaController extends Controller
{
    public function index()
    {
        $cajasPendientesRetiro = Caja::where('estado', 'CX FINALIZADA')->get();
        $cajasEnTransito = Caja::where('estado', 'EN TRANSITO VUELTA')->get();
        return view('logistica.dashboard', compact('cajasPendientesRetiro', 'cajasEnTransito'));
    }

    public function retirar(Request $request, Caja $caja)
    {
        if (!BoxStateService::canTransition($caja, 'EN TRANSITO VUELTA')) {
            return back()->with('error', 'Esta caja no puede ser retirada desde su estado actual.');
        }

        BoxStateService::transition($caja, 'EN TRANSITO VUELTA', auth()->id(), [
            'observaciones' => 'Caja retirada del servicio (hospital/sanatorio). En tránsito de vuelta.'
        ]);

        return back()->with('success', "{$caja->nombre} retirada del servicio. En tránsito de vuelta.");
    }
}
