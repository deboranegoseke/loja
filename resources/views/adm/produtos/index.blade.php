<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
            <div class="min-w-0">
                <h2 class="truncate font-semibold text-lg sm:text-xl text-gray-800 leading-tight">Produtos</h2>
                <h6 class="mt-0.5 text-[10px] sm:text-xs text-gray-500">resources\views\adm\produtos\index.blade.php</h6>
            </div>

            <div class="w-full sm:w-auto">
                <a href="{{ route('adm.produtos.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Novo produto
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $q      = request('q');
        $active = request('active'); // '1', '0' ou null
        $stock  = request('stock');  // 'in', 'out' ou null
    @endphp

    <div class="py-4">
        <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8">
            @if (session('success') || session('status'))
                <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                    {{ session('success') ?? session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                {{-- Filtros --}}
                <form method="GET" class="p-4 border-b">
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Buscar: nome, SKU"
                            class="sm:col-span-2 rounded-md border-gray-300 w-full"
                        />

                        <select name="active" class="rounded-md border-gray-300 w-full">
                            <option value="">Ativo (todos)</option>
                            <option value="1" @selected($active === '1')>Ativos</option>
                            <option value="0" @selected($active === '0')>Inativos</option>
                        </select>

                        <select name="stock" class="rounded-md border-gray-300 w-full">
                            <option value="">Estoque (todos)</option>
                            <option value="in"  @selected($stock === 'in')>Com estoque</option>
                            <option value="out" @selected($stock === 'out')>Sem estoque</option>
                        </select>

                        <x-primary-button class="w-full sm:w-auto justify-center">Filtrar</x-primary-button>
                    </div>

                    {{-- Atalhos de status em telas pequenas (opcional) --}}
                    <div class="mt-3 flex flex-wrap gap-2 sm:hidden">
                        <a href="{{ route('adm.produtos.index') }}" class="text-xs rounded-full border px-3 py-1">Todos</a>
                        <a href="{{ route('adm.produtos.index', ['active' => '1']) }}" class="text-xs rounded-full border px-3 py-1">Ativos</a>
                        <a href="{{ route('adm.produtos.index', ['active' => '0']) }}" class="text-xs rounded-full border px-3 py-1">Inativos</a>
                        <a href="{{ route('adm.produtos.index', ['stock' => 'in']) }}" class="text-xs rounded-full border px-3 py-1">Com estoque</a>
                        <a href="{{ route('adm.produtos.index', ['stock' => 'out']) }}" class="text-xs rounded-full border px-3 py-1">Sem estoque</a>
                    </div>
                </form>

                {{-- Lista MOBILE (cards) — visível apenas < sm --}}
                <div class="p-4 space-y-3 sm:hidden">
                    @forelse($products as $p)
                        <div class="rounded-lg border border-gray-200 p-3">
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $p->cover_url ?? 'https://picsum.photos/seed/placeholder/96/96' }}"
                                    alt=""
                                    class="h-16 w-16 rounded object-cover border"
                                    loading="lazy"
                                >
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $p->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">SKU: {{ $p->sku ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <div class="text-gray-500">Preço</div>
                                    <div class="font-medium">R$ {{ number_format($p->price, 2, ',', '.') }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Estoque</div>
                                    <div class="font-medium">{{ $p->stock }}</div>
                                </div>
                                <div class="text-right">
                                    @if($p->active)
                                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 px-2 py-0.5">ativo</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-2 py-0.5">inativo</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 flex flex-col xs:flex-row gap-2">
                                <a href="{{ route('adm.produtos.edit', $p) }}"
                                   class="w-full xs:w-auto inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                                    Editar
                                </a>

                                <form action="{{ route('adm.produtos.destroy', $p) }}" method="POST" class="w-full xs:w-auto">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-full inline-flex justify-center rounded-md border px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                                        onclick="return confirm('Remover este produto?')"
                                    >
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-10">Nenhum produto.</div>
                    @endforelse
                </div>

                {{-- Tabela DESKTOP — visível apenas ≥ sm --}}
                <div class="p-0 overflow-x-auto hidden sm:block">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ativo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($products as $p)
                            <tr class="align-top">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $p->cover_url ?? 'https://picsum.photos/seed/placeholder/96/96' }}" class="h-12 w-12 rounded object-cover border" alt="" loading="lazy">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ $p->sku ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">R$ {{ number_format($p->price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $p->stock }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($p->active)
                                        <span class="text-xs rounded-full bg-green-50 text-green-700 px-2 py-1">ativo</span>
                                    @else
                                        <span class="text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1">inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('adm.produtos.edit', $p) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Editar</a>
                                        <form action="{{ route('adm.produtos.destroy', $p) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Remover este produto?')">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhum produto.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
