<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">SAC</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Para abrir um chamado, acesse <a href="{{ route('cliente.pedidos.index') }}" class="text-pink-600 hover:underline">Meus pedidos</a> e clique em <b>Abrir SAC</b> no pedido desejado.
                </p>

                @if ($tickets->isEmpty())
                <p class="text-gray-600">Nenhum chamado ainda.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-500 border-b">
                            <tr>
                                <th class="py-2 pr-4">#</th>
                                <th class="py-2 pr-4">Pedido</th>
                                <th class="py-2 pr-4">Cliente</th> {{-- <== era "Assunto" --}}
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">Aberto em</th>
                                <th class="py-2 pr-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($tickets as $t)
                            @php
                            $badge = match($t->status) {
                            'open' => 'bg-yellow-100 text-yellow-800',
                            'answered' => 'bg-blue-100 text-blue-800',
                            'closed' => 'bg-gray-200 text-gray-700',
                            default => 'bg-gray-100 text-gray-800'
                            };
                            @endphp
                            <tr>
                                <td class="py-3 pr-4 font-medium">#{{ $t->id }}</td>
                                <td class="py-3 pr-4">#{{ $t->order_id }}</td>
                                <td class="py-3 pr-4">{{ $t->user?->name ?? '—' }}</td> {{-- <== era $t->subject --}}
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">{{ $t->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('cliente.sac.show', $t) }}"
                                        class="inline-flex items-center rounded-md border px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mt-6">{{ $tickets->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>