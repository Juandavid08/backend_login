<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuarios;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index()
    {
        $users = Usuarios::all();

        if($users->isEmpty()){
            return response()->json([
                'message' => 'No hay usuarios registrados',
                'status' => 200]);
        }

        $data = [
            'message' => 'Usuarios encontrados',
            'status' => 200,
            'data' => $users
        ];

        return response()->json($data, 200);
      
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => 'required|email|unique:Usuarios',
            'telefono' => 'required|numeric|min:1000000',
            'password' => 'required|string'
        ]);


        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        $user = Usuarios::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'password' => bcrypt($request->password)
        ]);
        
        if(!$user){
           $data = [
                'message' => 'Error al registrar el usuario',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'Usuarios' => $user,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    public function show($id){

        $usuario = Usuarios::find($id);

        if(!$usuario){
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'usuario'=> $usuario,
            'status'=> 200
        ];
        return response()->json($data, 200);
    }

    public function delete($id){    
        $usuario = Usuarios::find($id);

        if(!$usuario){
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $usuario->delete();

        $data = [
            'message' => 'Usuario eliminado',
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    public function update(Request $request, $id){ 
        $usuario = Usuarios::find($id);
        
        if(!$usuario){  
            $data = [
                'message' => 'Usuario no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
           'nombre' => 'required|max:255',
           'apellido' => 'required|max:255',
           'correo' => 'required|email|unique:Usuarios',
           'telefono' => 'required|numeric|min:1000000',
           'password' => 'required|string'
           ]);

           if($validator->fails()){ 
            $data = [
                'message'=> 'Error en la validacion de datos',
                'status'=> 400,
                'errors'=> $validator->errors()
                ];

                return response()->json($data, 400);
        
            }

            $usuario->nombre = $request->nombre;
            $usuario->apellido = $request->apellido;
            $usuario->correo = $request->correo;
            $usuario->telefono = $request->telefono;

            $usuario->save();

            $data = [ 
                'message'=> 'Usuario modificado exitosamente',
                'status'=> 200,
                'usuario'=> $usuario];

            return response()->json($data, 200);

    }
}