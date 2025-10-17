<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Controlador de autenticación: manejo de sesión y usuario autenticado.
     *
     * @group Autenticación
     */

    /**
     * Inicia sesión de usuario.
     *
     * @bodyParam email string required Correo electrónico válido. Ejemplo: usuario@prueba.com
     * @bodyParam password string required Contraseña del usuario. Ejemplo: pass123
     *
     * @response 200 {
     *   "message": "Inicio de sesión exitoso!, Bienvenido User",
     *   "user": "User",
     *   "token": "token_value",
     *   "token_type": "Bearer"
     * }
     * 
     * @response 422 {
     *   "email": ["Las credenciales proporcionadas son incorrectas."]
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso!, Bienvenido ' . $user->name,
            'user' => $user->name,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Cierra sesión del usuario actual.
     *
     * @authenticated
     * 
     * @response 200 {
     *   "message": "Sesión cerrada exitosamente"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * Obtiene información básica del usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 {
     *   "Nombre": "User",
     *   "Email": "usuario@prueba.com",
     *   "Saldo": 1500.00
     * }
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            "Nombre" => $request->user()->name,
            "Email" => $request->user()->email,
            "Saldo" => $request->user()->saldo_actual,
        ]);
    }
}
