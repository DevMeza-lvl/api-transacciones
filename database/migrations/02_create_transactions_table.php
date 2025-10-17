<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration para crear la tabla `transactions`.
 * Registra las transacciones entre usuarios.
 *
 * Campos importantes:
 * - usuario_emisor_id: usuario que envia la transacción.
 * - usuario_receptor_id: usuario receptor.
 * - monto: monto de la transacción (decimal > 0).
 * - descripcion: descripción opcional.
 * - estado: estado de la transacción (pendiente, completada, rechazada).
 * - fecha_transaccion: fecha en que se realiza.
 *
 * Añade índices para optimizar consultas frecuentes.
 * Restringe monto positivo y usuarios diferentes.
 */

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_emisor_id')->constrained('users');
            $table->foreignId('usuario_receptor_id')->constrained('users');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'rechazada'])->default('pendiente');
            $table->date('fecha_transaccion');
            $table->timestamps();
            $table->unique(['usuario_emisor_id', 'usuario_receptor_id', 'monto', 'descripcion', 'fecha_transaccion'], 'unique_transaction');

            $table->index(['usuario_emisor_id', 'fecha_transaccion']);
            $table->index(['usuario_receptor_id', 'fecha_transaccion']);
            $table->index('estado');
        });

        Schema::table('transactions', function (Blueprint $table) {
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT check_monto_positive CHECK (monto > 0)');
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT check_different_users CHECK (usuario_emisor_id != usuario_receptor_id)');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
