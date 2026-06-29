<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaImagen;
use App\Models\Cirugia;
use App\Models\User;
use App\Services\BoxStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DepositoController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('imagenes')->get();
        $tecnicos = User::where('role', 'tecnico')->get();
        return view('deposito.dashboard', compact('cajas', 'tecnicos'));
    }

    public function egreso(Request $request, Caja $caja)
    {
        $request->validate([
            'tecnico_id' => 'required|exists:users,id',
            'paciente' => 'required|string',
            'medico' => 'required|string',
            'bioimplant_id' => 'nullable|string',
        ]);

        if (!BoxStateService::canTransition($caja, 'EN ESTERILIZADORA')) {
            return back()->with('error', 'La caja no puede pasar a Esterilizadora desde su estado actual.');
        }

        $cirugia = Cirugia::create([
            'paciente' => $request->paciente,
            'medico' => $request->medico,
            'bioimplant_id' => $request->bioimplant_id,
            'tecnico_id' => $request->tecnico_id,
            'fecha_cx' => now(),
            'status' => 'PENDIENTE'
        ]);

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

        BoxStateService::transition($caja, 'EN ESTERILIZADORA', auth()->id(), [
            'observaciones' => "Egreso hacia esterilizadora para CX de {$request->paciente}"
        ]);

        return back()->with('success', 'Caja enviada a Esterilizadora correctamente.');
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
}
