<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


    /**
     * Modelo Transaction.
     *
     * @property int $id
     * @property int $usuario_emisor_id
     * @property int $usuario_receptor_id
     * @property float $monto
     * @property string|null $descripcion
     * @property string $estado
     * @property \Carbon\Carbon $fecha_transaccion
     *
     * Relaciones:
     * @property User $usuarioEmisor
     * @property User $usuarioReceptor
     */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_emisor_id',
        'usuario_receptor_id',
        'monto',
        'descripcion',
        'estado',
        'fecha_transaccion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_transaccion' => 'date',
    ];

    public function usuarioEmisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_emisor_id');
    }

    public function usuarioReceptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_receptor_id');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_transaccion', $fecha);
    }

    public function scopeConUsuarios($query)
    {
        return $query->with(['usuarioEmisor:id,name', 'usuarioReceptor:id,name']);
    }
}
