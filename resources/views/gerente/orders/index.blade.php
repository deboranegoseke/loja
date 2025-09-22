<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pedidos da loja</h2>
            <h6 class="text-xs text-gray-500">resources\views\gerente\orders\index.blade.php</h6>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                {{-- Filtros --}}
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-5">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar: #id, nome, e-mail, rastreio"
                           class="sm:col-span-2 rounded-md border-gray-300 text-sm">

                    <select name="status" class="rounded-md border-gray-300 text-sm">
                        <option value="">Pagamento (todos)</option>
                        @foreach ($paymentStatuses as $k => $v)
                            <option value="{{ $k }}" @selected($status===$k)>{{ $v }}</option>
                        @endforeach
                    </select>

                    <select name="fulfillment" class="rounded-md border-gray-300 text-sm">
                        <option value="">Rastreio (todos)</option>
                        @foreach ($fulfillmentStatuses as $k => $v)
                            <option value="{{ $k }}" @selected($fulfillment===$k)>{{ $v }}</option>
                        @endforeach
                    </select>

                    <x-primary-button class="justify-center text-sm">Filtrar</x-primary-button>
                </form>

                {{-- Tabela --}}
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
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <tr>
                                    <td class="py-3 pr-4 font-medium">#{{ $o->id }}</td>
                                    <td class="py-3 pr-4 whitespace-nowrap">{{ $o->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 pr-4">
                                        <div class="font-medium truncate max-w-[180px]">{{ $o->customer_name ?? $o->user?->name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-[180px]">{{ $o->customer_email ?? $o->user?->email }}</div>
                                    </td>
                                    <td class="py-3 pr-4 font-semibold whitespace-nowrap">R$ {{ number_format($o->total,2,',','.') }}</td>
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

                <div class="mt-6">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
