<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){
        $validated = $request->validated();
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado',
                'bearer' => $token,
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'message' => 'Credenciais inválidas. Verifique seu e-mail e senha e tente novamente.'
        ], 401);
    }
}
