<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Processa o login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // GERENTE / ADM: podem ir para a URL "pretendida" (ex.: dashboard)
        if ($user && ($user->hasRole('gerente') || $user->hasRole('adm'))) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // CLIENTE: ignora a URL "pretendida" e vai SEMPRE para a vitrine
        $request->session()->forget('url.intended'); // garante que não vai reaproveitar /dashboard
        return redirect('/'); // resources/views/welcome.blade.php
    }

    /**
     * Faz logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
