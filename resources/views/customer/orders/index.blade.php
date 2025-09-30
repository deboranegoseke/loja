{{-- resources/views/customer/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Meus pedidos
                </h2>
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
                        {{-- LISTA MOBILE (CARDS) — < sm --}}
                        <div class="space-y-3 sm:hidden">
                            @foreach ($orders as $o)
                                @php
                                    $badge = match($o->status) {
                                        'pending'   => 'bg-yellow-100 text-yellow-800',
                                        'paid'      => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default     => 'bg-gray-100 text-gray-800'
                                    };

                                    // Itens
                                    $items = $o->items ?? collect();
                                    $firstItem = $items->first();
                                    $itemName = $firstItem?->product?->name
                                        ?? $firstItem?->name
                                        ?? 'Item do pedido';
                                    $itemsCount = $o->items_count ?? $items->count();
                                    $extra = max(0, $itemsCount - 1);

                                    // Miniatura
                                    $thumb = $firstItem?->product?->thumbnail_url
                                        ?? $firstItem?->product?->image_url
                                        ?? null;

                                    // Pagamento
                                    $paymentLabel = $o->payment_method_label
                                        ?? ($o->payment_method ? strtoupper($o->payment_method) : '—');
                                @endphp

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-md bg-gray-100 ring-1 ring-gray-200 overflow-hidden flex items-center justify-center">
                                                @if($thumb)
                                                    <img src="{{ $thumb }}" alt="Produto" class="h-full w-full object-cover">
                                                @else
                                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 2v5.586l-2.293-2.293a1 1 0 00-1.414 0L9 12l-1.293-1.293a1 1 0 00-1.414 0L4 13V5h12z"/>
                                                    </svg>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <div class="truncate font-medium text-gray-900">
                                                    {{ \Illuminate\Support\Str::limit($itemName, 60) }}
                                                    @if($extra > 0)
                                                        <span class="text-gray-500">+{{ $extra }} {{ \Illuminate\Support\Str::plural('item', $extra) }}</span>
                                                    @endif
                                                </div>
                                                <div class="mt-0.5 text-xs text-gray-500 flex items-center gap-2">
                                                    <span>#{{ $o->id }}</span>
                                                    <span>•</span>
                                                    <span>{{ $o->created_at?->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                            {{ $o->status_label }}
                                        </span>
                                    </div>

                                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                        <dt class="text-gray-600">Total</dt>
                                        <dd class="text-right font-semibold">R$ {{ number_format($o->total, 2, ',', '.') }}</dd>

                                        <dt class="text-gray-600">Pagamento</dt>
                                        <dd class="text-right">{{ $paymentLabel }}</dd>

                                        <dt class="text-gray-600">Entrega</dt>
                                        <dd class="text-right">
                                            @if($o->status === 'paid')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $o->fulfillment_badge_class }}">
                                                    {{ $o->fulfillment_status_label }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </dd>
                                    </dl>

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

                                        @php $ticket = $o->tickets->first(); @endphp
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

                        {{-- TABELA DESKTOP — ≥ sm --}}
                        <div class="hidden sm:block">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left text-gray-500 border-b">
                                        <tr>
                                            <th class="py-2 pr-4">Itens</th>
                                            <th class="py-2 pr-4">Data</th>
                                            <th class="py-2 pr-4">Status</th>
                                            <th class="py-2 pr-4">Entrega</th>
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

                                                $items = $o->items ?? collect();
                                                $firstItem = $items->first();
                                                $itemName = $firstItem?->product?->name
                                                    ?? $firstItem?->name
                                                    ?? 'Item do pedido';
                                                $itemsCount = $o->items_count ?? $items->count();
                                                $extra = max(0, $itemsCount - 1);
                                                $thumb = $firstItem?->product?->thumbnail_url
                                                    ?? $firstItem?->product?->image_url
                                                    ?? null;

                                                $paymentLabel = $o->payment_method_label
                                                    ?? ($o->payment_method ? strtoupper($o->payment_method) : '—');

                                                $ticket = $o->tickets->first();
                                            @endphp
                                            <tr class="align-middle">
                                                <td class="py-3 pr-4">
                                                    <div class="flex items-center gap-3 max-w-[520px]">
                                                        <div class="h-10 w-10 flex-shrink-0 rounded-md bg-gray-100 ring-1 ring-gray-200 overflow-hidden flex items-center justify-center">
                                                            @if($thumb)
                                                                <img src="{{ $thumb }}" alt="Produto" class="h-full w-full object-cover">
                                                            @else
                                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 2v5.586l-2.293-2.293a1 1 0 00-1.414 0L9 12l-1.293-1.293a1 1 0 00-1.414 0L4 13V5h12z"/>
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="truncate text-gray-900">
                                                                {{ \Illuminate\Support\Str::limit($itemName, 60) }}
                                                                @if($extra > 0)
                                                                    <span class="text-gray-500">+{{ $extra }} {{ \Illuminate\Support\Str::plural('item', $extra) }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="mt-0.5 text-xs text-gray-500 flex items-center gap-2">
                                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5">#{{ $o->id }}</span>
                                                                <span>{{ $paymentLabel }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
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
                                                                Ver Suporte
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
