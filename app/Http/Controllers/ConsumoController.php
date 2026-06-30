<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\TokensAccion;
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

    public function viewToken($token)
    {
        $t = TokensAccion::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('recepcion.token', compact('t'));
    }

    public function confirmarToken(Request $request, $token)
    {
        $t = TokensAccion::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $caja = $t->caja;

        if ($t->accion === 'consumo.controlar') {
            BoxStateService::transition($caja, 'PENDIENTE', null, [
                'responsable_nombre' => $t->responsable_nombre,
                'observaciones' => "Recepción externa. Caja recibida para control."
            ]);
        } elseif ($t->accion === 'consumo.finalizar') {
            $resultado = $t->params['resultado'] ?? 'ok';
            if ($resultado === 'ok') {
                BoxStateService::transition($caja, 'ACONDICIONAMIENTO', null, [
                    'responsable_nombre' => $t->responsable_nombre,
                    'observaciones' => "Control externo finalizado OK. Pasa a lavado."
                ]);
            } else {
                BoxStateService::transition($caja, 'EN REPARACION', null, [
                    'responsable_nombre' => $t->responsable_nombre,
                    'observaciones' => "FALLA EXTERNA DETECTADA: " . ($t->params['observaciones'] ?? 'Sin detalle')
                ]);
            }
        }

        $t->update(['used_at' => now()]);

        return view('recepcion.hecho', compact('caja'));
    }
}
