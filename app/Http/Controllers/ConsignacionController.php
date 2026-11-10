<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaConsignacion;
use App\Services\BoxStateService;
use App\Services\InstrumentalPdfExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConsignacionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $disponibles = Caja::where('estado', 'DISPONIBLE')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('codigo_interno', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $consignadas = Caja::where('estado', 'CONSIGNADA')
            ->orderByDesc('fecha_consignacion')
            ->paginate(20, ['*'], 'consignadas_page');

        return view('consignaciones.index', compact('disponibles', 'consignadas'));
    }

    /**
     * Paso 1 de "Consignar": valida los datos de cabecera, guarda el PDF (si
     * vino) y extrae su instrumental (cantidad/descripción) con
     * InstrumentalPdfExtractorService, igual que hace el módulo de
     * Consignaciones. Muestra la grilla editable antes de confirmar.
     */
    public function extraer(Request $request, Caja $caja)
    {
        $request->validate([
            'consignatario_nombre' => 'required|string|max:255',
            'fecha_consignacion' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($caja->estado !== 'DISPONIBLE') {
            return back()->with('error', 'Solo cajas Sin Consignar pueden consignarse. La caja "' . $caja->nombre . '" está "' . $caja->estadoLabel() . '".');
        }

        $pdfPath = null;
        $secciones = [];

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('cajas/pdfs', 'public');
            $secciones = InstrumentalPdfExtractorService::extraer(Storage::disk('public')->path($pdfPath));
        }

        return view('consignaciones.confirmar', [
            'caja' => $caja,
            'consignatario_nombre' => $request->consignatario_nombre,
            'fecha_consignacion' => $request->fecha_consignacion ?: now()->format('Y-m-d'),
            'observaciones' => $request->observaciones,
            'pdf_path' => $pdfPath,
            'secciones' => $secciones,
        ]);
    }

    /**
     * Paso 2 ("Confirmar Consignación"): guarda la grilla (ya revisada/editada
     * por el usuario) como el instrumental de esta consignación, pasa la
     * caja a CONSIGNADA y registra consignatario/fecha, igual que hace el
     * módulo de Consignaciones vía POST /api/consignaciones.
     */
    public function consignar(Request $request, Caja $caja)
    {
        $request->validate([
            'consignatario_nombre' => 'required|string|max:255',
            'fecha_consignacion' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'pdf_path' => 'nullable|string',
            'cant' => 'nullable|array',
            'desc' => 'nullable|array',
            'seccion' => 'nullable|array',
            'chk' => 'nullable|array',
        ]);

        if ($caja->estado !== 'DISPONIBLE') {
            return back()->with('error', 'Solo cajas Sin Consignar pueden consignarse. La caja "' . $caja->nombre . '" está "' . $caja->estadoLabel() . '".');
        }

        DB::transaction(function () use ($request, $caja) {
            BoxStateService::transition($caja, 'CONSIGNADA', auth()->id(), [
                'responsable_nombre' => auth()->user()->name,
                'observaciones' => $request->observaciones,
            ]);

            $pdfPath = $request->input('pdf_path') ?: null;

            $caja->consignatario_nombre = $request->consignatario_nombre;
            $caja->fecha_consignacion = $request->fecha_consignacion ?: now();
            if ($pdfPath) {
                $caja->pdf_path = $pdfPath;
            }
            $caja->save();

            $consignacion = CajaConsignacion::create([
                'caja_id' => $caja->id,
                'user_id' => auth()->id(),
                'consignatario_nombre' => $request->consignatario_nombre,
                'fecha_consignacion' => $request->fecha_consignacion ?: now(),
                'observaciones' => $request->observaciones,
                'pdf_path' => $pdfPath,
            ]);

            $cants = $request->input('cant', []);
            $descs = $request->input('desc', []);
            $secciones = $request->input('seccion', []);
            $chks = $request->input('chk', []);

            foreach ($cants as $idx => $cant) {
                $cantidad = (int) $cant;
                $descripcion = trim($descs[$idx] ?? '');
                if ($cantidad <= 0 || $descripcion === '') {
                    continue;
                }
                $consignacion->items()->create([
                    'seccion' => $secciones[$idx] ?? null,
                    'cantidad' => $cantidad,
                    'descripcion' => $descripcion,
                    'incluido' => isset($chks[$idx]),
                ]);
            }
        });

        return redirect()->route('consignaciones.index')->with('success', "{$caja->nombre} consignada a {$request->consignatario_nombre}.");
    }

    /**
     * Reversa una consignación: CONSIGNADA -> DISPONIBLE.
     */
    public function devolver(Request $request, Caja $caja)
    {
        $request->validate(['observaciones' => 'nullable|string']);

        if ($caja->estado !== 'CONSIGNADA') {
            return back()->with('error', 'Solo cajas Consignadas pueden devolverse. La caja "' . $caja->nombre . '" está "' . $caja->estadoLabel() . '".');
        }

        BoxStateService::transition($caja, 'DISPONIBLE', auth()->id(), [
            'observaciones' => $request->observaciones ?: 'Devuelta a Disponible desde Consignaciones',
        ]);

        $caja->consignatario_nombre = null;
        $caja->fecha_consignacion = null;
        $caja->save();

        return back()->with('success', "{$caja->nombre} devuelta a Sin Consignar.");
    }

    /**
     * Historial de consignaciones (con sus ítems) de una caja, para el modal
     * "Ver instrumental" en la pantalla de Consignaciones.
     */
    public function historial(Caja $caja)
    {
        return response()->json(
            $caja->consignaciones()->with('items')->orderByDesc('fecha_consignacion')->get()
        );
    }
}
