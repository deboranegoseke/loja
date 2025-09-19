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
    // app/Http/Controllers/Auth/RegisteredUserController.php

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cria como cliente por padrão (tua migration já define 'cliente' como default, mas deixo explícito)
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'cliente',
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        // Se for staff (gerente/adm) vai pro dashboard; senão vai pra vitrine
        $isStaff = method_exists($user, 'hasRole')
            ? ($user->hasRole('gerente') || $user->hasRole('adm'))
            : in_array($user->role, ['gerente', 'adm'], true);

        return $isStaff
            ? redirect()->intended(route('dashboard'))
            : redirect()->intended(url('/'));
    }
}
