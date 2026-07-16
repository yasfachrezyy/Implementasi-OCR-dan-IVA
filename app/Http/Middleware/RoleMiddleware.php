<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) { 
            return redirect('login');
        }

        $userRole = strtolower(trim(Auth::user()->role));
        $allowedRolesRaw = explode(',', $roles);

        $allowedRoles = array_map(function($role) {
            return strtolower(trim($role));
        }, $allowedRolesRaw);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }
        
        abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
    }
}