<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\UserRole;
use App\Exceptions\InvalidCredentialsException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) throw new InvalidCredentialsException();
        $user = auth()->user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'is_admin' => $user->role === UserRole::ADMIN->value
        ]);
    }

    public function register(RegisterRequest $request)
    {
        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ]);
        $token = auth()->attempt($request->only(['email', 'password']));
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'is_admin' => false
        ]);
    }
}
