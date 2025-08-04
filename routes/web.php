<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaMedicaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AtencionesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('auth.login');
});
// Ruta para recordar la cita
Route::get('/citas/{id}/recordar', [CitaMedicaController::class, 'recordarForm'])->name('citas.recordar');
// Ruta raíz opcional
Route::get('/', function () {
    return redirect()->route('citas.index');
});

Route::middleware(['auth'])->group(function () {
    // FORMULARIO cambiar contraseña:
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])
         ->name('profile.password.edit');

    // PROCESAR cambio de contraseña:
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
         ->name('profile.password.update');
});
Route::patch('profile/avatar', [ProfileController::class, 'updateAvatar'])
     ->name('profile.avatar.update');
// web.php
Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil');
Route::middleware('auth')->group(function () {
    // Mostrar y actualizar datos básicos
    Route::get('perfil', [ProfileController::class, 'show'])->name('perfil.show');
Route::post('/perfil/avatar', [ProfileController::class, 'actualizarAvatar'])->name('perfil.avatar');
    // Actualizar contraseña
    Route::put('perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::put('perfil/avatar/remove', [ProfileController::class, 'removeAvatar'])
     ->middleware('auth')
     ->name('perfil.avatar.remove');
Route::put('perfil/avatar', [ProfileController::class, 'updateAvatar'])
     ->name('perfil.avatar')
     ->middleware('auth');
Route::get('atenciones/create', [AtencionesController::class, 'create'])->name('atenciones.create');
Route::post('atenciones', [AtencionesController::class, 'store'])->name('atenciones.store');
Route::get('atenciones/{atencion}/edit', [AtencionesController::class, 'edit'])->name('atenciones.edit');
Route::put('atenciones/{atencion}', [AtencionesController::class, 'update'])->name('atenciones.update');
Route::delete('atenciones/{atencion}', [AtencionesController::class, 'destroy'])->name('atenciones.destroy');
Route::middleware('auth')->group(function () {
    // Otras rutas...
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});
Route::middleware(['auth'])->group(function() {
    Route::get('/citas/events', [CitaMedicaController::class, 'events'])->name('citas.events');
    Route::resource('citas', CitaMedicaController::class)->except(['show', 'create', 'edit']);
});

// Procesar la actualización del perfil
Route::put('/perfil', [ProfileController::class, 'update'])
     ->name('profile.update');
// CRUD completo con resource
Route::put('horarios/update-masivo', [HorarioController::class, 'updateMasivo'])
     ->name('horarios.updateMasivo');
Route::post('/horarios/guardar', [HorarioController::class, 'guardarCambios'])->name('horarios.guardarCambios');
Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
Route::get('/horarios/{medico_id}', [HorarioController::class, 'getHorarios']);
Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
Route::put('/horarios/{id}', [HorarioController::class, 'update'])->name('horarios.update');
Route::get('/horarios/medico/{medico_id}', [HorarioController::class, 'getHorarios'])->name('horarios.get');
Route::get('/reportes/ausencias', [ReporteController::class, 'ausencias'])->name('reportes.ausencias');
Route::get('/reportes/atendidas', [ReporteController::class, 'atendidas'])->name('reportes.atendidas');
Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
Route::get('/reportes/reporte', [ReporteController::class, 'reporte'])->name('reportes.reporte');
Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::apiResource('atenciones', AtencionesController::class);
Route::middleware(['auth'])
     ->resource('usuarios', UserController::class);
Route::resource('citas', CitaMedicaController::class);
Route::post('/emails/{id}/recordatorio', [CitaMedicaController::class, 'enviarRecordatorio'])->name('emails.recordatorio');
Route::post('/citas/{id}/recordar', [CitaMedicaController::class, 'recordar'])->name('citas.reminder');
Route::get('/reportes/resumen', [ReporteController::class, 'resumenGeneral'])->name('reportes.resumen');
// web.php
Route::get('/citas/exportar/excel', [CitaMedicaController::class, 'exportarExcel'])->name('citas.exportar.excel');
Route::get('/citas/exportar/pdf', [CitaMedicaController::class, 'exportarPDF'])->name('citas.exportar.pdf');
Route::get('/medicos/exportar/excel', [MedicoController::class, 'exportarExcel'])->name('medicos.exportar.excel');
Route::get('/medicos/exportar/pdf', [MedicoController::class, 'exportarPDF'])->name('medicos.exportar.pdf');
Route::get('/consultas/exportar-excel', [CitaMedicaController::class, 'exportarExcel'])->name('consultas.exportar.excel');
// Rutas de autenticación
Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('auth.login');
// Ruta para cancelar la cita, debe usar el método DELETE
Route::delete('/citas/{id}/cancel', [CitaMedicaController::class, 'destroy'])->name('citas.cancel');
Route::get('/pacientes/{id}/citas', [PacienteController::class, 'verCitas'])->name('pacientes.citas');

Route::get('citas/{cita}/recordatorio', [CitaMedicaController::class, 'recordatorio'])->name('citas.recordatorio');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
// Rutas para manejar las citas médicas
Route::post('/citas', [CitaMedicaController::class, 'store']);
Route::post('/cita/index', [CitaMedicaController::class, 'indexCita']);
Route::put('/cita/{id}/create', [CitaMedicaController::class, 'createCita']);
Route::post('/cita/{id}/notificar', [CitaMedicaController::class, 'notificarCita']);
Route::delete('/cita/{id}/cancelar', [CitaMedicaController::class, 'cancelarCita']);
// Ruta para cancelar una cita médica
Route::delete('/citas/{id}/cancel', [CitaMedicaController::class, 'destroy'])->name('citas.cancel');
Route::resource('citas', CitaMedicaController::class);
Route::middleware(['auth'])->group(function () {
    // Ruta para marcar como atendida
    Route::put('atenciones/{atencion}/atendida', [AtencionesController::class, 'marcarAtendida'])
         ->name('atenciones.atendida');
});
// Ruta protegida después del login
Route::middleware(['auth'])->group(function () {
    Route::get('/public', function () {
        return view('dashboard'); // Aquí carga tu vista principal
    })->name('dashboard');
Route::resource('medicos', MedicoController::class);
Route::get('/reportes', function () {
    return view('reportes.index');
})->name('reportes.index');
Route::resource('reportes', ReporteController::class);
Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/enfermedades', [ReporteController::class, 'index'])->name('reportes.enfermedades');

// Ruta para el reporte de pacientes recurrentes
Route::get('/reportes/enfermedades', [ReporteController::class, 'index'])->name('reportes.enfermedadesComunes');
Route::post('/reportes/enfermedades', [ReporteController::class, 'store'])->name('reportes.enfermedadesComunes.store');
Route::get('/reportes/{id}', [ReporteController::class, 'show'])->name('reportes.show');
Route::get('/pacientes',                   [PacienteController::class, 'index'])->name('pacientes.index');
Route::get('/pacientes/create',            [PacienteController::class, 'create'])->name('pacientes.create');
Route::post('/pacientes',                  [PacienteController::class, 'store'])->name('pacientes.store');
Route::put('/pacientes/{paciente}',        [PacienteController::class, 'update'])->name('pacientes.update');
Route::delete('/pacientes/{paciente}',     [PacienteController::class, 'destroy'])->name('pacientes.destroy');
Route::get('/pacientes/exportar/excel',    [PacienteController::class, 'exportarExcel'])->name('pacientes.exportar.excel');
Route::get('/pacientes/exportar/pdf',      [PacienteController::class, 'exportarPDF'])->name('pacientes.exportar.pdf');
});
Route::middleware(['auth'])->group(function () {
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/medicos/create', [MedicoController::class, 'create'])->name('medicos.create');
Route::post('/medicos', [MedicoController::class, 'store'])->name('medicos.store');
});
