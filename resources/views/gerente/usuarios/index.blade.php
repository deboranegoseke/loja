{{-- resources/views/gerente/usuarios/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Gestão de Usuários</h2>
                <p class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources/views/gerente/usuarios/index.blade.php</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8">

            {{-- Alertas --}}
            @if (session('status'))
                <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    ✅ <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    ⚠️ <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 sm:p-5">
                    <form method="GET" action="{{ route('gerente.usuarios.index') }}" class="grid gap-3 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="q" class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input
                                id="q"
                                type="text"
                                name="q"
                                value="{{ $filters['q'] }}"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Nome ou e-mail"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label for="role" class="block text-sm font-medium text-gray-700">Papel</label>
                            <select
                                id="role"
                                name="role"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Todos</option>
                                <option value="cliente" @selected($filters['role']==='cliente')>Cliente</option>
                                <option value="adm" @selected($filters['role']==='adm')>Administrador</option>
                                <option value="gerente" @selected($filters['role']==='gerente')>Gerente</option>
                            </select>
                        </div>

                        <div class="sm:col-span-1 flex items-end">
                            <button
                                class="inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                🔍 Filtrar
                            </button>
                        </div>

                        {{-- Atalhos no mobile (opcional) --}}
                        <div class="col-span-full sm:hidden">
                            <div class="mt-1 flex flex-wrap gap-2">
                                <a href="{{ route('gerente.usuarios.index') }}" class="text-xs rounded-full border px-3 py-1">Todos</a>
                                <a href="{{ route('gerente.usuarios.index', ['role' => 'cliente']) }}" class="text-xs rounded-full border px-3 py-1">Clientes</a>
                                <a href="{{ route('gerente.usuarios.index', ['role' => 'adm']) }}" class="text-xs rounded-full border px-3 py-1">Administradores</a>
                                <a href="{{ route('gerente.usuarios.index', ['role' => 'gerente']) }}" class="text-xs rounded-full border px-3 py-1">Gerentes</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $labels = ['cliente'=>'Cliente','adm'=>'Administrador','gerente'=>'Gerente'];
                $badges = [
                    'cliente' => 'bg-gray-100 text-gray-700',
                    'adm' => 'bg-amber-50 text-amber-700',
                    'gerente' => 'bg-indigo-50 text-indigo-700'
                ];
            @endphp

            {{-- LISTA MOBILE (CARDS) — visível < sm --}}
            <div class="sm:hidden space-y-3">
                @forelse ($users as $u)
                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $u->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $u->email }}</div>
                                <div class="mt-0.5 text-[11px] text-gray-400">#{{ $u->id }}</div>
                            </div>
                            <span class="shrink-0 text-xs rounded-full px-2 py-1 {{ $badges[$u->role] }}">
                                {{ $labels[$u->role] }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('gerente.usuarios.update', $u) }}" class="mt-3 grid grid-cols-1 xs:grid-cols-3 gap-2">
                            @csrf @method('PATCH')

                            <select
                                name="role"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                @if(auth()->id() === $u->id) onchange="this.value='gerente'" @endif
                            >
                                @foreach ($labels as $value => $label)
                                    <option value="{{ $value }}"
                                        @selected($u->role === $value)
                                        @if(auth()->id() === $u->id && $value !== 'gerente') disabled @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="xs:col-span-2 inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                💾 Salvar
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-center text-gray-500">
                        Nenhum usuário encontrado.
                    </div>
                @endforelse

                <div class="pt-2 flex justify-center">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>

            {{-- TABELA DESKTOP — visível ≥ sm --}}
            <div class="hidden sm:block">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 uppercase">Nome</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 uppercase">E-mail</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 uppercase">Papel</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($users as $u)
                                    <tr class="align-top">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 truncate max-w-[260px]">{{ $u->name }}</div>
                                            <div class="text-xs text-gray-500">#{{ $u->id }}</div>
                                        </td>

                                        <td class="px-4 py-3 text-gray-700 max-w-[280px] truncate">{{ $u->email }}</td>

                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('gerente.usuarios.update', $u) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                                                @csrf
                                                @method('PATCH')

                                                <select
                                                    name="role"
                                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    @if(auth()->id() === $u->id) onchange="this.value='gerente'" @endif
                                                >
                                                    @foreach ($labels as $value => $label)
                                                        <option value="{{ $value }}"
                                                            @selected($u->role === $value)
                                                            @if(auth()->id() === $u->id && $value !== 'gerente') disabled @endif>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                                    💾 Salvar
                                                </button>
                                            </form>
                                        </td>

                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="text-xs rounded-full px-2 py-1 {{ $badges[$u->role] }}">
                                                {{ $labels[$u->role] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                                            Nenhum usuário encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginação --}}
                    <div class="px-4 py-4 flex justify-center">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
