{{-- resources/views/gerente/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Pedidos da loja</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources/views/gerente/orders/index.blade.php</h6>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    {{-- Filtros --}}
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-5">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Buscar: #id, nome, e-mail, rastreio"
                            class="sm:col-span-2 rounded-md border-gray-300 text-sm w-full"
                        >

                        <select name="status" class="rounded-md border-gray-300 text-sm w-full">
                            <option value="">Pagamento (todos)</option>
                            @foreach ($paymentStatuses as $k => $v)
                                <option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>
                            @endforeach
                        </select>

                        <select name="fulfillment" class="rounded-md border-gray-300 text-sm w-full">
                            <option value="">Rastreio (todos)</option>
                            @foreach ($fulfillmentStatuses as $k => $v)
                                <option value="{{ $k }}" @selected($fulfillment === $k)>{{ $v }}</option>
                            @endforeach
                        </select>

                        <x-primary-button class="w-full sm:w-auto justify-center text-sm">Filtrar</x-primary-button>

                        {{-- Atalhos rápidos no mobile (opcional) --}}
                        <div class="sm:hidden col-span-1">
                            <div class="mt-1 flex flex-wrap gap-2">
                                <a href="{{ route('gerente.pedidos.index') }}" class="text-xs rounded-full border px-3 py-1">Todos</a>
                                <a href="{{ route('gerente.pedidos.index', ['status' => 'pending']) }}" class="text-xs rounded-full border px-3 py-1">Pendentes</a>
                                <a href="{{ route('gerente.pedidos.index', ['status' => 'paid']) }}" class="text-xs rounded-full border px-3 py-1">Pagos</a>
                                <a href="{{ route('gerente.pedidos.index', ['status' => 'cancelled']) }}" class="text-xs rounded-full border px-3 py-1">Cancelados</a>
                            </div>
                        </div>
                    </form>

                    {{-- LISTA MOBILE (CARDS) — visível < sm --}}
                    <div class="space-y-3 sm:hidden">
                        @forelse ($orders as $o)
                            @php
                                $payBadge = match($o->status) {
                                    'pending'   => 'bg-yellow-100 text-yellow-800',
                                    'paid'      => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default     => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">#{{ $o->id }}</div>
                                        <div class="text-xs text-gray-500">{{ $o->created_at?->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $payBadge }}">
                                        {{ $o->status_label }}
                                    </span>
                                </div>

                                <div class="mt-2 text-sm">
                                    <div class="font-medium truncate">
                                        {{ $o->customer_name ?? $o->user?->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $o->customer_email ?? $o->user?->email }}
                                    </div>
                                </div>

                                <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                    <div class="text-gray-600">Total</div>
                                    <div class="text-right font-semibold">
                                        R$ {{ number_format($o->total, 2, ',', '.') }}
                                    </div>

                                    <div class="text-gray-600">Rastreio</div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] {{ $o->fulfillment_badge_class }}">
                                            {{ $o->fulfillment_status_label }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Atualização rápida --}}
                                <form method="POST" action="{{ route('gerente.pedidos.update', $o) }}" class="mt-3 grid grid-cols-1 xs:grid-cols-3 gap-2">
                                    @csrf @method('PATCH')

                                    <select name="fulfillment_status" class="rounded-md border-gray-300 text-xs">
                                        @foreach ($fulfillmentStatuses as $k => $v)
                                            <option value="{{ $k }}" @selected($o->fulfillment_status === $k)>{{ $v }}</option>
                                        @endforeach
                                    </select>

                                    <x-primary-button class="xs:col-span-2 text-xs px-3 py-2 w-full justify-center">
                                        Salvar
                                    </x-primary-button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">Nenhum pedido encontrado.</div>
                        @endforelse

                        <div class="pt-2">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    </div>

                    {{-- TABELA DESKTOP — visível ≥ sm --}}
                    <div class="hidden sm:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-500 border-b">
                                    <tr>
                                        <th class="py-2 pr-4">#</th>
                                        <th class="py-2 pr-4">Data</th>
                                        <th class="py-2 pr-4">Cliente</th>
                                        <th class="py-2 pr-4">Total</th>
                                        <th class="py-2 pr-4">Pagamento</th>
                                        <th class="py-2 pr-4">Rastreio</th>
                                        <th class="py-2 pr-4 w-52 sm:w-64">Atualizar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse ($orders as $o)
                                        @php
                                            $payBadge = match($o->status) {
                                                'pending'   => 'bg-yellow-100 text-yellow-800',
                                                'paid'      => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default     => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <tr class="align-top">
                                            <td class="py-3 pr-4 font-medium">#{{ $o->id }}</td>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                                            <td class="py-3 pr-4">
                                                <div class="font-medium truncate max-w-[220px]">{{ $o->customer_name ?? $o->user?->name }}</div>
                                                <div class="text-xs text-gray-500 truncate max-w-[220px]">{{ $o->customer_email ?? $o->user?->email }}</div>
                                            </td>
                                            <td class="py-3 pr-4 font-semibold whitespace-nowrap">
                                                R$ {{ number_format($o->total,2,',','.') }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $payBadge }}">
                                                    {{ $o->status_label }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $o->fulfillment_badge_class }}">
                                                    {{ $o->fulfillment_status_label }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">
                                                <form method="POST" action="{{ route('gerente.pedidos.update', $o) }}"
                                                      class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                    @csrf @method('PATCH')

                                                    <select name="fulfillment_status" class="rounded-md border-gray-300 text-xs">
                                                        @foreach ($fulfillmentStatuses as $k => $v)
                                                            <option value="{{ $k }}" @selected($o->fulfillment_status === $k)>{{ $v }}</option>
                                                        @endforeach
                                                    </select>

                                                    <x-primary-button class="text-xs px-3 py-2 whitespace-nowrap">Salvar</x-primary-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-6 text-center text-gray-500">Nenhum pedido encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    </div>
                </div> {{-- /p --}}
            </div> {{-- /card --}}
        </div>
    </div>
</x-app-layout>
