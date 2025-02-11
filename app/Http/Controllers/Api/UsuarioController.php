<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuarios;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class UsuarioController extends Controller
{
    public function index()
    {
        $users = Usuarios::all();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No hay usuarios registrados',
                'status' => 200
            ]);
        }

        return response()->json([
            'message' => 'Usuarios encontrados',
            'status' => 200,
            'data' => $users
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => 'required|email|unique:usuarios,correo', // Minúsculas en la tabla
            'telefono' => 'required|digits_between:7,15', // Validación correcta
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $user = Usuarios::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password) // Encriptar la contraseña
        ]);

        return response()->json([
            'Usuarios' => $user,
            'status' => 201
        ], 201);
    }

    public function show($id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'usuario' => $usuario,
            'status' => 200
        ], 200);
    }

    public function delete($id) // Cambio de nombre
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado',
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => "required|email|unique:usuarios,correo,{$id}",
            'telefono' => 'required|digits_between:7,15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $usuario->update($request->only(['nombre', 'apellido', 'correo', 'telefono']));

        return response()->json([
            'message' => 'Usuario modificado exitosamente',
            'status' => 200,
            'usuario' => $usuario
        ], 200);
    }

    public function login(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|min:6' // Longitud mínima de la contraseña
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        // Buscar el usuario por correo
        $usuario = Usuarios::where('correo', $request->correo)->first();
    
        if (!$usuario) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'status' => 401,
                'info' => [
                    'correo_enviado' => $request->correo,
                    'verificacion_password' => 'Usuario no encontrado'
                ]
            ], 401);
        }
    
        // Verificar la contraseña
        if (!Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'status' => 401,
                'info' => [
                    'correo_enviado' => $request->correo,
                    'verificacion_password' => 'Incorrecta'
                ]
            ], 401);
        }
    
        // Generar el token de autenticación
        $token = $usuario->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'status' => 200,
            'usuario' => $usuario,
            'token' => $token,
            'info' => [
                'verificacion_password' => 'Correcta'
            ]
        ], 200);
    }
    
    public function logout(Request $request)
{
    // Revocar el token actual del usuario
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Sesión cerrada exitosamente',
        'status' => 200
    ], 200);
}
    
}
