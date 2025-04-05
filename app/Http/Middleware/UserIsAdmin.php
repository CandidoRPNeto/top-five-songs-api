<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->role !== UserRole::ADMIN->value) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Acesso não autorizado. Apenas administradores podem acessar este recurso.',
            ], 403);
        }

        return $next($request);
    }
}
