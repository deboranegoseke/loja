<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Meus pedidos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($orders->isEmpty())
                    <p class="text-gray-600">Você ainda não possui pedidos.</p>
                    <a href="{{ url('/') }}" class="mt-3 inline-flex items-center rounded-md bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                        Ir para a vitrine
                    </a>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
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
                                                {{ ucfirst($o->status) }}
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
                                        <td class="py-3 pr-4 font-semibold">R$ {{ number_format($o->total,2,',','.') }}</td>
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('cliente.pedidos.show', $o) }}"
                                               class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                Detalhes
                                            </a>

                                            @if ($o->status === 'pending' && Route::has('pix.show'))
                                                <a href="{{ route('pix.show', $o) }}"
                                                   class="ml-2 inline-flex items-center rounded-md bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pink-700">
                                                    Pagar com Pix
                                                </a>
                                            @endif

                                            @if($ticket)
                                                <a href="{{ route('cliente.sac.show', $ticket) }}"
                                                   class="ml-2 inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                                    Ver SAC
                                                </a>
                                            @else
                                                <a href="{{ route('cliente.sac.create', $o) }}"
                                                   class="ml-2 inline-flex items-center rounded-md bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pink-700">
                                                    Abrir SAC
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
