<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $delimiter;
    protected $userId; 

    public function __construct($fechaInicio = null, $fechaFin = null, $delimiter = ';', $userId = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->delimiter = $delimiter;
        $this->userId = $userId;
    }

    public function query()
    {
        return Transaction::query()
            ->with(['usuarioEmisor:id,name', 'usuarioReceptor:id,name'])
            ->when($this->fechaInicio, function ($query, $fecha) {
                return $query->where('fecha_transaccion', '>=', $fecha);
            })
            ->when($this->fechaFin, function ($query, $fecha) {
                return $query->where('fecha_transaccion', '<=', $fecha);
            })
            ->when($this->userId, function ($query, $userId) {
                return $query->where(function ($q) use ($userId) {
                    $q->where('usuario_emisor_id', $userId)
                    ->orWhere('usuario_receptor_id', $userId);
                });
            })
            ->orderBy('fecha_transaccion', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario Emisor',
            'Usuario Receptor',
            'Monto',
            'Descripción',
            'Estado',
            'Fecha Transacción',
            'Fecha Creación',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->usuarioEmisor->name ?? 'N/A',
            $transaction->usuarioReceptor->name ?? 'N/A',
            number_format($transaction->monto, 2, '.', ''),
            $transaction->descripcion ?? '',
            ucfirst($transaction->estado),
            $transaction->fecha_transaccion->format('d/m/Y'),
            $transaction->created_at->format('d/m/Y H:i:s'),
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true,
        ];
    }
}
