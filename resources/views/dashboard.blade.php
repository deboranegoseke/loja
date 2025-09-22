<x-app-layout>
    @php
        // Fallback global: garante $user disponível em TODO o arquivo
        $user = $user ?? auth()->user();

        // ===== Defaults seguros para evitar "Undefined variable" =====
        $receitaHoje       = $receitaHoje       ?? 0;
        $pedidosPendentes  = $pedidosPendentes  ?? 0;
        $produtosAtivos    = $produtosAtivos    ?? 0;
        $ticketsAbertos    = $ticketsAbertos    ?? 0;
        $usuariosRecentes  = $usuariosRecentes  ?? collect();
        $ticketsRecentes   = $ticketsRecentes   ?? collect();
        $ultimosPedidos    = $ultimosPedidos    ?? collect();
        $temSAC            = $temSAC            ?? false;
        $temColunaIsActive = $temColunaIsActive ?? false;

        // Helper: checar role sem quebrar se não houver método
        $temRole = fn($role) => $user && method_exists($user,'hasRole') ? $user->hasRole($role) : false;
        $temAlgumaRole = fn(array $roles) => $user && method_exists($user,'hasRole') ? $user->hasRole($roles) : false;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <h6>resources\views\dashboard.blade.php</h6>


        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">Receita (hoje)</div>
                    <div class="mt-1 text-2xl font-semibold">
                        R$ {{ number_format($receitaHoje, 2, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">Pedidos pendentes</div>
                    <div class="mt-1 text-2xl font-semibold">
                        {{ $pedidosPendentes }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">
                        {{ $temColunaIsActive ? 'Produtos ativos' : 'Produtos (total)' }}
                    </div>
                    <div class="mt-1 text-2xl font-semibold">
                        {{ $produtosAtivos }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">Tickets abertos (SAC)</div>
                    <div class="mt-1 text-2xl font-semibold">
                        @if($temSAC) {{ $ticketsAbertos }} @else — @endif
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Coluna esquerda --}}
                <div class="lg:col-span-8 space-y-6">
                    {{-- Últimos pedidos --}}
                    <div class="bg-white rounded-lg shadow-sm border">
                        <div class="px-6 py-4 border-b">
                            <h3 class="font-semibold text-gray-800">Últimos pedidos</h3>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2 pr-4">#</th>
                                        <th class="py-2 pr-4">Cliente</th>
                                        <th class="py-2 pr-4">Total</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Criado em</th>
                                        <th class="py-2 pr-4"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($ultimosPedidos as $pedido)
                                        <tr>
                                            <td class="py-2 pr-4">{{ $pedido->id }}</td>
                                            <td class="py-2 pr-4">
                                                {{ optional($pedido->customer)->name ?? '—' }}
                                            </td>
                                            <td class="py-2 pr-4">
                                                R$ {{ number_format($pedido->total ?? 0, 2, ',', '.') }}
                                            </td>
                                            <td class="py-2 pr-4">
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs">
                                                    {{ ucfirst($pedido->status ?? 'desconhecido') }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-4">
                                                {{ optional($pedido->created_at)->format('d/m/Y H:i') ?? '—' }}
                                            </td>
                                            <td class="py-2 pr-4 text-right">
                                                @if(Route::has('gerente.pedidos.index'))
                                                    <a href="{{ route('gerente.pedidos.index') }}"
                                                       class="text-indigo-600 hover:underline">ver</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-gray-500">Sem pedidos recentes.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tickets recentes --}}
                    @if($temAlgumaRole(['adm','gerente']) && $temSAC)
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="px-6 py-4 border-b">
                                <h3 class="font-semibold text-gray-800">Tickets recentes (SAC)</h3>
                            </div>
                            <div class="p-6">
                                <ul class="divide-y">
                                    @forelse($ticketsRecentes as $tk)
                                        <li class="py-3 flex items-start justify-between gap-4">
                                            <div>
                                                <div class="font-medium text-gray-800">
                                                    #{{ $tk->id }} —
                                                    {{ \Illuminate\Support\Str::limit($tk->assunto ?? $tk->subject ?? 'Ticket', 60) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ optional($tk->cliente)->nome
                                                        ?? optional($tk->cliente)->name
                                                        ?? optional($tk->user)->name
                                                        ?? 'Cliente' }}
                                                    •
                                                    {{ ucfirst($tk->status ?? '—') }}
                                                    •
                                                    {{ optional($tk->created_at)->diffForHumans() ?? '' }}
                                                </div>
                                            </div>
                                            @if(Route::has('cliente.sac.index'))
                                                <a href="{{ route('cliente.sac.index') }}" class="text-indigo-600 hover:underline text-sm">abrir</a>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-gray-500">Sem tickets recentes.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Coluna direita --}}
                <div class="lg:col-span-4 space-y-6">
                    {{-- Usuários recentes (somente gerente) --}}
                    @if(($podeVerUsuarios ?? false) && $usuariosRecentes->count())
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="px-6 py-4 border-b">
                                <h3 class="font-semibold text-gray-800">Usuários recentes</h3>
                            </div>
                            <div class="p-6">
                                <ul class="divide-y">
                                    @foreach($usuariosRecentes as $u)
                                        <li class="py-3">
                                            <div class="font-medium text-gray-800">{{ $u->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $u->email }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if(Route::has('gerente.usuarios.index'))
                                    <div class="mt-4">
                                        <a href="{{ route('gerente.usuarios.index') }}" class="text-indigo-600 hover:underline text-sm">gerenciar usuários</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
