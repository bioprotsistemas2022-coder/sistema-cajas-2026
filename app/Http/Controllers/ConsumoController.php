<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Services\BoxStateService;
use Illuminate\Http\Request;

class ConsumoController extends Controller
{
    public function index()
    {
        $cajasEnTransito = Caja::where('estado', 'EN TRANSITO')->get();
        $cajasPendientes = Caja::where('estado', 'PENDIENTE')->get();
        return view('consumo.dashboard', compact('cajasEnTransito', 'cajasPendientes'));
    }

    public function controlar(Request $request, Caja $caja)
    {
        BoxStateService::transition($caja, 'PENDIENTE', auth()->id(), [
            'observaciones' => "Iniciando control de caja recibida."
        ]);

        return back()->with('success', 'Caja en proceso de control.');
    }

    public function finalizar(Request $request, Caja $caja)
    {
        $request->validate([
            'resultado' => 'required|in:ok,falla',
            'observaciones' => 'nullable|string'
        ]);

        if ($request->resultado === 'ok') {
            BoxStateService::transition($caja, 'ACONDICIONAMIENTO', auth()->id(), [
                'observaciones' => "Control finalizado correctamente. Pasa a lavado."
            ]);
            return back()->with('success', 'Control finalizado. Caja enviada a Acondicionamiento.');
        } else {
            BoxStateService::transition($caja, 'EN REPARACION', auth()->id(), [
                'observaciones' => "FALLA DETECTADA: " . $request->observaciones
            ]);
            return back()->with('warning', 'Caja reportada con fallas y enviada a Reparación.');
        }
    }
}
