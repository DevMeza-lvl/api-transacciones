<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }
    private function authorizeAdmin()
    {
        if(auth()->user()->role !== 'admin') {
            abort(403, 'Forbidden');
        }
    }


    /**
     * Controlador de usuarios.
     *
     * @group Usuarios
     */

    /**
     * Registra un nuevo usuario.
     *
     * Este endpoint es público para nuevos registros.
     *
     * @bodyParam name string required Nombre completo del usuario.
     * @bodyParam email string required Correo único válido.
     * @bodyParam saldo_inicial number required Saldo inicial (>= 0).
     * @bodyParam password string required Contraseña (mínimo 6 caracteres).
     *
     * @response 201 {
     *   "message": "Usuario registrado exitosamente",
     *   "user": {...}
     * }
     * @response 422 {"errors": {...}}
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['role'] = 'user';
        $data['saldo_actual'] = $data['saldo_inicial'];

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => bcrypt($request->input('password')),
            'saldo_inicial' => $data['saldo_inicial'],
            'saldo_actual'  => $data['saldo_actual'],
            'role'          => $data['role'],
        ]);

        if (!$user) {
            throw ValidationException::withMessages([
                'error' => ['Error al crear el usuario. Inténtelo de nuevo.'],
            ]);
        }

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user'    => $user,
        ], 201);
    }

    /**
     * Lista usuarios. Solo administradores.
     *
     * @authenticated
     * @queryParam search string Opcional, busca en nombre o correo.
     * @queryParam per_page int Cantidad por página. Default 15.
     *
     * @response 200 {
     *   "data": [...],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $users = User::query()
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate($request->per_page ?? 15);

        return response()->json($users);
    }

    /**
     * Muestra detalle de usuario. Solo administradores.
     *
     * @authenticated
     * @get /api/users/{id}
     * @urlParam id int required ID de usuario. Example: 1
     *
     * @response 200 {...}
     */
    public function show(User $user): JsonResponse
    {
        $this->authorizeAdmin();

        return response()->json($user);
    }

    /**
     * Actualiza un usuario. Solo administradores.
     *
     * @authenticated
     * @put /api/users/{id}
     * @urlParam id int required ID de usuario. Example: 1
     *
     * @bodyParam name string optional Nuevo nombre del usuario. Max 255 caracteres. Example: Juan Pérez
     * @bodyParam email string optional Nuevo correo electrónico. Único (exceptuando el actual). Example: juan.perez@example.com
     * @bodyParam saldo_inicial number optional Nuevo saldo inicial. Debe ser no negativo. Example: 1500.50
     * @bodyParam password string optional Nueva contraseña. Mínimo 6 caracteres. Example: NuevaClaveSegura123
     *
     * @response 200 {
     *   "message": "Usuario actualizado exitosamente",
     *   "user": {...}
     * }
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();

        // Si viene nueva contraseña, encripta antes de actualizar
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user'    => $user,
        ]);
    }


    /**
     * Elimina un usuario sin transacciones pendientes. Solo administradores.
     *
     * @authenticated
     * @delete /api/users/{id}
     * @urlParam id int required ID de usuario. Example: 1
     *
     * @response 200 { "message": "Usuario eliminado exitosamente" }
     * @response 422 { "message": "No se puede eliminar usuario con transacciones pendientes" }
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorizeAdmin();

        if (
            $user->transaccionesEnviadas()->where('estado', 'pendiente')->exists() ||
            $user->transaccionesRecibidas()->where('estado', 'pendiente')->exists()
        ) {
            return response()->json([
                'message' => 'No se puede eliminar usuario con transacciones pendientes',
            ], 422);
        }

        try {
            $user->delete();
            return response()->json([
                'message' => 'Usuario eliminado exitosamente',
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                return response()->json([
                    'message' => 'No se puede eliminar el usuario porque tiene transacciones históricas asociadas.',
                ], 422);
            }
            throw $e;
        }
    }
}