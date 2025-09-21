<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suporte</h2>
        <h6>resources\views\customer\sac\index.blade.php</h6>
    </x-slot>

    @php
        $q = request('q');
        $status = request('status'); // open | answered | closed | null
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Filtros (mesmo padrão das outras telas) --}}
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 mb-5">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar: #chamado, #pedido, nome, e-mail"
                        class="sm:col-span-2 rounded-md border-gray-300"
                    />

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Status (todos)</option>
                        <option value="open"     @selected($status === 'open')>Aberto</option>
                        <option value="answered" @selected($status === 'answered')>Respondido</option>
                        <option value="closed"   @selected($status === 'closed')>Fechado</option>
                    </select>

                    <div class="sm:col-span-2"></div>
                    <x-primary-button class="justify-center">Filtrar</x-primary-button>
                </form>



                @if ($tickets->count() === 0)
                    <p class="text-gray-600">Nenhum chamado ainda.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-500 border-b">
                                <tr>
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">Pedido</th>
                                    <th class="py-2 pr-4">Cliente</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Aberto em</th>
                                    <th class="py-2 pr-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($tickets as $t)
                                    @php
                                        // normaliza para comparação estrita do match
                                        $statusNorm = strtolower(trim((string) $t->status));

                                        // aceita PT e EN para não quebrar se vier "aberto/respondido/fechado"
                                        $badge = match ($statusNorm) {
                                            'open', 'aberto'         => 'bg-green-200 text-black',
                                            'answered', 'respondido' => 'bg-blue-100 text-blue-800',
                                            'closed', 'fechado'      => 'bg-red-200 text-black',
                                            default                  => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="py-3 pr-4 font-medium">#{{ $t->id }}</td>
                                        <td class="py-3 pr-4">#{{ $t->order_id }}</td>
                                        <td class="py-3 pr-4">{{ $t->user?->name ?? '—' }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                                {{ $t->status_label }}
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
