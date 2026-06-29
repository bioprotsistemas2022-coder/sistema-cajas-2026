<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaImagen;
use App\Services\BoxStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminCajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::all();
        return view('admin.cajas', compact('cajas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_interno' => 'required|string|unique:cajas,codigo_interno',
        ]);

        Caja::create([
            'nombre' => $request->nombre,
            'codigo_interno' => $request->codigo_interno,
            'estado' => 'DISPONIBLE',
        ]);

        return back()->with('success', 'Caja creada correctamente.');
    }

    public function update(Request $request, Caja $caja)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_interno' => 'required|string|unique:cajas,codigo_interno,' . $caja->id,
        ]);

        $caja->update([
            'nombre' => $request->nombre,
            'codigo_interno' => $request->codigo_interno,
        ]);

        return back()->with('success', 'Caja actualizada.');
    }

    public function destroy(Caja $caja)
    {
        foreach ($caja->imagenes as $img) {
            Storage::disk('public')->delete($img->ruta);
        }
        foreach ($caja->imagenes as $img) {
            $img->delete();
        }
        if ($caja->pdf_path) {
            Storage::disk('public')->delete($caja->pdf_path);
        }
        $caja->delete();
        return redirect()->route('admin.cajas.index')->with('success', 'Caja eliminada.');
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
