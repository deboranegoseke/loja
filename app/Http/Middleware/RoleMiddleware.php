<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        abort_unless($user, 403, 'Não autenticado.');

        $allowed = collect($roles)->flatMap(fn($r) => explode('|', $r))->map('trim')->filter();
        abort_unless($allowed->contains($user->role), 403, 'Acesso negado.');

        return $next($request);
    }
}
