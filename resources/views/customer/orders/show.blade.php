{{-- resources/views/customer/orders/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                    Pedido #{{ $order->id }}
                </h2>
                <h6 class="mt-0.5 hidden sm:block text-[10px] sm:text-xs text-gray-400">
                    resources/views/customer/orders/show.blade.php
                </h6>
            </div>

            <div class="w-full sm:w-auto">
                <a href="{{ route('cliente.pedidos.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ← Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">

            {{-- RESUMO DO PEDIDO --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                @php
                    $badge = match($order->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-800',
                        'paid'      => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default     => 'bg-gray-100 text-gray-800'
                    };
                @endphp

                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div>
                            <div class="text-gray-600 text-sm">Realizado em</div>
                            <div class="font-medium">{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                        </div>

                        <div>
                            <div class="text-gray-600 text-sm">Status</div>
                            <span class="inline-flex items-center mt-1 px-2 py-1 rounded-full text-xs {{ $badge }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="sm:text-right">
                            <div class="text-gray-600 text-sm">Total</div>
                            <div class="text-2xl font-bold">R$ {{ number_format($order->total,2,',','.') }}</div>
                        </div>
                    </div>

                    @if (!empty($order->tracking_code))
                        <div class="mt-4 text-sm text-gray-700 flex flex-wrap items-center gap-2">
                            <span class="text-gray-600">Código de rastreio:</span>
                            <span class="font-medium break-all">{{ $order->tracking_code }}</span>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border px-2.5 py-1 text-xs font-medium hover:bg-gray-50"
                                onclick="(async()=>{try{await navigator.clipboard.writeText(`{{ $order->tracking_code }}`); this.innerText='Copiado!'; setTimeout(()=>this.innerText='Copiar',2000);}catch(e){alert('Não foi possível copiar.');}})()"
                            >Copiar</button>
                        </div>
                    @endif

                    @if ($order->status === 'pending' && Route::has('pix.show'))
                        <div class="mt-6">
                            <a href="{{ route('pix.show', $order) }}"
                               class="inline-flex items-center rounded-md bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                                Pagar com Pix
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ITENS --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="font-semibold mb-3">Itens</h3>

                    <div class="divide-y">
                        @foreach ($order->items as $item)
                            <div class="py-3 flex items-center gap-3">
                                <img
                                    src="{{ $item->product?->cover_url ?? 'https://picsum.photos/seed/placeholder/96/96' }}"
                                    alt=""
                                    loading="lazy" decoding="async"
                                    class="h-12 w-12 sm:h-14 sm:w-14 rounded-md object-cover bg-gray-100 ring-1 ring-gray-200"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-sm line-clamp-1">
                                        {{ $item->product?->name ?? 'Produto removido' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Qtd: {{ $item->quantity }} • Unit.: R$ {{ number_format($item->unit_price,2,',','.') }}
                                    </div>
                                </div>
                                <div class="font-semibold text-sm whitespace-nowrap">
                                    R$ {{ number_format($item->total,2,',','.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RASTREIO (sem "Rota de entrega") --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="font-semibold mb-4">Rastreio</h3>

                    @if ($order->isPaid())
                        @php
                            // Passos SEM "Rota de entrega"
                            $steps = [
                                ['key' => 'separacao',   'label' => 'Separação'],
                                ['key' => 'em_transito', 'label' => 'Em trânsito'],
                                ['key' => 'entregue',    'label' => 'Entregue'],
                            ];

                            $mapIndex = array_flip(array_column($steps, 'key'));

                            // Compatibilidade: se status vier como "rota_entrega", trate como "em_transito"
                            $statusKey = $order->fulfillment_status === 'rota_entrega'
                                ? 'em_transito'
                                : $order->fulfillment_status;

                            $current  = $mapIndex[$statusKey] ?? -1;
                        @endphp

                        <ol class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            @foreach ($steps as $i => $s)
                                @php
                                    $active = $i <= $current;
                                    $dot  = $active ? 'bg-pink-600' : 'bg-gray-300';
                                    $text = $active ? 'text-gray-900' : 'text-gray-400';
                                    $bar  = $active ? 'bg-pink-200' : 'bg-gray-200';
                                @endphp
                                <li class="flex w-full items-center gap-3 sm:flex-1">
                                    <span class="h-3 w-3 shrink-0 rounded-full {{ $dot }}"></span>
                                    <span class="text-sm {{ $text }}">{{ $s['label'] }}</span>
                                    @if(!$loop->last)
                                        <span class="hidden sm:block flex-1 h-[2px] mx-3 {{ $bar }}"></span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $order->fulfillment_badge_class }}">
                                {{ $order->fulfillment_status_label }}
                            </span>
                            @if($order->tracking_code)
                                <span class="text-sm text-gray-600">
                                    Código: <strong class="break-all">{{ $order->tracking_code }}</strong>
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-600 text-sm">Rastreio disponível após confirmação do pagamento.</p>
                    @endif
                </div>
            </div>

            {{-- AÇÕES FINAIS --}}
            <div class="flex flex-col xs:flex-row xs:items-center gap-2">
                <a href="{{ route('cliente.pedidos.index') }}"
                   class="w-full xs:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                    Voltar para meus pedidos
                </a>

                <a href="{{ url('/') }}"
                   class="w-full xs:w-auto inline-flex justify-center rounded-md border px-4 py-2 text-sm font-medium hover:bg-gray-50">
                    Ir à vitrine
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
