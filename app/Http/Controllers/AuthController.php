<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['status' => 'fail', 'message' => 'email ou senha incorreto(s)'], 401);
        }
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    public function register(RegisterRequest $request)
    {
        try {
            User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]);
            return response()->json(['status' => 'success', 'message' => 'usuario criado com sucesso']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail', 'message' => $th->getMessage()],500);
        }
    }
}
