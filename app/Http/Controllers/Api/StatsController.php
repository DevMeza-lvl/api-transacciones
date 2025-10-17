<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function authorizeAdmin()
    {
        if(auth()->user()->role !== 'admin') {
            abort(403, 'Forbidden');
        }
    }

    /**
     * Controlador de estadísticas. 
     *
     * @group Estadísticas
     */

    /**
     * Obtiene estadísticas básicas por usuario (total y promedio monto transferido). Solo administradores.
     *
     * @authenticated
     *
     * @response 200 {
     *   "usuarios_stats": [
     *     {
     *       "id": 1,
     *       "name": "Usuario 1",
     *       "total_transferido": 5000.00,
     *       "promedio_monto": 250.00
     *     },
     *     ...
     *   ]
     * }
     */

    public function usuariosStats(): JsonResponse
    {
        $this->authorizeAdmin();
        $usuariosStats = User::select([
                'users.id',
                'users.name',
                DB::raw('COALESCE(SUM(t.monto), 0) as total_transferido'),
                DB::raw('COALESCE(AVG(t.monto), 0) as promedio_monto')
            ])
            ->leftJoin('transactions as t', function ($join) {
                $join->on('users.id', '=', 't.usuario_emisor_id')
                     ->where('t.estado', '=', 'completada');
            })
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_transferido', 'desc')
            ->get();

        return response()->json([
            'usuarios_stats' => $usuariosStats,
        ]);
    }
}
