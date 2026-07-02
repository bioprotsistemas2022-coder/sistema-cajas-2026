<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaImagen;
use App\Models\Cirugia;
use App\Models\TokensAccion;
use App\Models\User;
use App\Services\BoxStateService;
use App\Services\ProcedureApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DepositoController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('imagenes')->get();
        $tecnicos = User::where('role', 'tecnico')->get();
        $tokensActivos = TokensAccion::whereNull('used_at')
            ->where('expires_at', '>', now())
            ->get()
            ->keyBy('caja_id');
        return view('deposito.dashboard', compact('cajas', 'tecnicos', 'tokensActivos'));
    }

    public function egreso(Request $request, Caja $caja)
    {
        $request->validate([
            'paciente' => 'required|string',
            'medico' => 'required|string',
            'plc_cod' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        if (!BoxStateService::canTransition($caja, 'EN ESTERILIZADORA')) {
            return back()->with('error', 'La caja no puede pasar a Esterilizadora desde su estado actual.');
        }

        $cirugiaData = [
            'paciente' => $request->paciente,
            'medico' => $request->medico,
            'plc_cod' => $request->plc_cod,
            'observaciones' => $request->observaciones,
            'fecha_cx' => now(),
            'status' => 'PENDIENTE'
        ];

        if ($request->tipo_tecnico === 'externo') {
            $cirugiaData['tecnico_id'] = null;
            $cirugiaData['tecnico_nombre'] = $request->tecnico_nombre;
        } else {
            $request->validate(['tecnico_id' => 'required|exists:users,id']);
            $cirugiaData['tecnico_id'] = $request->tecnico_id;
        }

        $cirugia = Cirugia::create($cirugiaData);

        $cirugia->cajas()->attach($caja->id);

        if ($request->hasFile('pdf')) {
            $request->validate(['pdf' => 'mimes:pdf|max:10240']);
            if ($caja->pdf_path) {
                Storage::disk('public')->delete($caja->pdf_path);
            }
            $path = $request->file('pdf')->store('cajas/pdfs', 'public');
            $caja->update(['pdf_path' => $path]);
        }

        if ($request->hasFile('imagenes')) {
            $request->validate(['imagenes.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120']);
            foreach ($request->file('imagenes') as $imgFile) {
                $path = $imgFile->store('cajas/imagenes', 'public');
                CajaImagen::create([
                    'caja_id' => $caja->id,
                    'ruta' => $path,
                    'descripcion' => 'Pre-Cirugía ' . now()->format('d/m/Y H:i'),
                ]);
            }
        }

        $obs = "Egreso hacia esterilizadora para CX de {$request->paciente}";
        if ($request->plc_cod) {
            $obs .= " [PlcCod:{$request->plc_cod}]";
        }
        BoxStateService::transition($caja, 'EN ESTERILIZADORA', auth()->id(), [
            'observaciones' => $obs,
        ]);

        $successMsg = 'Caja enviada a Esterilizadora correctamente.';
        if ($request->tipo_tecnico === 'externo') {
            $url = route('tecnico.surgery.view', [$cirugia, $cirugia->access_token]);
            $successMsg .= " Link para técnico externo: <a href='{$url}' target='_blank' class='fw-bold text-white'>{$url}</a>";
        }

        return back()->with('success', $successMsg);
    }

    public function reasignar(Request $request, Cirugia $cirugia)
    {
        $request->validate([
            'tecnico_id' => 'nullable|exists:users,id',
            'tecnico_nombre' => 'nullable|string|max:255',
        ]);

        if (!$request->tecnico_id && !$request->tecnico_nombre) {
            return back()->with('error', 'Debe seleccionar un técnico o indicar un nombre externo.');
        }

        if ($cirugia->tecnico_id && !$cirugia->tecnico_original_id) {
            $cirugia->update(['tecnico_original_id' => $cirugia->tecnico_id]);
        }

        $cirugia->update([
            'tecnico_id' => $request->tecnico_id,
            'tecnico_nombre' => $request->tecnico_nombre,
        ]);

        $nombre = $request->tecnico_id
            ? User::find($request->tecnico_id)?->name ?? 'Técnico'
            : $request->tecnico_nombre;

        return back()->with('success', "Cirugía reasignada a {$nombre}.");
    }

    public function delegarRecepcion(Request $request, Caja $caja)
    {
        $request->validate([
            'responsable_nombre' => 'required|string|max:255',
            'accion' => 'required|in:consumo.controlar,consumo.finalizar',
            'resultado' => 'required_if:accion,consumo.finalizar|in:ok,falla',
        ]);

        $token = Str::random(48);

        $params = ['resultado' => $request->resultado] ?? [];

        TokensAccion::create([
            'token' => $token,
            'accion' => $request->accion,
            'caja_id' => $caja->id,
            'responsable_nombre' => $request->responsable_nombre,
            'params' => $params,
            'expires_at' => now()->addHours(24),
        ]);

        $url = route('recepcion.token', $token);

        return back()->with('success', "Link de recepción generado: <a href='{$url}' target='_blank' class='fw-bold'>{$url}</a>");
    }

    public function cancelar(Request $request, Cirugia $cirugia)
    {
        $request->validate([
            'motivo' => 'required|string',
            'accion' => 'required|in:cancelar,postergar',
        ]);

        foreach ($cirugia->cajas as $caja) {
            BoxStateService::transition($caja, 'DISPONIBLE', auth()->id(), [
                'observaciones' => "Cirugía {$request->accion}ada: {$request->motivo}"
            ]);
        }

        $nuevoStatus = $request->accion === 'cancelar' ? 'CANCELADA' : 'POSTPUESTA';
        $cirugia->update(['status' => $nuevoStatus]);

        $mensaje = $request->accion === 'cancelar'
            ? "Cirugía cancelada. Caja(s) retornada(s) a Disponible."
            : "Cirugía postergada. Caja(s) retornada(s) a Disponible. Se puede reasignar después.";

        return back()->with('success', $mensaje);
    }

    public function reparacion(Request $request, Caja $caja)
    {
        $request->validate(['observaciones' => 'required|string']);

        BoxStateService::transition($caja, 'EN REPARACION', auth()->id(), [
            'observaciones' => $request->observaciones
        ]);

        return back()->with('success', 'Caja enviada a Reparación.');
    }

    public function baja(Request $request, Caja $caja)
    {
        $request->validate(['observaciones' => 'required|string']);

        BoxStateService::transition($caja, 'BAJA', auth()->id(), [
            'observaciones' => $request->observaciones
        ]);

        return back()->with('success', 'Caja dada de baja.');
    }

    public function disponibilizar(Caja $caja)
    {
        if (!BoxStateService::canTransition($caja, 'DISPONIBLE')) {
            return back()->with('error', 'Esta caja no puede volver a Disponible desde su estado actual.');
        }

        BoxStateService::transition($caja, 'DISPONIBLE', auth()->id(), [
            'observaciones' => 'Caja reparada y vuelta a Disponible'
        ]);

        return back()->with('success', "{$caja->nombre} está nuevamente Disponible.");
    }

    public function buscarProcedimientos(Request $request, ProcedureApiService $api)
    {
        $request->validate([
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
            'paciente' => 'nullable|string|max:255',
            'medico' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $filters = array_filter($request->only(['fecha_desde', 'fecha_hasta', 'paciente', 'medico', 'limit']));

        $data = $api->search($filters);

        return response()->json($data);
    }
}
