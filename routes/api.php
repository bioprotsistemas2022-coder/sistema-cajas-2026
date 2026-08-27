<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\CirugiaController;
use App\Http\Controllers\Api\ConsignacionController;
use App\Http\Controllers\Api\ConsumoController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\GrupoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API de conexión para sistemas externos. Autenticación mediante tokens
| de Laravel Sanctum (Bearer token).
|
*/

// Autenticación (público)
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Sesión
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/tokens', [AuthController::class, 'tokens']);
    Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);

    // Cajas
    Route::middleware('abilities:cajas:read')->group(function () {
        Route::get('/cajas', [CajaController::class, 'index']);
        Route::get('/cajas/{caja}', [CajaController::class, 'show']);
    });
    Route::middleware('abilities:cajas:write')->group(function () {
        Route::post('/cajas', [CajaController::class, 'store']);
        Route::put('/cajas/{caja}', [CajaController::class, 'update']);
        Route::patch('/cajas/{caja}', [CajaController::class, 'update']);
        Route::delete('/cajas/{caja}', [CajaController::class, 'destroy']);
        Route::patch('/cajas/{caja}/estado', [CajaController::class, 'changeState']);
    });

    // Consignaciones (Módulo de Consignaciones)
    Route::middleware('abilities:consignaciones:read')->group(function () {
        Route::get('/consignaciones', [ConsignacionController::class, 'index']);
        Route::get('/consignaciones/stats', [ConsignacionController::class, 'stats']);
        Route::get('/consignaciones/{caja}', [ConsignacionController::class, 'show']);
    });
    Route::middleware('abilities:consignaciones:write')->group(function () {
        Route::post('/consignaciones', [ConsignacionController::class, 'store']);
        Route::post('/consignaciones/{caja}/devolver', [ConsignacionController::class, 'devolver']);
    });

    // Cirugías
    Route::middleware('abilities:cirugias:read')->group(function () {
        Route::get('/cirugias', [CirugiaController::class, 'index']);
        Route::get('/cirugias/{cirugia}', [CirugiaController::class, 'show']);
    });
    Route::middleware('abilities:cirugias:write')->group(function () {
        Route::post('/cirugias', [CirugiaController::class, 'store']);
        Route::post('/cirugias/vincular-cx', [CirugiaController::class, 'vincularCx']);
        Route::put('/cirugias/{cirugia}', [CirugiaController::class, 'update']);
        Route::patch('/cirugias/{cirugia}', [CirugiaController::class, 'update']);
        Route::delete('/cirugias/{cirugia}', [CirugiaController::class, 'destroy']);
        Route::post('/cirugias/{cirugia}/cajas', [CirugiaController::class, 'attachCajas']);
        Route::delete('/cirugias/{cirugia}/cajas/{caja}', [CirugiaController::class, 'detachCaja']);
    });

    // Consumos
    Route::middleware('abilities:consumos:read')->group(function () {
        Route::get('/consumos', [ConsumoController::class, 'index']);
        Route::get('/consumos/{consumo}', [ConsumoController::class, 'show']);
    });
    Route::middleware('abilities:consumos:write')->group(function () {
        Route::post('/consumos', [ConsumoController::class, 'store']);
        Route::put('/consumos/{consumo}', [ConsumoController::class, 'update']);
        Route::patch('/consumos/{consumo}', [ConsumoController::class, 'update']);
        Route::delete('/consumos/{consumo}', [ConsumoController::class, 'destroy']);
    });

    // Grupos
    Route::middleware('abilities:grupos:read')->group(function () {
        Route::get('/grupos', [GrupoController::class, 'index']);
        Route::get('/grupos/{grupo}', [GrupoController::class, 'show']);
    });
    Route::middleware('abilities:grupos:write')->group(function () {
        Route::post('/grupos', [GrupoController::class, 'store']);
        Route::put('/grupos/{grupo}', [GrupoController::class, 'update']);
        Route::patch('/grupos/{grupo}', [GrupoController::class, 'update']);
        Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy']);
        Route::post('/grupos/{grupo}/cajas', [GrupoController::class, 'attachCajas']);
        Route::delete('/grupos/{grupo}/cajas/{caja}', [GrupoController::class, 'detachCaja']);
    });

    // Historial de eventos
    Route::middleware('abilities:eventos:read')->group(function () {
        Route::get('/eventos', [EventoController::class, 'index']);
        Route::get('/eventos/{evento}', [EventoController::class, 'show']);
    });
});