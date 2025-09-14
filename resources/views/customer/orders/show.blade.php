<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedido #{{ $order->id }}
            </h2>
            <a href="{{ route('cliente.pedidos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @php
                    $badge = match($order->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-800',
                        'paid'      => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default     => 'bg-gray-100 text-gray-800'
                    };
                @endphp

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="text-gray-600 text-sm">Realizado em</div>
                        <div class="font-medium">{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-gray-600 text-sm">Status</div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="text-right">
                        <div class="text-gray-600 text-sm">Total</div>
                        <div class="text-2xl font-bold">R$ {{ number_format($order->total,2,',','.') }}</div>
                    </div>
                </div>

                @if (!empty($order->tracking_code))
                    <div class="mt-4 text-sm text-gray-600">
                        Código de rastreio: <span class="font-medium">{{ $order->tracking_code }}</span>
                    </div>
                @endif

                @if ($order->status === 'pending')
                    <div class="mt-6">
                        <a href="{{ route('pix.show', $order) }}"
                           class="inline-flex items-center rounded-md bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                            Pagar com Pix
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Itens</h3>
                <div class="divide-y">
                    @foreach ($order->items as $item)
                        <div class="py-2 flex items-center gap-3">
                            <img
                                src="{{ $item->product?->cover_url }}"
                                alt=""
                                loading="lazy" decoding="async"
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-md object-cover bg-gray-100 ring-1 ring-gray-200"
                                onerror="this.style.display='none';"
                            >
                            <div class="flex-1">
                                <div class="font-medium text-sm line-clamp-1">
                                    {{ $item->product?->name ?? 'Produto removido' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Qtd: {{ $item->quantity }} • Unit.: R$ {{ number_format($item->unit_price,2,',','.') }}
                                </div>
                            </div>
                            <div class="font-semibold text-sm">
                                R$ {{ number_format($item->total,2,',','.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RASTREIO --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Rastreio</h3>

                @if ($order->isPaid())
                    @php
                        $steps = [
                            ['key' => 'separacao',    'label' => 'Separação'],
                            ['key' => 'em_transito',  'label' => 'Em trânsito'],
                            ['key' => 'rota_entrega', 'label' => 'Rota de entrega'],
                            ['key' => 'entregue',     'label' => 'Entregue'],
                        ];
                        $mapIndex = array_flip(array_column($steps, 'key'));
                        $current  = $mapIndex[$order->fulfillment_status] ?? -1;
                    @endphp

                    <ol class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        @foreach ($steps as $i => $s)
                            @php
                                $active = $i <= $current;
                                $dot  = $active ? 'bg-pink-600' : 'bg-gray-300';
                                $text = $active ? 'text-gray-900' : 'text-gray-400';
                            @endphp
                            <li class="flex items-center gap-3 sm:flex-1">
                                <span class="h-3 w-3 rounded-full {{ $dot }}"></span>
                                <span class="text-sm {{ $text }}">{{ $s['label'] }}</span>
                                @if(!$loop->last)
                                    <span class="hidden sm:block flex-1 h-[2px] mx-3 {{ $active ? 'bg-pink-200' : 'bg-gray-200' }}"></span>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $order->fulfillment_badge_class }}">
                            {{ $order->fulfillment_status_label }}
                        </span>
                        @if($order->tracking_code)
                            <span class="ml-3 text-sm text-gray-600">Código: <strong>{{ $order->tracking_code }}</strong></span>
                        @endif
                    </div>
                @else
                    <p class="text-gray-600 text-sm">Rastreio disponível após confirmação do pagamento.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
