<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

    /**
    * Modelo User.
    *
    * @property string $name
    * @property string $email
    * @property string $password
    * @property float $saldo_inicial
    * @property float $saldo_actual
    * 
    * Relaciones:
    * @property \Illuminate\Database\Eloquent\Collection $transaccionesEnviadas
    * @property \Illuminate\Database\Eloquent\Collection $transaccionesRecibidas
    */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'saldo_inicial',
        'saldo_actual',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'saldo_inicial' => 'decimal:2',
            'saldo_actual' => 'decimal:2',
        ];
    }

    public function transaccionesEnviadas(): HasMany
    {
        return $this->hasMany(Transaction::class, 'usuario_emisor_id');
    }

    public function transaccionesRecibidas(): HasMany
    {
        return $this->hasMany(Transaction::class, 'usuario_receptor_id');
    }

    public function puedeTransferir(float $monto): bool
    {
        return $this->saldo_actual >= $monto;
    }

    public function montoTransferidoHoy(): float
    {
        return $this->transaccionesEnviadas()
            ->where('fecha_transaccion', today())
            ->where('estado', 'completada')
            ->sum('monto');
    }

    public function puedeTransferirHoy(float $monto): bool
    {
        $limiteDiario = 5000;
        $montoHoy = $this->montoTransferidoHoy();
        return ($montoHoy + $monto) <= $limiteDiario;
    }
}
