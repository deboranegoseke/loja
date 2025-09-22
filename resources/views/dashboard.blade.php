{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Cards resumidos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Receita do dia (pagos) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Receita do Dia</div>
                    <div class="mt-2 text-3xl font-bold">
                        R$ {{ number_format(($receitaDia ?? 0), 2, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Somente pedidos pagos</div>
                </div>

                {{-- Receita do mês (pagos) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Receita do Mês</div>
                    <div class="mt-2 text-3xl font-bold">
                        R$ {{ number_format(($receitaMes ?? 0), 2, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Somente pedidos pagos</div>
                </div>

                {{-- Produtos Ativos (active = 0) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Produtos Ativos</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ $qtdProdutosAtivos ?? 0 }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Regra: <code>active = 0</code></div>
                </div>

                {{-- Produtos Esgotados (geral, stock ≤ 10) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Produtos Esgotados</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ $qtdProdutosEsgotados ?? 0 }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Regra: <code>stock &le; 10</code> (independente de <code>active</code>)</div>
                </div>

                {{-- SAC em Aberto --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">SAC em Aberto</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ $totalSacAberto ?? 0 }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">Regra: <code>status IN ('open','aberto')</code></div>
                </div>

                {{-- Pedidos em Separação (contador) --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5">
                    <div class="text-sm text-gray-500">Pedidos em Separação</div>
                    <div class="mt-2 text-3xl font-bold">
                        {{ $qtdPedidosSeparacao ?? (isset($pedidosSeparacao) ? $pedidosSeparacao->count() : 0) }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Regra: <code>fulfillment_status = 'separacao'</code>
                    </div>
                </div>
            </div>

            {{-- Lista: Pedidos em separação --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl">
                <div class="p-5 border-b">
                    <h3 class="text-lg font-semibold">Pedidos em Separação</h3>
                    <p class="text-sm text-gray-500">Listando pedidos com <code>fulfillment_status = 'separacao'</code>.</p>
                </div>

                <div class="p-5 overflow-x-auto">
                    @if(empty($pedidosSeparacao) || $pedidosSeparacao->isEmpty())
                        <div class="text-sm text-gray-500">Nenhum pedido em separação no momento.</div>
                    @else
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">Cliente</th>
                                    <th class="py-2 pr-4">E-mail</th>
                                    <th class="py-2 pr-4">Total</th>
                                    <th class="py-2 pr-4">Criado em</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pedidosSeparacao as $pedido)
                                    @php
                                        $id  = is_array($pedido) ? ($pedido['id'] ?? null) : ($pedido->id ?? null);
                                        $nm  = is_array($pedido) ? ($pedido['customer_name'] ?? null) : ($pedido->customer_name ?? null);
                                        $em  = is_array($pedido) ? ($pedido['customer_email'] ?? null) : ($pedido->customer_email ?? null);
                                        $ttl = is_array($pedido) ? ($pedido['total'] ?? 0) : ($pedido->total ?? 0);
                                        $dt  = is_array($pedido) ? ($pedido['created_at'] ?? null) : ($pedido->created_at ?? null);
                                    @endphp
                                    <tr>
                                        <td class="py-2 pr-4 font-medium">#{{ $id }}</td>
                                        <td class="py-2 pr-4">{{ $nm ?? '—' }}</td>
                                        <td class="py-2 pr-4">{{ $em ?? '—' }}</td>
                                        <td class="py-2 pr-4">R$ {{ number_format($ttl, 2, ',', '.') }}</td>
                                        <td class="py-2 pr-4">{{ $dt ? \Illuminate\Support\Carbon::parse($dt)->format('d/m/Y H:i') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Lista: Produtos esgotados na vitrine (active = 0 OU stock ≤ 10) --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl">
                <div class="p-5 border-b">
                    <h3 class="text-lg font-semibold">Produtos Esgotados na Vitrine</h3>

                </div>

                <div class="p-5 overflow-x-auto">
                    @if(empty($produtosEsgotados) || $produtosEsgotados->isEmpty())
                        <div class="text-sm text-green-700">Nenhum produto para alertar na vitrine. 🎉</div>
                    @else
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">SKU</th>
                                    <th class="py-2 pr-4">Nome</th>
                                    <th class="py-2 pr-4">Estoque</th>
                                    <th class="py-2 pr-4">Ativo?</th>
                                    <th class="py-2 pr-4">Motivo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($produtosEsgotados as $p)
                                    @php
                                        $id     = is_array($p) ? ($p['id'] ?? null)     : ($p->id ?? null);
                                        $sku    = is_array($p) ? ($p['sku'] ?? null)    : ($p->sku ?? null);
                                        $name   = is_array($p) ? ($p['name'] ?? null)   : ($p->name ?? null);
                                        $stock  = is_array($p) ? ($p['stock'] ?? null)  : ($p->stock ?? null);
                                        $active = is_array($p) ? ($p['active'] ?? null) : ($p->active ?? null);
                                        $motivo = is_array($p) ? ($p['motivo'] ?? null) : ($p->motivo ?? null);
                                    @endphp
                                    <tr>
                                        <td class="py-2 pr-4 font-medium">{{ $id }}</td>
                                        <td class="py-2 pr-4">{{ $sku ?? '—' }}</td>
                                        <td class="py-2 pr-4">{{ $name }}</td>
                                        <td class="py-2 pr-4">{{ $stock }}</td>
                                        <td class="py-2 pr-4">
                                            @if(!is_null($active))
                                                {{ (int)$active === 1 ? 'Sim' : 'Não' }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4">{{ $motivo ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
