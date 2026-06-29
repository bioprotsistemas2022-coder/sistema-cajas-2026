<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminTecnicoController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'tecnico');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tecnicos = $query->orderBy('name')->get();
        return view('admin.tecnicos', compact('tecnicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'tecnico',
        ]);

        return back()->with('success', 'Técnico creado correctamente.');
    }

    public function update(Request $request, User $tecnico)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $tecnico->id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = $request->password;
        }

        $tecnico->update($data);

        return back()->with('success', 'Técnico actualizado.');
    }

    public function destroy(User $tecnico)
    {
        if ($tecnico->role !== 'tecnico') {
            return back()->with('error', 'Solo se pueden eliminar técnicos.');
        }

        $tecnico->delete();
        return back()->with('success', 'Técnico eliminado.');
    }
}
