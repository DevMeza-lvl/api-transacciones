<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Controlador de transacciones entre usuarios.
     *
     * @group Transacciones
     */

    /**
     * Lista transacciones con filtros y paginación.
     *
     * @authenticated
     * @queryParam fecha_inicio date Opcional, filtra desde esta fecha.
     * @queryParam fecha_fin date Opcional, filtra hasta esta fecha.
     * @queryParam usuario_id int Opcional, filtra por usuario emisor o receptor.
     * @queryParam per_page int Cantidad de elementos por página. Default: 15.
     *
     * @response 200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "usuario_emisor_id": 2,
     *      "usuario_receptor_id": 3,
     *      "monto": 100,
     *      ...
     *    }
     *  ],
     *  "links": {...},
     *  "meta": {...}
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = Transaction::query()
            ->conUsuarios()
            ->when($request->fecha_inicio, function ($query, $fecha) {
                return $query->where('fecha_transaccion', '>=', $fecha);
            })
            ->when($request->fecha_fin, function ($query, $fecha) {
                return $query->where('fecha_transaccion', '<=', $fecha);
            })
            ->when($request->usuario_id, function ($query, $usuario) {
                return $query->where(function ($q) use ($usuario) {
                    $q->where('usuario_emisor_id', $usuario)
                      ->orWhere('usuario_receptor_id', $usuario);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($transactions);
    }

    /**
     * Crea una nueva transacción validando reglas de negocio.
     *
     * @authenticated
     * 
     * @bodyParam email_receptor string required Email del receptor.
     * @bodyParam monto number required Monto a transferir.
     * @bodyParam descripcion string Opcional.
     *
     * @response 201 {
     *   "message": "Transacción realizada exitosamente",
     *   "transaction": {...}
     * }
     * 
     * @response 422 {
     *   "message": "Saldo insuficiente",
     *   "errors": { "monto": [...] }
     * }
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $usuarioEmisor = auth()->user();
            $usuarioReceptor = User::where('email', $request->email_receptor)->first();

            if (!$usuarioReceptor) {
                return response()->json([
                    'message' => 'El usuario receptor no existe',
                    'errors' => ['email_receptor' => ['El correo del receptor no está registrado']]
                ], 422);
            }

            if ($usuarioEmisor->id === $usuarioReceptor->id) {
                return response()->json([
                    'message' => 'No se puede transferir a sí mismo',
                    'errors' => ['email_receptor' => ['El receptor debe ser diferente al emisor']]
                ], 422);
            }

            $monto = $request->monto;

            if (!$usuarioEmisor->puedeTransferir($monto)) {
                return response()->json([
                    'message' => 'Saldo insuficiente para realizar la transferencia',
                    'errors' => ['monto' => ['El monto excede el saldo disponible']]
                ], 422);
            }

            if (!$usuarioEmisor->puedeTransferirHoy($monto)) {
                return response()->json([
                    'message' => 'Límite diario de transferencia excedido (5,000 USD)',
                    'errors' => ['monto' => ['El monto excede el límite diario de transferencia']]
                ], 422);
            }

            $transaccionDuplicada = Transaction::where('usuario_emisor_id', $usuarioEmisor->id)
                ->where('usuario_receptor_id', $usuarioReceptor->id)
                ->where('monto', $monto)
                ->where('fecha_transaccion', today())
                ->where('descripcion', $request->descripcion)
                ->exists();

            if ($transaccionDuplicada) {
                return response()->json([
                    'message' => 'Transacción duplicada detectada',
                    'errors' => ['general' => ['Ya existe una transacción idéntica hoy']]
                ], 422);
            }

            $transaction = Transaction::create([
                'usuario_emisor_id' => $usuarioEmisor->id,
                'usuario_receptor_id' => $usuarioReceptor->id,
                'monto' => $monto,
                'descripcion' => $request->descripcion,
                'fecha_transaccion' => today(),
                'estado' => 'completada',
            ]);

            $usuarioEmisor->decrement('saldo_actual', $monto);
            $usuarioReceptor->increment('saldo_actual', $monto);

            DB::commit();

            return response()->json([
                'message' => 'Transacción realizada exitosamente',
                'transaction' => $transaction->load(['usuarioEmisor:id,name', 'usuarioReceptor:id,name']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar la transacción',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra detalle de una transacción.
     *
     * @authenticated
     *
     * @urlParam transaction int required ID de la transacción.
     *
     * @response 200 {
     *   "id": 1,
     *   "usuario_emisor": {...},
     *   "usuario_receptor": {...},
     *   ...
     * }
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['usuarioEmisor', 'usuarioReceptor']);
        return response()->json($transaction);
    }
}
