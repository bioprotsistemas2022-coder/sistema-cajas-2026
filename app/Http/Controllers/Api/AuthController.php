<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Emite un token de acceso para el usuario autenticado.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken(
            $request->device_name ?? 'api-token',
            $this->abilitiesForRole($user->role)
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * Devuelve el usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    /**
     * Lista los tokens activos del usuario.
     */
    public function tokens(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->tokens()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at'])
        );
    }

    /**
     * Revoca un token específico.
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return response()->json(['message' => 'Token revocado.']);
    }

    /**
     * Define las habilidades (abilities) del token según el rol del usuario.
     */
    private function abilitiesForRole(string $role): array
    {
        return match ($role) {
            'admin' => ['*'],
            'deposito' => [
                'cajas:read', 'cajas:write',
                'cirugias:read', 'cirugias:write',
                'consumos:read',
                'grupos:read', 'grupos:write',
                'eventos:read',
                'consignaciones:read', 'consignaciones:write',
            ],
            'tecnico' => [
                'cajas:read',
                'cirugias:read', 'cirugias:write',
                'consumos:read', 'consumos:write',
                'eventos:read',
                'consignaciones:read',
            ],
            'consumo' => [
                'cajas:read',
                'cirugias:read',
                'consumos:read', 'consumos:write',
                'eventos:read',
                'consignaciones:read',
            ],
            'logistica' => [
                'cajas:read',
                'cirugias:read',
                'eventos:read',
                'consignaciones:read',
            ],
            'acondicionador' => [
                'cajas:read', 'cajas:write',
                'cirugias:read',
                'eventos:read',
            ],
            default => ['cajas:read', 'cirugias:read', 'eventos:read', 'consignaciones:read'],
        };
    }
}