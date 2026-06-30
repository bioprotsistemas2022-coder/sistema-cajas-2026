<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\AcondicionadorController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\AdminTecnicoController;
use App\Http\Controllers\AdminCajaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return match($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'deposito' => redirect()->route('deposito.dashboard'),
        'tecnico' => redirect()->route('tecnico.dashboard'),
        'consumo' => redirect()->route('consumo.dashboard'),
        'acondicionador' => redirect()->route('acondicionador.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin,deposito'])->group(function () {
    Route::get('/admin', [DepositoController::class, 'index'])->name('admin.dashboard');
    Route::get('/deposito', [DepositoController::class, 'index'])->name('deposito.dashboard');
    Route::get('/cajas', [CajaController::class, 'index'])->name('cajas.index');
    Route::get('/cajas/{caja}', [CajaController::class, 'show'])->name('cajas.show');
    Route::post('/cajas/{caja}/egreso', [DepositoController::class, 'egreso'])->name('cajas.egreso');
    Route::post('/cajas/{caja}/reparacion', [DepositoController::class, 'reparacion'])->name('cajas.reparacion');
    Route::post('/cajas/{caja}/baja', [DepositoController::class, 'baja'])->name('cajas.baja');
    Route::post('/cajas/{caja}/pdf', [CajaController::class, 'uploadPdf'])->name('cajas.pdf');
    Route::delete('/cajas/{caja}/pdf', [CajaController::class, 'deletePdf'])->name('cajas.deletePdf');
    Route::post('/cajas/{caja}/imagen', [CajaController::class, 'uploadImagen'])->name('cajas.imagen');
    Route::delete('/cajas/imagen/{imagen}', [CajaController::class, 'deleteImagen'])->name('cajas.deleteImagen');

    // ABM Tecnicos accessible by Admin and Deposito
    Route::get('/admin/tecnicos', [AdminTecnicoController::class, 'index'])->name('admin.tecnicos');
    Route::post('/admin/tecnicos', [AdminTecnicoController::class, 'store'])->name('admin.tecnicos.store');
    Route::put('/admin/tecnicos/{tecnico}', [AdminTecnicoController::class, 'update'])->name('admin.tecnicos.update');
    Route::delete('/admin/tecnicos/{tecnico}', [AdminTecnicoController::class, 'destroy'])->name('admin.tecnicos.destroy');

    // Reasignar técnico de cirugía
    Route::post('/cirugias/{cirugia}/reasignar', [DepositoController::class, 'reasignar'])->name('cirugias.reasignar');

    // Delegar recepción (generar token externo)
    Route::post('/cajas/{caja}/delegar-recepcion', [DepositoController::class, 'delegarRecepcion'])->name('cajas.delegarRecepcion');

    // Cancelar o postergar cirugía
    Route::post('/cirugias/{cirugia}/cancelar', [DepositoController::class, 'cancelar'])->name('cirugias.cancelar');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/cajas', [AdminCajaController::class, 'index'])->name('admin.cajas.index');
    Route::post('/admin/cajas', [AdminCajaController::class, 'store'])->name('admin.cajas.store');
    Route::put('/admin/cajas/{caja}', [AdminCajaController::class, 'update'])->name('admin.cajas.update');
    Route::delete('/admin/cajas/{caja}', [AdminCajaController::class, 'destroy'])->name('admin.cajas.destroy');
    Route::post('/admin/cajas/{caja}/pdf', [AdminCajaController::class, 'uploadPdf'])->name('admin.cajas.pdf');
    Route::delete('/admin/cajas/{caja}/pdf', [AdminCajaController::class, 'deletePdf'])->name('admin.cajas.deletePdf');
    Route::post('/admin/cajas/{caja}/imagen', [AdminCajaController::class, 'uploadImagen'])->name('admin.cajas.imagen');
    Route::delete('/admin/cajas/imagen/{imagen}', [AdminCajaController::class, 'deleteImagen'])->name('admin.cajas.deleteImagen');
});

Route::middleware(['auth', 'role:admin,tecnico'])->group(function () {
    Route::get('/tecnico', [TecnicoController::class, 'index'])->name('tecnico.dashboard');
});

Route::get('/cx/{cirugia}/{token}', [TecnicoController::class, 'viewSurgery'])->name('tecnico.surgery.view');
Route::post('/cx/{cirugia}/llegado', [TecnicoController::class, 'llegado'])->name('tecnico.surgery.llegado');
Route::post('/cx/{cirugia}/finalizar', [TecnicoController::class, 'finalizar'])->name('tecnico.surgery.finalizar');

// Token público para recepción (logística externa)
Route::get('/recepcion/{token}', [ConsumoController::class, 'viewToken'])->name('recepcion.token');
Route::post('/recepcion/{token}/confirmar', [ConsumoController::class, 'confirmarToken'])->name('recepcion.confirmar');

Route::middleware(['auth', 'role:admin,consumo'])->group(function () {
    Route::get('/consumo', [ConsumoController::class, 'index'])->name('consumo.dashboard');
    Route::post('/consumo/{caja}/controlar', [ConsumoController::class, 'controlar'])->name('consumo.controlar');
    Route::post('/consumo/{caja}/finalizar', [ConsumoController::class, 'finalizar'])->name('consumo.finalizar');
});

Route::middleware(['auth', 'role:admin,acondicionador'])->group(function () {
    Route::get('/acondicionamiento', [AcondicionadorController::class, 'index'])->name('acondicionador.dashboard');
    Route::post('/acondicionamiento/{caja}/disponibilizar', [AcondicionadorController::class, 'disponibilizar'])->name('acondicionador.disponibilizar');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
