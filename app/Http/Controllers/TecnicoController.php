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
        $userId = auth()->id();
        $data = ['observaciones' => "Llegado en condiciones para CX"];

        if (!$userId) {
            $userId = $cirugia->tecnico_id;
            $responsable = $request->responsable_nombre ?? $cirugia->tecnico_nombre;
            if ($responsable) {
                $data['responsable_nombre'] = $responsable;
            }
        }

        foreach ($cirugia->cajas as $caja) {
            BoxStateService::transition($caja, 'EN CX', $userId, $data);
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

        $userId = auth()->id();
        $data = ['observaciones' => "Cirugía finalizada. Pendiente de control de consumos."];

        if (!$userId) {
            $userId = $cirugia->tecnico_id;
            $responsable = $request->responsable_nombre ?? $cirugia->tecnico_nombre;
            if ($responsable) {
                $data['responsable_nombre'] = $responsable;
            }
        }

        foreach ($cirugia->cajas as $caja) {
            BoxStateService::transition($caja, 'CX FINALIZADA', $userId, $data);

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
