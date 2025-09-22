{{-- resources/views/customer/sac/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Suporte</h2>
                <h6 class="mt-0.5 hidden sm:block text-[10px] sm:text-xs text-gray-500">
                    resources/views/customer/sac/index.blade.php
                </h6>
            </div>
        </div>
    </x-slot>

    @php
        $q = request('q');
        $status = request('status'); // open | answered | closed | null
    @endphp

    <div class="py-4">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                {{-- Filtros --}}
                <form method="GET" class="p-4 border-b">
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Buscar: #chamado, #pedido, nome, e-mail"
                            class="sm:col-span-2 rounded-md border-gray-300 w-full"
                        />

                        <select name="status" class="rounded-md border-gray-300 w-full">
                            <option value="">Status (todos)</option>
                            <option value="open"     @selected($status === 'open')>Aberto</option>
                            <option value="answered" @selected($status === 'answered')>Respondido</option>
                            <option value="closed"   @selected($status === 'closed')>Fechado</option>
                        </select>

                        <div class="sm:col-span-1"></div>

                        <x-primary-button class="w-full sm:w-auto justify-center">Filtrar</x-primary-button>
                    </div>

                    {{-- Atalhos úteis no mobile (opcional) --}}
                    <div class="mt-3 flex flex-wrap gap-2 sm:hidden">
                        <a href="{{ route('cliente.sac.index') }}" class="text-xs rounded-full border px-3 py-1">Todos</a>
                        <a href="{{ route('cliente.sac.index', ['status' => 'open']) }}" class="text-xs rounded-full border px-3 py-1">Abertos</a>
                        <a href="{{ route('cliente.sac.index', ['status' => 'answered']) }}" class="text-xs rounded-full border px-3 py-1">Respondidos</a>
                        <a href="{{ route('cliente.sac.index', ['status' => 'closed']) }}" class="text-xs rounded-full border px-3 py-1">Fechados</a>
                    </div>
                </form>

                <div class="p-4">
                    @if ($tickets->count() === 0)
                        <p class="text-gray-600">Nenhum chamado ainda.</p>
                    @else
                        {{-- LISTA MOBILE (CARDS) — < sm --}}
                        <div class="space-y-3 sm:hidden">
                            @foreach ($tickets as $t)
                                @php
                                    $statusNorm = strtolower(trim((string) $t->status));
                                    $badge = match ($statusNorm) {
                                        'open', 'aberto'         => 'bg-green-100 text-green-800',
                                        'answered', 'respondido' => 'bg-blue-100 text-blue-800',
                                        'closed', 'fechado'      => 'bg-red-100 text-red-800',
                                        default                  => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">#{{ $t->id }}</div>
                                            <div class="text-xs text-gray-500">Pedido #{{ $t->order_id }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                            {{ $t->status_label }}
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <div class="text-sm font-medium truncate">
                                            {{ $t->user?->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Aberto em {{ $t->created_at?->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('cliente.sac.show', $t) }}"
                                           class="w-full inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                                           Detalhes
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            <div class="pt-2">
                                {{ $tickets->withQueryString()->links() }}
                            </div>
                        </div>

                        {{-- TABELA DESKTOP — ≥ sm --}}
                        <div class="hidden sm:block">
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
                                                $statusNorm = strtolower(trim((string) $t->status));
                                                $badge = match ($statusNorm) {
                                                    'open', 'aberto'         => 'bg-green-100 text-green-800',
                                                    'answered', 'respondido' => 'bg-blue-100 text-blue-800',
                                                    'closed', 'fechado'      => 'bg-red-100 text-red-800',
                                                    default                  => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <tr>
                                                <td class="py-3 pr-4 font-medium">#{{ $t->id }}</td>
                                                <td class="py-3 pr-4">#{{ $t->order_id }}</td>
                                                <td class="py-3 pr-4 max-w-[240px] truncate">{{ $t->user?->name ?? '—' }}</td>
                                                <td class="py-3 pr-4">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $badge }}">
                                                        {{ $t->status_label }}
                                                    </span>
                                                </td>
                                                <td class="py-3 pr-4 whitespace-nowrap">{{ $t->created_at?->format('d/m/Y H:i') }}</td>
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

                            <div class="mt-6">
                                {{ $tickets->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div> {{-- /p-4 --}}
            </div> {{-- /card --}}
        </div>
    </div>
</x-app-layout>
