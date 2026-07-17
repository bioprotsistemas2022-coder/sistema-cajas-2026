<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Caja;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::withCount('cajas')->with('cajas')->get();
        $cajasDisponibles = Caja::where('estado', 'DISPONIBLE')->orderBy('nombre')->get();
        return view('admin.grupos', compact('grupos', 'cajasDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cajas' => 'nullable|array',
            'cajas.*' => 'exists:cajas,id',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        if ($request->cajas) {
            $grupo->cajas()->attach($request->cajas);
        }

        return redirect()->route('admin.grupos')->with('success', 'Grupo creado correctamente.');
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cajas' => 'nullable|array',
            'cajas.*' => 'exists:cajas,id',
        ]);

        $grupo->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        $grupo->cajas()->sync($request->cajas ?? []);

        return redirect()->route('admin.grupos')->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->delete();
        return redirect()->route('admin.grupos')->with('success', 'Grupo eliminado.');
    }
}
