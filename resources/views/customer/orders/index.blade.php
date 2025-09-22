<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Meus pedidos</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500 hidden sm:block">
                    resources/views/customer/orders/index.blade.php
                </h6>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    @if ($orders->isEmpty())
                        <p class="text-gray-600">Você ainda não possui pedidos.</p>
                        <a href="{{ url('/') }}"
                           class="mt-3 inline-flex items-center rounded-md bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                            Ir para a vitrine
                        </a>
                    @else
                        {{-- LISTA MOBILE (CARDS) — visível apenas em < sm --}}
                        <div class="space-y-3 sm:hidden">
                            @foreach ($orders as $o)
                                @php
                                    $badge = match($o->status) {
                                        'pending'   => 'bg-yellow-100 text-yellow-800',
                                        'paid'      => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default     => 'bg-gray-100 text-gray-800'
                                    };
                                    $ticket = $o->tickets->first();
                                @endphp
                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">Pedido #{{ $o->id }}</div>
                                            <div class="text-xs text-gray-500">{{ $o->created_at?->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                            {{ $o->status_label }}
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                        <div class="text-gray-600">Total</div>
                                        <div class="text-right font-semibold">
                                            R$ {{ number_format($o->total, 2, ',', '.') }}
                                        </div>

                                        <div class="text-gray-600">Rastreio</div>
                                        <div class="text-right">
                                            @if($o->status === 'paid')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $o->fulfillment_badge_class }}">
                                                    {{ $o->fulfillment_status_label }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-col xs:flex-row xs:items-center gap-2">
                                        <a href="{{ route('cliente.pedidos.show', $o) }}"
                                           class="w-full xs:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                                            Detalhes
                                        </a>

                                        @if ($o->status === 'pending' && Route::has('pix.show'))
                                            <a href="{{ route('pix.show', $o) }}"
                                               class="w-full xs:w-auto inline-flex justify-center rounded-md bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                                                Pagar com Pix
                                            </a>
                                        @endif

                                        @if($ticket)
                                            <a href="{{ route('cliente.sac.show', $ticket) }}"
                                               class="w-full xs:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                                                Ver SAC
                                            </a>
                                        @else
                                            <a href="{{ route('cliente.sac.create', $o) }}"
                                               class="w-full xs:w-auto inline-flex justify-center rounded-md bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                                                Abrir SAC
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="pt-2">
                                {{ $orders->withQueryString()->links() }}
                            </div>
                        </div>

                        {{-- TABELA DESKTOP — visível em ≥ sm --}}
                        <div class="hidden sm:block">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm whitespace-nowrap">
                                    <thead class="text-left text-gray-500 border-b">
                                        <tr>
                                            <th class="py-2 pr-4">Pedido</th>
                                            <th class="py-2 pr-4">Data</th>
                                            <th class="py-2 pr-4">Status</th>
                                            <th class="py-2 pr-4">Rastreio</th>
                                            <th class="py-2 pr-4">Total</th>
                                            <th class="py-2 pr-4">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach ($orders as $o)
                                            @php
                                                $badge = match($o->status) {
                                                    'pending'   => 'bg-yellow-100 text-yellow-800',
                                                    'paid'      => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    default     => 'bg-gray-100 text-gray-800'
                                                };
                                                $ticket = $o->tickets->first();
                                            @endphp
                                            <tr>
                                                <td class="py-3 pr-4 font-medium">#{{ $o->id }}</td>
                                                <td class="py-3 pr-4">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                                                <td class="py-3 pr-4">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                                        {{ $o->status_label }}
                                                    </span>
                                                </td>
                                                <td class="py-3 pr-4">
                                                    @if($o->status === 'paid')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $o->fulfillment_badge_class }}">
                                                            {{ $o->fulfillment_status_label }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 pr-4 font-semibold">
                                                    R$ {{ number_format($o->total, 2, ',', '.') }}
                                                </td>
                                                <td class="py-3 pr-4">
                                                    <div class="flex flex-wrap gap-2">
                                                        <a href="{{ route('cliente.pedidos.show', $o) }}"
                                                           class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                            Detalhes
                                                        </a>

                                                        @if ($o->status === 'pending' && Route::has('pix.show'))
                                                            <a href="{{ route('pix.show', $o) }}"
                                                               class="inline-flex items-center rounded-md bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pink-700">
                                                                Pagar com Pix
                                                            </a>
                                                        @endif

                                                        @if($ticket)
                                                            <a href="{{ route('cliente.sac.show', $ticket) }}"
                                                               class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                                Ver SAC
                                                            </a>
                                                        @else
                                                            <a href="{{ route('cliente.sac.create', $o) }}"
                                                               class="inline-flex items-center rounded-md bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pink-700">
                                                                Abrir SAC
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $orders->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div> {{-- /p --}}
            </div> {{-- /card --}}
        </div>
    </div>
</x-app-layout>
