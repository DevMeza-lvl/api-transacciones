<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDailyTransferLimit
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->routeIs('transactions.store')) {
            $usuario = $request->user();
            $monto = $request->input('monto');

            if ($usuario && $monto) {
                if (!$usuario->puedeTransferirHoy($monto)) {
                    return response()->json([
                        'message' => 'Límite diario de transferencia excedido',
                        'errors' => ['monto' => ['El monto excede el límite diario de 5,000 USD']]
                    ], 422);
                }
            }
        }

        return $next($request);
    }
}
