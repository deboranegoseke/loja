<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()
            ->when($request->filled('q'), function ($qq) use ($request) {
                $k = (string) $request->get('q', '');
                $qq->where(function ($w) use ($k) {
                    $w->where('name', 'like', "%{$k}%")
                      ->orWhere('email', 'like', "%{$k}%");
                });
            })
            ->when($request->filled('role') && in_array($request->get('role'), [
                User::ROLE_CLIENTE, User::ROLE_ADM, User::ROLE_GERENTE
            ], true), fn($qq) => $qq->where('role', $request->get('role')))
            ->orderByDesc('id');

        $users = $q->paginate(12)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('gerente.usuarios.index', [
            'users'   => $users,
            'filters' => [
                'q'    => (string) $request->get('q', ''),
                'role' => (string) $request->get('role', ''),
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        // validação inline (equivalente ao FormRequest)
        $data = $request->validate([
            'role' => 'required|in:'.implode(',', [
                User::ROLE_CLIENTE,
                User::ROLE_ADM,
                User::ROLE_GERENTE,
            ]),
        ]);

        $auth    = $request->user();
        $newRole = $data['role'];

        // impedir auto-rebaixamento
        if ($auth->id === $user->id && $newRole !== User::ROLE_GERENTE) {
            return $this->fail($request, 'Você não pode remover seu próprio papel de gerente.');
        }

        // garantir pelo menos 1 gerente
        if ($user->role === User::ROLE_GERENTE && $newRole !== User::ROLE_GERENTE) {
            $gerentes = User::where('role', User::ROLE_GERENTE)->count();
            if ($gerentes <= 1) {
                return $this->fail($request, 'Deve existir pelo menos um gerente no sistema.');
            }
        }

        $user->update(['role' => $newRole]);

        if ($request->wantsJson()) {
            return response()->json($user->fresh());
        }

        return back()->with('status', 'Papel atualizado com sucesso!');
    }

    protected function fail(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }
        return back()->withErrors(['role' => $message])->withInput();
    }
}
