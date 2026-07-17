<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaImagen;
use App\Services\BoxStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::all();
        return view('cajas.index', compact('cajas'));
    }

    public function show(Caja $caja)
    {
        $caja->load(['eventos.user', 'cirugias.consumos', 'imagenes', 'grupos']);
        return view('cajas.show', compact('caja'));
    }

    public function uploadPdf(Request $request, Caja $caja)
    {
        $request->validate(['pdf' => 'required|mimes:pdf|max:10240']);

        if ($caja->pdf_path) {
            Storage::disk('public')->delete($caja->pdf_path);
        }

        $path = $request->file('pdf')->store('cajas/pdfs', 'public');
        $caja->update(['pdf_path' => $path]);

        return back()->with('success', 'PDF cargado correctamente.');
    }

    public function deletePdf(Caja $caja)
    {
        if ($caja->pdf_path) {
            Storage::disk('public')->delete($caja->pdf_path);
            $caja->update(['pdf_path' => null]);
        }
        return back()->with('success', 'PDF eliminado.');
    }

    public function uploadImagen(Request $request, Caja $caja)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $path = $request->file('imagen')->store('cajas/imagenes', 'public');

        CajaImagen::create([
            'caja_id' => $caja->id,
            'ruta' => $path,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Imagen cargada correctamente.');
    }

    public function deleteImagen(CajaImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();
        return back()->with('success', 'Imagen eliminada.');
    }
}
