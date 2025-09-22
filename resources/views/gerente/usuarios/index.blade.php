<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestão de Usuários</h2>
            <p class="text-xs text-gray-500">resources/views/gerente/usuarios/index.blade.php</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
                <div class="p-4">
                    <form method="GET" action="{{ route('gerente.usuarios.index') }}" class="grid gap-3 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input type="text" name="q" value="{{ $filters['q'] }}"
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Nome ou e-mail">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Papel</label>
                            <select name="role"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="cliente" @selected($filters['role']==='cliente')>Cliente</option>
                                <option value="adm" @selected($filters['role']==='adm')>Administrador</option>
                                <option value="gerente" @selected($filters['role']==='gerente')>Gerente</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1 flex items-end">
                            <button class="inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                🔍 Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabela --}}
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
                        @php
                            $labels = ['cliente'=>'Cliente','adm'=>'Administrador','gerente'=>'Gerente'];
                            $badges = [
                                'cliente' => 'bg-gray-100 text-gray-700',
                                'adm' => 'bg-amber-50 text-amber-700',
                                'gerente' => 'bg-indigo-50 text-indigo-700'
                            ];
                        @endphp

                        @forelse ($users as $u)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500">#{{ $u->id }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $u->email }}</td>

                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('gerente.usuarios.update', $u) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <select name="role"
                                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                @if(auth()->id() === $u->id) onchange="this.value='gerente'" @endif>
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

                                <td class="px-4 py-3 text-right">
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
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
