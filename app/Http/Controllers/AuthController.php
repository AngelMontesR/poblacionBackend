<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class AuthController extends Controller
{

    /*
    * Genera token mediante credenciales usuario
    */
    public function login(LoginRequest $request)
    {
        try {
            $credenciales = $request->only('email', 'password');

            // Intentar autenticar al usuario
            if (Auth::attempt($credenciales)) {
                $usuario = Auth::user();
                // Crear un token con Sanctum
                $token = $usuario->createToken('token')->plainTextToken;
                // Devolver el token al usuario en la respuesta
                return response()->json([
                    'message' => 'Login exitoso',
                    'token' => $token
                ], 200);
            } else {
                return response()->json(['error' => 'Credenciales incorrectas'], 401);
            }
        } catch (\Exception $th) {
            Log::error("Error al intentar iniciar sesión: ".$th->getMessage());
            return response()->json(["error" => "Error en login"], 500);
        }
    }
}
