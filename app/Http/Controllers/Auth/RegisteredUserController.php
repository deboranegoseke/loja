<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cria o usuário como cliente
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'cliente',
        ]);

        event(new Registered($user));

        // Loga o usuário + regenera sessão (ok)
        Auth::login($user);
        $request->session()->regenerate();

        // Verifica se o usuário é staff
        $isStaff = method_exists($user, 'hasRole')
            ? ($user->hasRole('gerente') || $user->hasRole('adm'))
            : in_array($user->role, ['gerente', 'adm'], true);

        // Redireciona diretamente para rotas GET estáveis (evita intended->POST)
        return $isStaff
            ? redirect()->route('dashboard')   // GET protegido por auth/verified/role
            : redirect()->route('welcome');    // GET público
    }
}
