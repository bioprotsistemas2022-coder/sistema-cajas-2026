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
        if ($cirugia->status !== 'PENDIENTE') {
            return back()->with('error', 'La cirugía no está pendiente (estado: '.$cirugia->status.').');
        }
        $userId = auth()->id();
        $data = ['observaciones' => "Llegado en condiciones para CX"];

        if (!$userId) {
            $userId = $cirugia->tecnico_id;
            $responsable = $request->responsable_nombre ?? $cirugia->tecnico_nombre;
            if ($responsable) {
                $data['responsable_nombre'] = $responsable;
            }
        }

        try {
            foreach ($cirugia->cajas as $caja) {
                if (!BoxStateService::canTransition($caja, 'EN CX')) {
                    \Illuminate\Support\Facades\Log::warning("Llegado: caja {$caja->id} {$caja->codigo_interno} no puede EN CX desde {$caja->estado}");
                }
                BoxStateService::transition($caja, 'EN CX', $userId, $data);
            }

            $cirugia->update([
                'status' => 'EN_CURSO',
                'start_time' => now()
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Llegado CX {$cirugia->id} error: ".$e->getMessage());
            return back()->with('error', 'Error al iniciar: '.$e->getMessage());
        }

        return back()->with('success', 'Cirugía iniciada. El tiempo está corriendo.');
    }

    public function finalizar(Request $request, Cirugia $cirugia)
    {
        $request->validate([
            'consumos' => 'nullable|array',
            'consumos.*' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        if ($cirugia->status !== 'EN_CURSO') {
            return back()->with('error', 'La cirugía no está en curso (estado actual: '.$cirugia->status.').');
        }

        $userId = auth()->id();
        $data = ['observaciones' => "Cirugía finalizada. Pendiente de control de consumos."];

        if (!$userId) {
            $userId = $cirugia->tecnico_id;
            $responsable = $request->responsable_nombre ?? $cirugia->tecnico_nombre;
            if ($responsable) {
                $data['responsable_nombre'] = $responsable;
            }
        }

        try {
            foreach ($cirugia->cajas as $caja) {
                if (!BoxStateService::canTransition($caja, 'CX FINALIZADA')) {
                    \Illuminate\Support\Facades\Log::warning("Finalizar: caja {$caja->id} {$caja->codigo_interno} no puede CX FINALIZADA desde {$caja->estado}");
                }
                BoxStateService::transition($caja, 'CX FINALIZADA', $userId, $data);

                $raw = $request->input('consumos.'.$caja->id, '');
                $items = is_string($raw) ? [trim($raw)] : (is_array($raw) ? $raw : []);
                $items = array_filter($items, fn($v) => trim((string)$v) !== '');

                Consumo::create([
                    'cirugia_id' => $cirugia->id,
                    'caja_id' => $caja->id,
                    'items' => $items,
                    'observaciones' => $request->observaciones
                ]);
            }

            if ($cirugia->cajas->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning("Finalizar CX {$cirugia->id} sin cajas asociadas");
            }

            $cirugia->update([
                'status' => 'COMPLETADA',
                'end_time' => now()
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Finalizar CX {$cirugia->id} error: ".$e->getMessage());
            return back()->with('error', 'Error al finalizar: '.$e->getMessage());
        }

        return back()->with('success', 'Cirugía finalizada y consumos reportados.');
    }
}
