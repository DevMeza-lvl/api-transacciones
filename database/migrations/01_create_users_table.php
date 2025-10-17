<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration para crear la tabla `users`.
 * Contiene los datos de usuarios registrados en el sistema.
 *
 * Campos importantes:
 * - name: nombre del usuario (string).
 * - email: correo único para autenticación.
 * - saldo_inicial: saldo con el que inicia el usuario (decimal >=0).
 * - saldo_actual: saldo actualizado y real (decimal >=0).
 * - role: rol dentro del sistema (user o admin).
 * - password: contraseña cifrada.
 *
 * Añade restricciones CHECK para validar saldo >= 0.
 */

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->decimal('saldo_inicial', 10, 2)->default(0);
            $table->decimal('saldo_actual', 10, 2)->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users ADD CONSTRAINT check_saldo_inicial_positive CHECK (saldo_inicial >= 0)');
            DB::statement('ALTER TABLE users ADD CONSTRAINT check_saldo_actual_positive CHECK (saldo_actual >= 0)');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
