<?php

namespace App\Http\Controllers;

use App\Models\Cirugia;
use App\Models\Caja;
use App\Models\Consumo;
use App\Services\BoxStateService;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    public function index()
    {
        $cirugias = Cirugia::where('tecnico_id', auth()->id())->get();
        return view('tecnico.dashboard', compact('cirugias'));
    }

    public function viewSurgery(Cirugia $cirugia, $token)
    {
        if ($cirugia->access_token !== $token) {
            abort(403);
        }

        return view('tecnico.surgery_view', compact('cirugia'));
    }

    public function llegado(Request $request, Cirugia $cirugia)
    {
        // Technician confirms boxes arrived OK
        foreach ($cirugia->cajas as $caja) {
            BoxStateService::transition($caja, 'EN CX', auth()->id() ?? $cirugia->tecnico_id, [
                'observaciones' => "Llegado en condiciones para CX"
            ]);
        }

        $cirugia->update([
            'status' => 'EN_CURSO',
            'start_time' => now()
        ]);

        return back()->with('success', 'Cirugía iniciada. El tiempo está corriendo.');
    }

    public function finalizar(Request $request, Cirugia $cirugia)
    {
        $request->validate([
            'consumos' => 'required|array',
            'observaciones' => 'nullable|string'
        ]);

        foreach ($cirugia->cajas as $caja) {
            // After CX, boxes go to TRANSITO
            BoxStateService::transition($caja, 'EN TRANSITO', auth()->id() ?? $cirugia->tecnico_id, [
                'observaciones' => "Cirugía finalizada. Pendiente de control de consumos."
            ]);

            // Save consumptions
            Consumo::create([
                'cirugia_id' => $cirugia->id,
                'caja_id' => $caja->id,
                'items' => $request->consumos[$caja->id] ?? [],
                'observaciones' => $request->observaciones
            ]);
        }

        $cirugia->update([
            'status' => 'COMPLETADA',
            'end_time' => now()
        ]);

        return back()->with('success', 'Cirugía finalizada y consumos reportados.');
    }
}
