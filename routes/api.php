<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Middleware\CheckDailyTransferLimit;

/**
 * API REST para gestión de usuarios, autenticación, transacciones, estadísticas y exportaciones.
 *
 * Rutas públicas:
 * - /login: Autenticación de usuario.
 * - /register: Registro de nuevo usuario.
 * 
 * Rutas protegidas con Sanctum:
 * - /logout, /me: Manejo de sesión y datos de usuario autenticado.
 * - /transactions: Registro y gestión de transacciones.
 * - /users: CRUD de usuarios (excepto registro).
 * - /stats/usuarios: Estadísticas de transferencias por usuario.
 * - /transactions/export/csv: Exportación CSV de transacciones.
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/transactions', [TransactionController::class, 'store'])
        ->middleware(CheckDailyTransferLimit::class);

    Route::get('/transactions/export/csv', [ExportController::class, 'exportTransactionsCsv']);

    Route::get('/stats/usuarios', [StatsController::class, 'usuariosStats']);

    Route::apiResource('users', UserController::class)->except(['store']);
});
