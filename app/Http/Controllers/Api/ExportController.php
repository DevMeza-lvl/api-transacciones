<?php

namespace App\Http\Controllers\Api;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportCsvRequest;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Controlador para exportación de datos.
     *
     * @group Exportación
     */

    /**
     * Exporta transacciones filtradas a CSV.
     *
     * @authenticated
     * @get /api/transactions/export/csv
     *
     * @queryParam fecha_inicio date Filtra desde esta fecha. No obligatorio. Example: 2025-01-15
     * @queryParam fecha_fin date Filtra hasta esta fecha; debe ser posterior o igual a fecha_inicio. No obligatorio. Example: 2025-02-15
     * @queryParam delimiter string El delimitador CSV (";" o ","). Default: ";". Example: ;
     *
     *
     * @response 200 Archivo CSV descargable con las transacciones.
     */
    public function exportTransactionsCsv(ExportCsvRequest $request)
    {

        $delimiter = $request->query('delimiter', ';');
        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        $user = auth()->user();
        $role = $user->role; 
        $isAdmin = $role === 'admin';

        $filename = 'transacciones_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return Excel::download(
            new TransactionsExport($fechaInicio, $fechaFin, $delimiter, $isAdmin ? null : $user->id),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
