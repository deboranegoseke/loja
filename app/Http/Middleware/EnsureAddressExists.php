<?php

namespace App\Http\Middleware;

use App\Models\Address;
use Closure;
use Illuminate\Http\Request;

class EnsureAddressExists
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $hasAddress = Address::where('user_id', $user->id)->exists();

        if (!$hasAddress) {
            return redirect()->route('enderecos.create')
                ->with('error', 'Você precisa cadastrar um endereço para continuar o pagamento.');
        }

        return $next($request);
    }
}
